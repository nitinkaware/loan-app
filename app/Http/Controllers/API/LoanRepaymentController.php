<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LoanRepayment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoanRepaymentController extends Controller
{
    public function store(Request $request, $repayment)
    {
        $request->validate([
            'amount_paid' => 'required|numeric',
        ]);

        /** @var LoanRepayment $repayment */
        $repayment = LoanRepayment::join('loans', 'loans.id', '=', 'loan_repayments.loan_id')
            ->where('loan_repayments.id', $repayment)
            ->where('loans.user_id', auth()->id())
            ->firstOrFail();

        if ($repayment->loan->isFullyPaid()) {
            return response()->json([
                'message' => 'Loan is fully paid.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $repayment->update([
            'amount_paid' => $request->amount_paid,
        ]);

        return response()->json([
            'message' => 'Loan repayment updated successfully.',
        ], Response::HTTP_ACCEPTED);
    }
}
