<?php

namespace App\Listeners;

use App\Events\LoanHasApproved;
use App\Models\Loan;

class CreateLoanRepayments
{
    /**
     * Handle the event.
     *
     * @param  LoanHasApproved  $event
     * @return void
     */
    public function handle(LoanHasApproved $event)
    {
        $event->loan->loanRepayments()->createMany(
            $this->getRepayments($event->loan)
        );
    }

    private function getRepayments(Loan $loan): array
    {
        $repayments = [];

        for ($i = 1; $i <= $loan->terms_in_week; $i++) {
            $repayments[] = [
                'amount' => $loan->amountPerWeek(),
                'due_on' => now()->addWeeks($i),
            ];
        }

        return $repayments;
    }
}
