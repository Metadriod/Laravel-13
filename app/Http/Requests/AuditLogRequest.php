<?php

namespace App\Http\Requests;

use App\Enums\AuditEvent;
use App\Enums\DbPolyType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class AuditLogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $routeName = $this->route()->getName();

        return match ($routeName) {
            'audit_logs.index' => $this->getFetchRules(),
            default => [],
        };
    }

    /**
     * User update rules
     */
    private function getFetchRules(): array
    {
        return [
            'auditable_type' => [new Enum(DbPolyType::class)],
            'auditable_id' => ['int'],
            'user_id' => ['int'],
            'event' => [new Enum(AuditEvent::class)],
            'sort' => ['in:asc,desc'],
            'sort_by' => ['string'],
            'limit' => ['int'],
            'page' => ['int'],
        ];
    }
}
