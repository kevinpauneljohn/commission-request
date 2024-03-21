<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Automation>
 */
class AutomationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->text,
            'user_id' => collect(collect(User::whereHas("roles", function($q){ $q->where("name","=","super admin"); })->get())->pluck('id'))->random(),
            'is_active' => true
        ];
    }
}
