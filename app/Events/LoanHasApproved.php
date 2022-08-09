<?php

namespace App\Events;

use App\Models\Loan;
use Illuminate\Foundation\Events\Dispatchable;

class LoanHasApproved
{
    use Dispatchable;

    /**
     * @var Loan
     */
    public Loan $loan;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }
}
