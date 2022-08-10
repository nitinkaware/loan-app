<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /**
     * Admin should not be able to request a loan
     *
     * @return void
     */
    public function testAdminShouldNotBeAbleToRequestALoan()
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson(route('api.loan-requests'), [
            'amount_required' => '100',
            'terms_in_week' => '4',
        ])->assertStatus(403);
    }

    /**
     * A user can request a loan
     *
     * @return void
     */
    public function testAUserCanRequestALoan()
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('api.loan-requests'), [
            'amount_required' => '100',
            'terms_in_week' => '6',
        ]);

        $response->assertCreated();

        $this->assertCount(1, Loan::all());

        $this->assertEquals(Loan::PENDING, Loan::first()->status);
    }

    /**
     * A validation should be in placed when a user requests a loan
     *
     * @return void
     */
    public function testAValidationShouldBeInPlacedWhenAUserRequestsALoan()
    {
        $this->actingAs($this->user);

        $payload = [
            [
                'amount_required' => '',
                'terms_in_week' => '6',
            ],
            [
                'amount_required' => '100',
                'terms_in_week' => '',
            ],
            [
                'amount_required' => '',
                'terms_in_week' => '',
            ],
        ];

        foreach ($payload as $item) {
            $response = $this->postJson(route('api.loan-requests'), $item);

            $response->assertUnprocessable();

            $this->assertCount(0, Loan::all());
        }
    }

    /**
     * A loan amount must be positive number
     *
     * @return void
     */
    public function testALoanAmountMustBePositiveNumber()
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('api.loan-requests'), [
            'amount_required' => '-100',
            'terms_in_week' => '6',
        ]);

        $response->assertUnprocessable();

        $this->assertCount(0, Loan::all());
    }

    /**
     * Loan terms can not be in decimal.
     *
     * @return void
     */
    public function testLoanTermsCanNotBeInDecimal()
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('api.loan-requests'), [
            'amount_required' => '100',
            'terms_in_week' => '6.5',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['terms_in_week' => 'Terms in week must be an integer.']);
    }

    /**
     * A loan terms must be greater than 0
     *
     * @return void
     */
    public function testALoanTermsMustBeGreaterThan0()
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('api.loan-requests'), [
            'amount_required' => '100',
            'terms_in_week' => '0',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['terms_in_week' => 'The terms in week must be at least 1.']);
    }
}
