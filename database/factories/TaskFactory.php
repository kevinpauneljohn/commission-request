<?php

namespace Database\Factories;

use App\Models\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->realText,
            'description' => fake()->realText,
            'assigned_to' => collect(collect(User::whereHas("roles", function($q){ $q->where('name','!=','sales director')->where("name","!=","super admin"); })->get())->pluck('id'))->random(),
            'creator' => collect(collect(User::whereHas("roles", function($q){ $q->where("name","=","super admin"); })->get())->pluck('id'))->random(),
            'status' => 'pending',
            'due_date' => now()->format('Y-m-d'),
            'request_id' => collect(Request::all()->pluck('id'))->random()
        ];
    }
}
