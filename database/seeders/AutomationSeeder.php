<?php

namespace Database\Seeders;

use App\Models\Automation;
use App\Models\AutomationTask;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AutomationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Automation::factory()
        ->has(
            AutomationTask::factory()
                ->count(5)
                ->sequence(fn ($sequence) => ['sequence_id' => $sequence->index + 1])
                ->state(function(array $attributes, Automation $automation){
                    return [
                        'automation_id' => $automation->id,
                        'creator' => $automation->user_id,
                    ];
                }))
        ->create();
    }
}
