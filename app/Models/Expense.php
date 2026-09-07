<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\BelongsToSchool;

class Expense extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'category_id',
        'description',
        'amount',
        'expense_date',
        'payment_method',
        'receipt_number',
        'supplier',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(FinancialCategory::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
