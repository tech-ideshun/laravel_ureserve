<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\EventService;

class EventController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $reservedPeople = DB::table('reservations')
        ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
        ->whereNull('canceled_date')
        ->groupBy('event_id');

        // dd($reservedPeople);

        $events = DB::table('events')
        ->leftJoinSub($reservedPeople, 'reservedPeople', function ($join){
            $join->on('events.id', '=', 'reservedPeople.event_id');
        })
        ->whereDate('start_date', '>=', $today) // 今日より以前の日程は表示させない
        ->orderBy('start_date', 'asc')
        ->paginate(10);
        // ->get();
        // dd($events);

        return view('manager.events.index', compact('events'));
    }

    public function create()
    {
        return view('manager.events.create');
    }

    public function store(StoreEventRequest $request)
    {
        // // 予約される日時が既存のイベント日時と被りがないかチェック | もし存在していればexists()でtrueが返却される
        // $check = DB::table('events')
        // ->whereDate('start_date', $request['event_date'])
        // ->whereTime('end_date', '>', $request['start_time'])
        // ->whereTime('start_date', '<', $request['end_time'])
        // ->exists(); // 返り値：真偽値

        $check = EventService::checkEventDuplication($request['event_date'], $request['start_time'], $request['end_time']);

        // dd($check);
        if($check) {
            session()->flash('status', 'この時間帯は既に他の予約が存在します。');
            return view('manager.events.create');
        }

        // $start = $request['event_date'] . " " . $request['start_time'];
        // $start_date = Carbon::createFromFormat('Y-m-d H:i', $start);

        $start_date = EventService::joinDateAndTime($request['event_date'], $request['start_time']);

        // $end = $request['event_date'] . " " . $request['end_time'];
        // $end_date = Carbon::createFromFormat('Y-m-d H:i', $end);

        $end_date = EventService::joinDateAndTime($request['event_date'], $request['end_time']);

        Event::create([
            'name' => $request['event_name'],
            'information' => $request['information'],
            'start_date' => $start_date,
            'end_date' => $end_date,
            'max_people' => $request['max_people'],
            'is_visible' => $request['is_visible']
        ]);

        session()->flash('status', '登録okです');

        return to_route('events.index');
    }

    public function show(Event $event)
    {
        $event = Event::findOrFail($event->id);

        $users = $event->users;

        $reservations = [];
        foreach($users as $user)
        {
            $reservedInfo = [
                'name' => $user->name,
                'number_of_people' => $user->pivot->number_of_people,
                'canceled_date' => $user->pivot->canceled_date,
            ];

            array_push($reservations, $reservedInfo);
        }
        // dd($reservations);
        // dd($event, $users);

        $eventDate = $event->eventDate;
        $startTime = $event->startTime;
        $endTime = $event->endTime;

        // dd($eventDate, $startTime, $endTime);

        return view('manager.events.show', compact('event', 'users', 'reservations', 'eventDate', 'startTime', 'endTime'));
    }

    public function edit(Event $event)
    {
        $event = Event::findOrFail($event->id);
        $eventDate = $event->editEventDate;
        $startTime = $event->startTime;
        $endTime = $event->endTime;

        return view('manager.events.edit', compact('event', 'eventDate', 'startTime', 'endTime'));
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        $check = EventService::countEventDuplication($request['event_date'], $request['start_time'], $request['end_time']);

        // 重複するEventが2個以上 = 更新するもの以外で被りあり
        if($check > 1) {
            $event = Event::findOrFail($event->id);
            $eventDate = $event->editEventDate;
            $startTime = $event->startTime;
            $endTime = $event->endTime;

            session()->flash('status', 'この時間帯は既に他の予約が存在します。');
            return view('manager.events.edit', compact('event', 'eventDate', 'startTime', 'endTime'));
        }

        $start_date = EventService::joinDateAndTime($request['event_date'], $request['start_time']);

        $end_date = EventService::joinDateAndTime($request['event_date'], $request['end_time']);

        $event = Event::findOrFail($event->id);

        $event->name = $request['event_name'];
        $event->information = $request['information'];
        $event->start_date = $start_date;
        $event->end_date = $end_date;
        $event->max_people = $request['max_people'];
        $event->is_visible = $request['is_visible'];
        $event->save();

        session()->flash('status', '更新しました。');

        return to_route('events.index');
    }

    public function past()
    {
        $today = Carbon::today();

        $reservedPeople = DB::table('reservations')
        ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
        ->whereNull('canceled_date')
        ->groupBy('event_id');


        $events = DB::table('events')
        ->leftJoinSub($reservedPeople, 'reservedPeople', function ($join){
            $join->on('events.id', '=', 'reservedPeople.event_id');
        })
        ->whereDate('start_date', '<', $today)
        ->orderBy('start_date', 'desc')
        ->paginate(10);

        return view('manager.events.past', compact('events'));
    }

    public function destroy(Event $event)
    {
        //
    }
}
