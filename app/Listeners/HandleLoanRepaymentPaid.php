<?php

namespace App\Listeners;

use App\Events\LoanRepaymentPaid;
use App\Models\Loan;

class HandleLoanRepaymentPaid
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  LoanRepaymentPaid  $event
     * @return void
     */
    public function handle(LoanRepaymentPaid $event)
    {
        if ($event->loanRepayment->loan->isFullyPaid()) {
            $event->loanRepayment->loan->update(['status' => Loan::PAID]);
        }
    }
}
