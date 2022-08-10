<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class LoanApproveController extends Controller
{
    public function store(Loan $loan)
    {
        if ($this->isApproved($loan)) {
            throw ValidationException::withMessages([
                'message' => 'Loan is already approved.',
            ]);
        }

        $loan->approve();

        return response()->json([
            'message' => 'Loan approved successfully.',
        ], Response::HTTP_ACCEPTED);
    }

    private function isApproved(Loan $loan)
    {
        return $loan->isApproved();
    }
}
