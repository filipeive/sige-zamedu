<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

use App\Traits\BelongsToSchool;

class License extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'client_name',
        'license_key',
        'activated_at',
        'expires_at',
        'grace_period_days',
        'status',
        'plan',
        'last_ping_at',
        'signature',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_ping_at' => 'datetime',
    ];

    /**
     * Calcular e recalcular o estado atual da licença com base nas datas.
     */
    public function evaluateStatus(): string
    {
        if (!$this->expires_at) {
            $this->status = 'active';
            return 'active';
        }

        $now = Carbon::now();
        $graceEnd = (clone $this->expires_at)->addDays($this->grace_period_days);

        if ($now->lessThanOrEqualTo($this->expires_at)) {
            $this->status = 'active';
        } elseif ($now->lessThanOrEqualTo($graceEnd)) {
            $this->status = 'grace_period';
        } else {
            $this->status = 'suspended';
        }

        $this->save();
        return $this->status;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isInGracePeriod(): bool
    {
        return $this->status === 'grace_period';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }
}
