<?php

namespace Database\Factories;

use App\Models\Calls;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Calls>
 */
class CallsFactory extends Factory
{
    protected $model = Calls::class;

    public function definition(): array
    {
        $direction = $this->faker->randomElement(['Outbound', 'Inbound']);
        $fromName = $this->faker->name();
        $toName = $this->faker->name();
        $status = $this->faker->randomElement(['Answered', 'Unanswered', 'Scheduled']);
        $seconds = $this->faker->numberBetween(30, 3600); // 30s to 60m
        $h = str_pad((string) intdiv($seconds, 3600), 2, '0', STR_PAD_LEFT);
        $m = str_pad((string) intdiv($seconds % 3600, 60), 2, '0', STR_PAD_LEFT);
        $s = str_pad((string) ($seconds % 60), 2, '0', STR_PAD_LEFT);
        $duration = "$h:$m:$s";

        return [
            'call_date' => $this->faker->dateTimeBetween('-60 days', 'now')->format('Y-m-d H:i:s'),
            'from' => $fromName,
            'to' => $toName,
            'direction' => $direction,
            'status' => $status,
            'call_time' => $duration,
        ];
    }
}
