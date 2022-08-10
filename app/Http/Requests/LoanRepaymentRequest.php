<?php

namespace App\Http\Requests;

use App\Models\LoanRepayment;
use Illuminate\Foundation\Http\FormRequest;

class LoanRepaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'amount_paid' => ['bail', 'required', 'numeric', $this->doNotProcessIfLoanIsFullyPaid(), $this->mustBeGreaterThanOrEqualToAmountPayable(), $this->duplicatePaymentNotAllowed(), $this->validateAmountIsNotMoreThanRemainingDueAmount()],
        ];
    }

    private function mustBeGreaterThanOrEqualToAmountPayable()
    {
        return function ($attribute, $value, $fail) {
            if ($value < $this->getLoanRepaymentInstance()->amount) {
                $fail('Amount paid must be greater than or equal to amount payable.');
            }
        };
    }

    public function getLoanRepaymentInstance(): LoanRepayment
    {
        return once(function () {
            return LoanRepayment::select('loan_repayments.*')
                ->join('loans', 'loans.id', '=', 'loan_repayments.loan_id')
                ->where('loan_repayments.id', $this->route('repayment'))
                ->where('loans.user_id', auth()->id())
                ->firstOrFail();
        });
    }

    private function duplicatePaymentNotAllowed()
    {
        return function ($attribute, $value, $fail) {
            if ($this->getLoanRepaymentInstance()->amount_paid > 0) {
                $fail('Duplicate repayment is not allowed.');
            }
        };
    }

    private function doNotProcessIfLoanIsFullyPaid()
    {
        return function ($attribute, $value, $fail) {
            if ($this->getLoanRepaymentInstance()->loan->remainingDueAmount() == 0) {
                $fail('Loan is fully paid.');
            }
        };
    }

    private function validateAmountIsNotMoreThanRemainingDueAmount()
    {
        return function ($attribute, $value, $fail) {
            if ($value > $this->getLoanRepaymentInstance()->loan->remainingDueAmount()) {
                $fail('Amount paid must be less than or equal to remaining due amount.');
            }
        };
    }
}
