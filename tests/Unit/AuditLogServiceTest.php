<?php

use App\Enums\AuditEvent;
use App\Enums\DbPolyType;
use App\Interfaces\HttpResources\AuditLogServiceInterface;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\HttpResources\AuditLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuditLogServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuditLogServiceInterface $auditLogService;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
        $this->auditLogService = new AuditLogService(new AuditLog);
        $this->user = $this->produceUsers();
    }

    public function test_it_can_create_an_audit_log_based_on_eloquent_model(): void
    {
        $auditableModel = $this->produceUsers();
        $this->auditLogService->create(
            $this->user->id,
            AuditEvent::CREATED,
            $auditableModel,
            $auditableModel->id,
            $auditableModel,
        );

        $this->assertDatabaseCount('audit_logs', 1);
        /** @var AuditLog $audit */
        $audit = DB::table('audit_logs')->first();
        $this->assertEquals(AuditEvent::CREATED->value, $audit->event);
        $this->assertEquals(DbPolyType::USER->value, $audit->auditable_type);
        $this->assertEquals($auditableModel->id, $audit->auditable_id);
        $this->assertNotNull($audit->new_value);
        $this->assertNull($audit->old_value);
    }

    public function test_it_can_create_an_audit_based_on_enums_and_array(): void
    {
        $auditableModel = $this->produceUsers();
        $this->auditLogService->create(
            $this->user->id,
            AuditEvent::CREATED,
            DbPolyType::USER,
            $auditableModel->id,
            $auditableModel->toArray(),
        );

        $this->assertDatabaseCount('audit_logs', 1);
        /** @var AuditLog $audit */
        $audit = DB::table('audit_logs')->first();
        $this->assertEquals(AuditEvent::CREATED->value, $audit->event);
        $this->assertEquals(DbPolyType::USER->value, $audit->auditable_type);
        $this->assertEquals($auditableModel->id, $audit->auditable_id);
        $this->assertNotNull($audit->new_value);
        $this->assertNull($audit->old_value);
    }

    public function test_it_can_fetch_all_records(): void
    {
        $auditableModel = $this->produceUsers();
        $this->auditLogService->create(
            $this->user->id,
            AuditEvent::CREATED,
            $auditableModel,
            $auditableModel->id,
            $auditableModel,
        );
        $this->auditLogService->create(
            $this->user->id,
            AuditEvent::UPDATED,
            $auditableModel,
            $auditableModel->id,
            $auditableModel,
            $auditableModel
        );

        $records = $this->auditLogService->all();
        $this->assertCount(2, $records);
    }

    public function test_it_can_fetch_all_audit_records_with_length_aware_pagination()
    {
        $user = $this->produceUsers();

        $count = 10;
        foreach (range(1, $count) as $item) {
            $this->auditLogService->create(
                $this->user->id,
                AuditEvent::CREATED,
                $user,
                $user->id,
                $user,
            );
        }

        $request = new Request;
        $limit = 5;
        $request->replace(['limit' => $limit]);
        app()->instance('request', $request);

        $auditLogs = $this->auditLogService->all();

        $this->assertEquals($count, $auditLogs->total());
        $this->assertCount($limit, $auditLogs->items());
    }

    public function test_it_can_read_a_single_audit_log(): void
    {
        $auditableModel = $this->produceUsers();
        $auditLog = $this->auditLogService->create(
            $this->user->id,
            AuditEvent::CREATED,
            $auditableModel,
            $auditableModel->id,
            $auditableModel,
        );

        $foundAuditLog = $this->auditLogService->read($auditLog->id);
        $this->assertEquals($auditLog->id, $foundAuditLog->id);
    }

    public function test_it_can_delete_an_audit_log(): void
    {
        $auditableModel = $this->produceUsers();
        $auditLog = $this->auditLogService->create(
            $this->user->id,
            AuditEvent::CREATED,
            $auditableModel,
            $auditableModel->id,
            $auditableModel,
        );

        $this->auditLogService->destroy($auditLog->id);
        $this->assertDatabaseCount('audit_logs', 0);
    }
}
