<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuditLogRequest;
use App\Interfaces\HttpResources\AuditLogServiceInterface;
use Illuminate\Http\JsonResponse;
use PaginationHelper;
use Symfony\Component\HttpFoundation\Response;

class AuditLogController extends ApiController
{
    private AuditLogServiceInterface $auditLogService;

    public function __construct(AuditLogServiceInterface $auditLogService)
    {
        $this->auditLogService = $auditLogService;
    }

    /**
     * Fetch all audit logs
     */
    public function index(AuditLogRequest $request): JsonResponse
    {
        $auditLogs = $this->auditLogService->all();
        $formatted = PaginationHelper::formatPagination($auditLogs);

        return $this->success($formatted, Response::HTTP_OK);
    }

    public function read($id, AuditLogRequest $request): JsonResponse
    {
        $auditLog = $this->auditLogService->read($id);

        return $this->success(['data' => $auditLog], Response::HTTP_OK);
    }
}
