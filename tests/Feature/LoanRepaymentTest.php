<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\LoanRepayment;
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
        $this->actingAs($this->user);

        $loan = $this->user->loans()->create([
            'amount_required' => '100',
            'terms_in_week' => '4',
        ]);

        $this->postJson(route('api.loan-repayments.store', ['repayment' => 1]), [
            'amount_paid' => '25',
        ])->assertStatus(404);
    }

    /**
     * A customer can not make a payment if the amount is less than the amount due for that week.
     *
     * @return void
     */
    public function testACustomerCanNotMakeAPaymentIfTheAmountIsLessThanTheAmountDueForThatWeek()
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
            'amount_paid' => '24',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['amount_paid' => 'Amount paid must be greater than or equal to amount payable.']);
    }

    /**
     * When a user makes a full repayment, the loan status should be updated to paid.
     *
     * @return void
     */
    public function testWhenAUserMakesAFullRepaymentTheLoanStatusShouldBeUpdatedToPaid()
    {
        $this->actingAs($this->user);

        /** @var Loan $loan */
        $loan = $this->user->loans()->create([
            'amount_required' => '100',
            'terms_in_week' => '4',
        ]);

        $loan->approve();

        $loanRepayments = $loan->loanRepayments()->get();

        $loanRepayments->each(function (LoanRepayment $loanRepayment) {
            $this->postJson(route('api.loan-repayments.store', ['repayment' => $loanRepayment->id]), [
                'amount_paid' => $loanRepayment->amount,
            ])->assertStatus(202);
        });

        $this->assertEquals(0, $loan->remainingDueAmount());

        $this->assertEquals(Loan::PAID, $loan->fresh()->status);
    }

    /**
     * Duplicate repayment is not allowed for single repayment id
     *
     * @return void
     */
    public function testDuplicateRepaymentIsNotAllowedForSingleRepaymentId()
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

        $this->postJson(route('api.loan-repayments.store', ['repayment' => $loanRepayments[0]->id]), [
            'amount_paid' => '25',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['amount_paid' => 'Duplicate repayment is not allowed.']);
    }

    /**
     * A user can only make repayment for only related loan id that belongs to him.
     *
     * @return void
     */
    public function testAUserCanOnlyMakeRepaymentForOnlyRelatedLoanIdThatBelongsToHim()
    {
        $this->actingAs($this->user);

        $user2 = User::factory()->create();

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

        $this->actingAs($user2);

        $this->postJson(route('api.loan-repayments.store', ['repayment' => $loanRepayments[0]->id]), [
            'amount_paid' => '25',
        ])->assertStatus(404);
    }

    /**
     * Once the loan is fully paid, the user can not make any repayment.
     *
     * @return void
     */
    public function testOnceTheLoanIsFullyPaidTheUserCanNotMakeAnyRepayment()
    {
        $this->actingAs($this->user);

        $loan = $this->user->loans()->create([
            'amount_required' => '100',
            'terms_in_week' => '4',
        ]);

        $loan->approve();

        $loanRepayments = $loan->loanRepayments()->get();

        $loanRepayments->each(function (LoanRepayment $loanRepayment) {
            $this->postJson(route('api.loan-repayments.store', ['repayment' => $loanRepayment->id]), [
                'amount_paid' => $loanRepayment->amount,
            ])->assertStatus(202);
        });

        $this->assertEquals(0, $loan->remainingDueAmount());

        $this->assertEquals(Loan::PAID, $loan->fresh()->status);

        $this->postJson(route('api.loan-repayments.store', ['repayment' => $loanRepayments[0]->id]), [
            'amount_paid' => '25',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['amount_paid' => 'Loan is fully paid.']);
    }

    /**
     * A user can not make payment more than remaining due amount.
     *
     * @return void
     */
    public function testAUserCanNotMakePaymentMoreThanRemainingDueAmount()
    {
        $this->actingAs($this->user);

        /** @var Loan $loan */
        $loan = $this->user->loans()->create([
            'amount_required' => '100',
            'terms_in_week' => '1',
        ]);

        $loan->approve();

        $loanRepayments = $loan->loanRepayments()->get();

        $this->postJson(route('api.loan-repayments.store', ['repayment' => $loanRepayments[0]->id]), [
            'amount_paid' => '101',
        ])->assertUnprocessable();

        $this->assertEquals(100, $loan->remainingDueAmount());

        /** @var Loan $secondLoan */
        $secondLoan = $this->user->loans()->create([
            'amount_required' => '100',
            'terms_in_week' => '4',
        ]);

        $secondLoan->approve();

        $secondLoanRepayments = $secondLoan->loanRepayments()->take(3)->get();

        $this->postJson(route('api.loan-repayments.store', ['repayment' => $secondLoanRepayments[0]->id]), [
            'amount_paid' => 50,
        ])->assertStatus(202);

        $this->postJson(route('api.loan-repayments.store', ['repayment' => $secondLoanRepayments[1]->id]), [
            'amount_paid' => 26,
        ])->assertStatus(202);

        $this->postJson(route('api.loan-repayments.store', ['repayment' => $secondLoanRepayments[2]->id]), [
            'amount_paid' => 25,
        ])->assertUnprocessable();
    }
}
