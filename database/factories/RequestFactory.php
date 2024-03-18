<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Request>
 */
class RequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'buyer' => [
                'firstname' => fake()->firstName,
                'middlename' => fake()->lastName,
                'lastname' => fake()->lastName,
            ],
            'project' => collect(['Alpine Residences','The Ridge','Filmore Place','Madonna Residences','Aspire Residences','Xevera Mabalacat','Mansfield Residences','Timog Residences'])->random(),
            'model_unit' => fake()->firstName,
            'phase' => rand(1,100),
            'block' => rand(1,100),
            'lot' => rand(1,100),
            'total_contract_price' => rand(1500000,5000000),
            'financing' => collect(['hdmf','bank','inhouse','deferred','cash'])->random(),
            'request_type' => collect(['commission_request','cheque_pickup'])->random(),
            'sd_rate' => rand(1,6),
            'message' => fake()->realText,
            'user_id' => collect(collect(User::whereHas("roles", function($q){ $q->where("name","=","sales director"); })->get())->pluck('id'))->random(),
            'status' => 'pending'
        ];
    }
}
