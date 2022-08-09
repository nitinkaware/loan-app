<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanRepaymentTest extends TestCase {

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

        $loan = $this->user->loans()->create([
            'amount_required' => '100',
            'terms_in_week'   => '3',
        ]);

        $this->postJson(route('api.loan-repayments.store', ['loan' => $loan->id, 'repayment']), [
                'amount' => '50',
            ]
        )->assertStatus(202);

        $this->assertEquals(1, $loan->loanRepayments()->count());
    }
}
