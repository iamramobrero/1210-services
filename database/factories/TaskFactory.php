<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['to-do', 'in-progress', 'completed']),
            'content' => $this->faker->text(),
            'deleted_at' => $this->faker->randomElement([null, now()]),
        ];
    }
}
