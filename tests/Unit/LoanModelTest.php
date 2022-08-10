<?php

namespace Tests\Unit;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A remaining amount should be a correct value.
     */
    public function testRemainingDueAmount()
    {
        $user = User::factory()->create();

        /** @var Loan $loan */
        $loan = Loan::factory()->create([
            'user_id' => $user->id,
            'amount_required' => 1000,
            'terms_in_week' => 10,
        ]);

        $this->assertEquals(1000, $loan->remainingDueAmount());

        $loan->approve();

        $loan->loanRepayments()->first()->update([
            'amount_paid' => 111.33,
        ]);

        $this->assertEquals(888.67, $loan->remainingDueAmount());
    }

    /**
     * Verify that isFullyPaid method returns true when the loan is fully paid.
     */
    public function testIsFullyPaid()
    {
        $user = User::factory()->create();

        /** @var Loan $loan */
        $loan = Loan::factory()->create([
            'user_id' => $user->id,
            'amount_required' => 1000,
            'terms_in_week' => 10,
        ]);

        $loan->approve();

        $loanRepayments = $loan->loanRepayments()->get();

        $loanRepayments[0]->update([
            'amount_paid' => 500,
        ]);

        $loanRepayments[1]->update([
            'amount_paid' => 400,
        ]);

        $loanRepayments[3]->update([
            'amount_paid' => 100,
        ]);

        $this->assertTrue($loan->isFullyPaid());
    }
}
