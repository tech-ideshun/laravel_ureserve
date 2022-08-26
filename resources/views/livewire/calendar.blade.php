<div>
    カレンダー
    <input id="calendar" class="block mt-1 w-full"
    type="text" name="calendar" value="{{ $currentDate }}" wire:change="getDate($event.target.value)" />
    <div class="flex">
        @for ($i = 0; $i < 7; $i++)
            {{ $currentWeek[$i]}}
        @endfor
    </div>
    @foreach ($events as $event)
        {{ $event->start_date }}<br>
    @endforeach
</div>
