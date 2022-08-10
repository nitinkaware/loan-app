<?php

namespace App\Http\Resources;

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
        return $this->collection->map(function ($loan) {
            return [
                'loanId' => $loan->id,
                'amountRequested' => $loan->amount_required,
                'termsInWeeks' => $loan->terms_in_week,
                'loanStatus' => $loan->status,
                'createdAt' => $loan->created_at,
                'updatedAt' => $loan->updated_at,
                'repaymentsHistory' => $loan->loanRepayments->map(function ($loanRepayment) {
                    return [
                        'id' => $loanRepayment->id,
                        'amountPayable' => $loanRepayment->amount,
                        'paidOn' => $loanRepayment->due_on,
                    ];
                }),
            ];
        });
    }
}
