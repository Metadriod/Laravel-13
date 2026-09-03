<?php

namespace App\Models;

use App\Enums\AuditEvent;
use App\QueryFilters\AuditLog\AuditableId;
use App\QueryFilters\AuditLog\AuditableType;
use App\QueryFilters\Generic\Event;
use App\QueryFilters\Generic\UserFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Pipeline\Pipeline;

class AuditLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'auditable_id',
        'auditable_type',
        'event',
        'new_value',
        'old_value',
    ];

    /**
     * Attributes that should be cast
     *
     * @var string[]
     */
    protected $casts = [
        'event' => AuditEvent::class,
    ];

    /**
     * @Scope
     * Pipeline for HTTP Query filters
     */
    public function scopeFiltered(Builder $builder): Builder
    {
        return app(Pipeline::class)
            ->send($builder)
            // Filter by User, Subject, Event
            ->through([
                UserFilter::class,
                AuditableType::class,
                AuditableId::class,
                Event::class,
            ])
            ->thenReturn();
    }

    /**
     * Get parent commentable model (E.g. User, SocialWorker, etc.)
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }
}
