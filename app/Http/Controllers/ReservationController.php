<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\AreaDisabledDay;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    public function getReservations()
    {
        $array = ['error' => '', 'list' => []];
        $daysHelper = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

        $areas = Area::where('allowed', 1)->get();

        foreach ($areas as $area) {
            $dayList = explode(',', $area['days']);

            $dayGroups = [];

            $lastDay = intval(current($dayList));
            $dayGroups[] = $daysHelper[$lastDay];
            array_shift($dayList);

            foreach ($dayList as $day) {
                if(intval($day) != $lastDay + 1){
                    $dayGroups[] = $daysHelper[$lastDay];
                    $dayGroups[] = $daysHelper[$day];
                }

                $lastDay = intval($day);
            }

            $dayGroups[] = $daysHelper[end($dayList)];

            $dates = '';
            $close = 0;

            foreach ($dayGroups as $group) {
                if($close === 0){
                    $dates .= $group;
                }else{
                    $dates .= '-'.$group.',';
                }

                $close = 1 - $close;
            }

            $dates = explode(',', $dates);
            array_pop($dates);

            $start = date('H:i', strtotime($area['start_time']));
            $end = date('H:i', strtotime($area['end_time']));

            foreach ($dates as $dKey => $dValue) {
                $dates[$dKey] .= ' '.$start.' às '.$end;
            }

            $array['list'][] = [
                'id' => $area['id'],
                'cover' => asset('storage/'.$area['cover']),
                'title' => $area['title'],
                'dates' => $dates
            ];

        }

        return $array;
    }

    public function getDisabledDates($id)
    {
        $area = Area::findOrFail($id);

        $array = ['error' => '', 'list' => []];

        $disabledDays = AreaDisabledDay::where('id_area', $id)->get();

        foreach ($disabledDays as $disabledDay) {
            $array['list'][] = $disabledDay['day'];
        }

        $allowedDays = explode(',', $area['days']);
        $offDays = [];

        for ($i = 0; $i < 7; $i++) {
            if (!in_array($i, $allowedDays)) {
                $offDays[] = $i;
            }
        }

        $startDay = time();
        $endDay = strtotime('+3 months');
        $currentDay = $startDay;
        $keep = true;

        while ($keep) {
            if ($currentDay < $endDay) {
                $weekDay = date('w', $currentDay);

                if (in_array($weekDay, $offDays)) {
                    $array['list'][] = date('Y-m-d', $currentDay);
                }

                $currentDay = strtotime('+1 day', $currentDay);
            } else {
                $keep = false;
            }
        }

        return $array;
    }

    public function getTimes($id, Request $req)
    {
        $array = ['error' => '', 'list' => []];

        $validator = Validator::make($req->all(), [
            'date' => 'required|date_format:Y-m-d'
        ]);

        if (!$validator->fails()) {
            $date = $req['date'];
            $area = Area::findOrFail($id);

            $can = true;

            $existingDisabledDay = AreaDisabledDay::where('id_area', $id)
                ->where('day', $date)
                ->count();

            if ($existingDisabledDay > 0) {
                $can = false;
            }

            $allowedDays = explode(',', $area['days']);
            $weekDay = date('w', strtotime($date));

            if (!in_array($weekDay, $allowedDays)) {
                $can = false;
            }

            if ($can) {
                $startTime = strtotime($area['start_time']);
                $endTime = strtotime($area['end_time']);

                $times = [];

                for ($lastTime = $startTime; $lastTime < $endTime; $lastTime = strtotime('+1 hour', $lastTime)) {
                    $times[] = $lastTime;
                }

                $timeList = [];

                foreach ($times as $time) {
                    $timeList[] = [
                        'id' => date('H:i:s', $time),
                        'title' => date('H:i', $time).' - '.date('H:i', strtotime('+1 hour', $time)),
                    ];
                }

                $reservations = Reservation::where('id_area', $id)
                    ->whereBetween('reservation_date', [
                        $date.' 00:00:00',
                        $date.' 23:59:59'
                    ])
                    ->get();

                $toRemove = [];

                foreach ($reservations as $reservation) {
                    $time = date('H:i:s', strtotime($reservation['reservation_date']));

                    $toRemove[] = $time;
                }

                foreach ($timeList as $timeItem) {
                    if (!in_array($timeItem['id'], $toRemove)) {
                        $array['list'][] = $timeItem;
                    }
                }
            }
        } else {
            $array['error'] = $validator->errors()->first();

            return $array;
        }

        return $array;
    }
}
