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

        $this->assertEquals(50, $loan->fresh()->remainingDueAmount());
    }

    /**
     * A customer can not submit a weekly loan repayment if the loan is not approved.
     *
     * @return void
     */
    public function testACustomerCanNotSubmitALoanRepaymentIfTheLoanIsNotApproved()
    {
        $this->fail('Not implemented');
    }

    /**
     * A customer can not make a payment if the amount is less than the amount due for that week.
     *
     * @return void
     */
    public function testACustomerCanNotMakeAPaymentIfTheAmountIsLessThanTheAmountDueForThatWeek()
    {
        $this->fail('Not implemented');
    }

    /**
     * Loan terms can not be in decimal.
     *
     * @return void
     */
    public function testLoanTermsCanNotBeInDecimal()
    {
        $this->fail('Not implemented');
    }

    /**
     * When a user makes a full repayment, the loan status should be updated to paid.
     *
     * @return void
     */
    public function testWhenAUserMakesAFullRepaymentTheLoanStatusShouldBeUpdatedToPaid()
    {
        $this->fail('Not implemented');
    }

    /**
     * Duplicate repayment is not allowed for single repayment id
     *
     * @return void
     */
    public function testDuplicateRepaymentIsNotAllowedForSingleRepaymentId()
    {
        $this->fail('Not implemented');
    }

    /**
     * A user can only make repayment for only related loan id that belongs to him.
     *
     * @return void
     */
    public function testAUserCanOnlyMakeRepaymentForOnlyRelatedLoanIdThatBelongsToHim()
    {
        $this->fail('Not implemented');
    }
}
