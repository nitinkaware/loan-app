<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanRepaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->create();
    }

    /**
     * A user can submit a weekly loan repayment.
     *
     * @return void
     */
    public function testAUserCanSubmitALoanRepayment()
    {
        $this->actingAs($this->user);

        /** @var Loan $loan */
        $loan = $this->user->loans()->create([
            'amount_required' => '100',
            'terms_in_week' => '4',
        ]);

        $loan->approve();

        $loanRepayments = $loan->loanRepayments()->get();

        $this->postJson(route('api.loan-repayments.store', ['repayment' => $loanRepayments[0]->id]), [
            'amount_paid' => '25',
        ])->assertStatus(202);

        $this->assertEquals(75, $loan->remainingDueAmount());

        $this->postJson(route('api.loan-repayments.store', ['repayment' => $loanRepayments[1]->id]), [
            'amount_paid' => '25',
        ])->assertStatus(202);
//
        $this->assertEquals(50, $loan->fresh()->remainingDueAmount());
    }

    /**
     * Duplicate repayment is not allowed for single repayment id
     *
     * @return void
     */
    public function testDuplicateRepaymentIsNotAllowedForSingleRepaymentId()
    {
    }

    /**
     * A user can only make replayment for only related loan id that belongs to him.
     *
     * @return void
     */
    public function testAUserCanOnlyMakeRepaymentForOnlyRelatedLoanIdThatBelongsToHim()
    {
    }
}
