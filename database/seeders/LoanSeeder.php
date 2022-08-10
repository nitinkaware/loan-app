<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::inRandomOrder()->take(10)->get();

        $users->each(function (User $user) {
            Loan::factory()
                ->count(10)
                ->create([
                    'user_id' => $user->id,
                ]);
        });

        $loans = Loan::inRandomOrder()->take(5)->get();

        //For each users, create 5 approved loans
        $loans->each(function (Loan $loan) {
            $loan->approve();
        });
    }
}
