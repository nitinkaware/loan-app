<?php

namespace App\Http\Controllers\API;

use App\Events\LoanRepaymentPaid;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoanRepaymentRequest;
use Symfony\Component\HttpFoundation\Response;

class LoanRepaymentController extends Controller
{
    public function store(LoanRepaymentRequest $request)
    {
        $repayment = $request->getLoanRepaymentInstance();

        $repayment->update([
            'amount_paid' => $request->amount_paid,
        ]);

        LoanRepaymentPaid::dispatch($repayment);

        return response()->json([
            'message' => 'Loan repayment updated successfully.',
        ], Response::HTTP_ACCEPTED);
    }
}
