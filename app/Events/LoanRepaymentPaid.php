<?php

namespace App\Events;

use App\Models\LoanRepayment;
use Illuminate\Foundation\Events\Dispatchable;

class LoanRepaymentPaid
{
    use Dispatchable;

    public LoanRepayment $loanRepayment;

    public function __construct(LoanRepayment $loanRepayment)
    {
        $this->loanRepayment = $loanRepayment;
    }
}
