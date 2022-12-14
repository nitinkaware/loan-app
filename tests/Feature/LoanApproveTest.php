<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanApproveTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->admin()
            ->create();
    }

    /**
     * When a user request a loan, and admin approve it, the loan status should be approved,
     * and equal amount of installment should be created to loan_repayments table.
     *
     * @return void
     */
    public function testAnEqualAmountOfRepaymentsShouldBeCreated()
    {
        $this->actingAs($this->user);

        User::factory()
            ->create()
            ->loans()
            ->create([
                'amount_required' => '100',
                'terms_in_week' => '3',
            ]);

        /** @var Loan $loan */
        $loan = Loan::first();

        $this->postJson(
            route('api.loan-requests.approve', ['loan' => $loan->id])
        )->assertStatus(202);

        $this->assertEquals(
            $loan->terms_in_week,
            Loan::find($loan->id)->loanRepayments()->count()
        );

        $amountPerWeek = $loan->amountPerWeek();

        $this->assertEquals(
            $amountPerWeek,
            Loan::find($loan->id)->loanRepayments()->first()->amount
        );
    }

    /**
     * A loan can not be approved multiple times.
     *
     * @return void
     */
    public function testALoanCanNotBeApprovedMultipleTimes()
    {
        $this->actingAs($this->user);

        $loan = $this->user->loans()->create([
            'amount_required' => '100',
            'terms_in_week' => '3',
        ]);

        $this->postJson(
            route('api.loan-requests.approve', ['loan' => $loan->id])
        )->assertStatus(202);

        $this->postJson(
            route('api.loan-requests.approve', ['loan' => $loan->id])
        )->assertStatus(422);
    }
}
