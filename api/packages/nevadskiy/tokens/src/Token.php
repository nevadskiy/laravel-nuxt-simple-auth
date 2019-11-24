<?php

namespace Nevadskiy\Tokens;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int tokenable_id
 * @property string tokenable_type
 * @property string token
 * @property string type
 * @property Model tokenable
 * @property Carbon expired_at
 * @property Carbon used_at
 */
class Token extends Model
{
    use SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'used_at',
        'expired_at',
    ];

    /**
     * Get tokenable model which the token is related to.
     */
    public function tokenable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Fill tokenable attributes according to the given model.
     *
     * @param Model $model
     */
    public function fillTokenable(Model $model): void
    {
        $this->tokenable_id = $model->getKey();
        $this->tokenable_type = get_class($model);
    }

    /**
     * Scope a query to only include active tokens.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('used_at')->where('expired_at', '>', now());
    }

    /**
     * Continue the token expire date.
     *
     * @param Carbon $date
     */
    public function continueTo(Carbon $date): void
    {
        $this->update(['expired_at' => $date]);
    }

    /**
     * Mark the token as used.
     */
    public function markAsUsed(): void
    {
        $this->update(['used_at' => now()]);
    }

    /**
     * Determine if the token is expired already.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expired_at->isPast();
    }

    /**
     * Determine if the token is already used.
     *
     * @return bool
     */
    public function isUsed(): bool
    {
        return (bool) $this->used_at;
    }

    /**
     * Convert token to the string type.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->token;
    }
}
