<?php

namespace Database\Factories;

use App\Models\Automation;
use App\Models\AutomationTask;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AutomationTask>
 */
class AutomationTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
//            'automation_id' => collect(collect(Automation::all())->first())->pluck('id')->random(),
            'title' => fake()->text,
            'description' => fake()->realText,
            'assigned_to_role' => 'business administrator',
//            'creator' => collect(collect(User::whereHas("roles", function($q){ $q->where("name","=","super admin"); })->get())->pluck('id'))->random(),
            'days_before_due_date' => rand(1,7),
//            'sequence_id' => collect(AutomationTask::all())->count() + 1
        ];

    }
}
