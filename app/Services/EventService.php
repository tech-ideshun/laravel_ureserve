<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventService
{
    public static function checkEventDuplication($eventDate, $startTime, $endTime)
    {
        // 予約される日時が既存のイベント日時と被りがないかチェック | もし存在していればexists()でtrueが返却される
        return DB::table('events')
            ->whereDate('start_date', $eventDate)
            ->whereTime('end_date', '>', $startTime)
            ->whereTime('start_date', '<', $endTime)
            ->exists(); // 返り値：真偽値

        // return $check;   直でreturnもできる
    }

    public static function countEventDuplication($eventDate, $startTime, $endTime)
    {
        return DB::table('events')
            ->whereDate('start_date', $eventDate)
            ->whereTime('end_date', '>', $startTime)
            ->whereTime('start_date', '<', $endTime)
            ->count(); // 返り値：該当する件数

    }

    public static function joinDateAndTime($date, $time)
    {
        $join = $date . " " . $time;
        return Carbon::createFromFormat('Y-m-d H:i', $join);

        // return $dateTime;    直でreturnもできる
    }

    public static function getWeekEvents($startDate, $endDate)
    {
        $reservedPeople = DB::table('reservations')
        ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
        ->whereNull('canceled_date')
        ->groupBy('event_id');

        return DB::table('events')
        ->leftJoinSub($reservedPeople, 'reservedPeople', function ($join){
            $join->on('events.id', '=', 'reservedPeople.event_id');
        })
        ->whereBetween('start_date', [$startDate, $endDate]) // この期間だけのイベントを抽出
        ->orderBy('start_date', 'asc')
        ->get();
    }

    public static function countReservePeople($eventId)
    {
        return DB::table('reservations')
        ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
        ->whereNull('canceled_date')
        ->groupBy('event_id')
        ->having('event_id', $eventId)
        ->first();
    }
}
