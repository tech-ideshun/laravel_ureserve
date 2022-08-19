<div style="text-align: center">
    <button wire:click="increment">+</button>
    <h1>{{ $count }}</h1>

    <div class="mb-8"></div>
    こんにちは、{{ $name }}さん<br>
    <input type="text" wire:model="name"> {{-- リアルタイム更新 --}}
    {{-- <input type="text" wire:model.debounce.2000ms="name"> 指定秒数、更新を待つ--}}
    {{-- <input type="text" wire:model.lazy="name"> カーソルが外れたら--}}
    {{-- <input type="text" wire:model.defer="name"> submitされたら--}}
    <br>
    <button wire:mouseover="mouseOver">マウスを合わせてね</button>
</div>
