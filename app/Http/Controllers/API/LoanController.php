<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoanRequest;
use Symfony\Component\HttpFoundation\Response;

class LoanController extends Controller
{
    public function store(LoanRequest $request)
    {
        $request->user()
            ->loans()
            ->create($request->only([
                'amount_required',
                'terms_in_week',
            ]));

        return response()->json([
            'message' => 'Loan request created successfully.',
        ], Response::HTTP_CREATED);
    }
}
