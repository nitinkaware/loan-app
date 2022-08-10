<?php

namespace App\Http\Resources;

use App\Models\Loan;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LoanCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->map(function (Loan $loan) {
            return [
                'loanId' => $loan->id,
                'amountRequested' => $loan->amount_required,
                'amountRemaining' => $loan->remainingDueAmount(),
                'termsInWeeks' => $loan->terms_in_week,
                'loanStatus' => $loan->status,
                'createdAt' => $loan->created_at->format('jS F Y h:i:s A'),
                'updatedAt' => $loan->updated_at->format('jS F Y h:i:s A'),
                'repaymentsHistory' => $loan->loanRepayments->map(function ($loanRepayment) {
                    return [
                        'id' => $loanRepayment->id,
                        'amountPayable' => floatval($loanRepayment->amount),
                        'amountPaid' => floatval($loanRepayment->amount_paid),
                        'isPaid' => $loanRepayment->amount_paid > 0,
                        'dueOn' => $loanRepayment->due_on->format('jS F Y'),
                    ];
                }),
            ];
        });
    }
}
