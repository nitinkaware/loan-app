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
        $this->forceSeedFirstUser();
        $this->seedRandomUsers();
    }

    public function forceSeedFirstUser(): void
    {
        //Force seeder first user with loans
        $user = User::first();

        $user->each(function ($user) {
            $user->loans()->saveMany(Loan::factory(10)->make(['user_id' => $user->id]));
        });

        $user->loans()->take(2)->get()->each(function (Loan $loan) {
            $loan->approve();
        });
    }

    public function seedRandomUsers(): void
    {
        //Seed random users.
        $users = User::inRandomOrder()
            ->where('id', '<>', 1)
            ->take(10)
            ->get();

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
