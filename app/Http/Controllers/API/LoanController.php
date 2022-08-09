<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoanRequest;

class LoanController extends Controller {

    public function store(LoanRequest $request)
    {
        return $request->user()
            ->loans()
            ->create($request->only([
                'amount_required',
                'terms_in_week',
            ]));
    }
}
