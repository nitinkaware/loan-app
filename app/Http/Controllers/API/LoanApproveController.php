<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Symfony\Component\HttpFoundation\Response;

class LoanApproveController extends Controller
{
    public function store(Loan $loan)
    {
        $loan->approve();

        return response()->json([
            'message' => 'Loan approved successfully.',
        ], Response::HTTP_ACCEPTED);
    }
}
