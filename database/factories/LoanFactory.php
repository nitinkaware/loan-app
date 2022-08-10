<?php

namespace Database\Factories;

use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Loan>
 */
class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'amount_required' => $this->faker->numberBetween(100, 1000),
            'terms_in_week' => $this->faker->numberBetween(1, 10),
            'status' => Loan::PENDING,
        ];
    }
}
