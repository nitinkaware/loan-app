<?php

namespace App\Models;

use App\Events\LoanHasApproved;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    const PENDING = 'PENDING';

    const APPROVED = 'APPROVED';

    const REJECTED = 'REJECTED';

    const PAID = 'PAID';

    protected $fillable = [
        'amount_required',
        'terms_in_week',
        'status',
    ];

    protected $casts = [
        'terms_in_week' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loanRepayments(): HasMany
    {
        return $this->hasMany(LoanRepayment::class);
    }

    public function remainingDueAmount()
    {
        return $this->amount_required - $this->loanRepayments()->sum('amount_paid');
    }

    public function approve(): bool
    {
        $this->status = self::APPROVED;

        $this->save();

        LoanHasApproved::dispatch($this);

        return true;
    }

    public function isFullyPaid(): bool
    {
        return $this->remainingDueAmount() === 0;
    }

    public function amountPerWeek(): float
    {
        return number_format($this->amount_required / $this->terms_in_week, 2);
    }
}
