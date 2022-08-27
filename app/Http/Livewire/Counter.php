<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Counter extends Component
{
    public $count = 0;

    public $name = '';

    public function mount() // クラスの__construct()みたいな感じ
    {
        $this->name = 'mount';
    }

    public function updated()   // 更新毎
    {
        $this->name = 'updated';
    }

    public function mouseOver()
    {
        $this->name = 'mouseover';
    }

    public function increment()
    {
        $this->count++;
    }

    public function render()
    {
        return view('livewire.counter');
    }
}
