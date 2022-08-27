<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $availableHour = $this->faker->numberBetween(10, 18);
        $minutes = [0, 30]; // 00分か 30分
        $mKey = array_rand($minutes);   // $minutesからランダムにキーを取得
        $addHour = $this->faker->numberBetween(1, 3);

        // fakerのdateTimeThisMonthは戻り値がDateTime型なので変数に入れてformat、modifyメソッドで加工などできる
        $dummyDate = $this->faker->dateTimeThisMonth;   // 今月分をランダムに取得

        $startDate = $dummyDate->setTime($availableHour, $minutes[$mKey]);
        $clone = clone $startDate;  // そのままmodifyするとstartDateも変わるのでclone
        $endDate = $clone->modify('+' . $addHour . 'hour');
        // dd($dummyDate, $startDate, $endDate);

        return [
            'name' => $this->faker->name,
            'information' => $this->faker->realText,
            'max_people' => $this->faker->numberBetween(1, 20),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_visible' => $this->faker->boolean
        ];
    }
}
