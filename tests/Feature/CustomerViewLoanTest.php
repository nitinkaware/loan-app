<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerViewLoanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * An unauthorized user cannot view a loan.
     */
    public function testUnauthorizedUserCannotViewLoan()
    {
        $this->getJson(route('api.loans.index'))
            ->assertUnauthorized();
    }

    /**
     * A customer can view all the loans which he has taken along with repayment history. With cursor pagination.
     */
    public function testACustomerCanViewAllTheLoansWhichHeHasTaken()
    {
        $this->seed();

        $user = User::first();

        $response = $this->actingAs($user)->getJson(route('api.loans.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'loanId',
                        'amountRequested',
                        'termsInWeeks',
                        'loanStatus',
                        'createdAt',
                        'updatedAt',
                        'repaymentsHistory' => [
                            '*' => [
                                'id',
                                'amountPayable',
                                'paidOn',
                            ],
                        ],
                    ],
                ],
                'links',
                'meta',
            ]);

        $jsonResponse = $response->json();

        $this->assertCount(10, $jsonResponse['data']);

        $user->loans->each(function ($loan) use ($jsonResponse) {
            $this->assertContains($loan->id, array_column($jsonResponse['data'], 'loanId'));
        });
    }

    /**
     * A customer can view only the loans that belong to him. Some other user's loan should not be visible.
     */
    public function testACustomerCanViewOnlyTheLoansThatBelongToHim()
    {
        $this->seed();

        $user = User::first();

        $anotherUser = User::find(2);

        $response = $this->actingAs($user)->getJson(route('api.loans.index'));

        $anotherUser->loans->each(function ($loan) use ($response) {
            $this->assertNotContains($loan->id, array_column($response->json()['data'], 'loanId'));
        });
    }
}
