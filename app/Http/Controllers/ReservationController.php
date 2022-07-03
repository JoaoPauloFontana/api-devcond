<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\AreaDisabledDay;
use Illuminate\Http\Request;

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
}
