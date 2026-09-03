<?php

namespace App\Interfaces\HttpResources;

use App\Enums\AuditEvent;
use App\Enums\DbPolyType;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface AuditLogServiceInterface
{
    /**
     * Fetch all user activities
     */
    public function all(): LengthAwarePaginator;

    /**
     * Create an AuditLog
     */
    public function create($userId, AuditEvent $event, Model|DbPolyType $auditableType, $auditableId, Model|array|null $newVal = null, Model|array|null $oldVal = null): AuditLog;

    /**
     * Read a single AuditLog
     */
    public function read($id): AuditLog;

    /**
     * Delete an AuditLog
     */
    public function destroy($id): AuditLog;
}
