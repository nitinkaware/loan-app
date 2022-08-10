<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerViewLoanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A customer can view all the loans which he has taken along with repayment history. With cursor pagination.
     */
    public function testACustomerCanViewAllTheLoansWhichHeHasTaken()
    {
        $this->fail('Write some tests!');
    }

    /**
     * A customer can view only the loans that belong to him. Some other user's loan should not be visible.
     */
    public function testACustomerCanViewOnlyTheLoansThatBelongToHim()
    {
        $this->fail('Write some tests!');
    }
}
