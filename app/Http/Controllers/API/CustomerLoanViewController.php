<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoanCollection;

class CustomerLoanViewController extends Controller
{
    public function index()
    {
        $loansWithRepayments = auth()
            ->user()
            ->loans()
            ->with('loanRepayments')
            ->cursorPaginate(10);

        return new LoanCollection($loansWithRepayments);
    }
}
