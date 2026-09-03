<?php

namespace App\Services\HttpResources;

use App\Enums\AuditEvent;
use App\Enums\DbPolyType;
use App\Enums\PaginationType;
use App\Interfaces\HttpResources\AuditLogServiceInterface;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class AuditLogService extends HttpService implements AuditLogServiceInterface
{
    private AuditLog $model;

    public function __construct(AuditLog $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritDoc}
     */
    public function all(): LengthAwarePaginator
    {
        $query = $this->model::filtered();

        return $this->buildPagination(PaginationType::LENGTH_AWARE, $query);
    }

    /** {@inheritDoc} */
    public function create($userId, AuditEvent $event, Model|DbPolyType $auditableType, $auditableId, Model|array|null $newVal = null, Model|array|null $oldVal = null): AuditLog
    {
        // Convert the value to JSON if not NULL
        $newValue = $newVal;
        if (! is_null($newValue)) {
            $newValue = is_array($newVal) ? json_encode($newVal) : $newVal->toJson();
        }

        // Convert the value to JSON if not NULL
        $oldValue = $oldVal;
        if (! is_null($oldValue)) {
            $oldValue = is_array($oldVal) ? json_encode($oldVal) : $oldVal->toJson();
        }

        // Create via relationship call
        if ($auditableType instanceof Model) {
            return $auditableType->auditLogs()->create([
                'user_id' => $userId,
                'event' => $event,
                'new_value' => $newValue,
                'old_value' => $oldValue,
            ]);
        }

        // Create via the AuditLog model
        return $this->model::create([
            'user_id' => $userId,
            'event' => $event,
            'new_value' => $newValue,
            'old_value' => $oldValue,
            'auditable_type' => $auditableType,
            'auditable_id' => $auditableId,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function read($id): AuditLog
    {
        return $this->model::findOrFail($id)->load('auditable');
    }

    /**
     * {@inheritDoc}
     */
    public function destroy($id): AuditLog
    {
        $audit = $this->model::findOrFail($id);
        $audit->delete();

        return $audit;
    }
}
