<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// アクセサの読み込み用
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

// 多対多
use App\Models\User;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'information',
        'max_people',
        'start_date',
        'end_date',
        'is_visible'
    ];

    // ↓アクセサの定義

    /**
     * @return Eventテーブルの開始日を年月日に加工したもの
     */
    protected function eventDate(): Attribute
    {
        return Attribute::make(
            get: fn() => Carbon::parse($this->start_date)->format('Y年m月d日'),
        );
    }

    /**
     * @return Eventテーブルの開始日を更新用にフォーマットされたもの
     */
    protected function editEventDate(): Attribute
    {
        return Attribute::make(
            get: fn() => Carbon::parse($this->start_date)->format('Y-m-d'),
        );
    }

    /**
     * @return Eventテーブルの開始時間を時分に加工したもの
     */
    protected function startTime(): Attribute
    {
        return Attribute::make(
            get: fn() => Carbon::parse($this->start_date)->format('H時i分'),
        );
    }

    protected function endTime(): Attribute
    {
        return Attribute::make(
            get: fn() => Carbon::parse($this->end_date)->format('H時i分'),
        );
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'reservations')
        ->withPivot('id', 'number_of_people', 'canceled_date');
    }
}
