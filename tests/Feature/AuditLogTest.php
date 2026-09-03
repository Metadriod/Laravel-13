<?php

use App\Enums\AuditEvent;
use App\Enums\DbPolyType;
use App\Enums\Role;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Throwable;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private string $baseUri = self::BASE_API_URI.'/audit-logs';

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');

        /** @var User $user */
        $this->user = $this->produceUsers();
        $roles = [Role::ADMIN->value, Role::SUPER_USER->value];

        $this->user->syncRoles(fake()->randomElement($roles));

        Sanctum::actingAs($this->user);
    }

    /**
     * A basic feature test example.
     *
     *
     * @throws Throwable
     */
    public function test_it_can_fetch_all_audit_logs(): void
    {
        $total = 3;
        AuditLog::factory()->count($total)->create();

        $response = $this->get($this->baseUri);
        $response = $response->decodeResponseJson();

        $this->assertIsArray($response['data']);
        $this->assertCount($total, $response['data']);
    }

    /**
     * @throws Throwable
     */
    public function test_it_can_fetch_with_length_aware_pagination(): void
    {
        AuditLog::factory()->count(10)->create();
        $totalAuditLogCount = AuditLog::count('id');

        $limit = 5;
        $response = $this->get("$this->baseUri?limit=$limit");
        $response = $response->decodeResponseJson();

        $this->assertArrayHasKey('pagination', $response);
        $this->assertEquals($totalAuditLogCount, $response['pagination']['total']);
        $this->assertCount($limit, $response['data']);
    }

    /**
     * @throws Throwable
     */
    public function test_it_can_filter_via_user_id(): void
    {
        AuditLog::factory()->count(2)->state([
            'user_id' => $this->user->id,
        ])->create();

        $newUser = $this->produceUsers();
        $totalForNewUser = 3;
        AuditLog::factory()->count($totalForNewUser)->state([
            'user_id' => $newUser->id,
        ])->create();

        $response = $this->get("$this->baseUri?user=$newUser->id");
        $response = $response->decodeResponseJson();
        $this->assertEquals($totalForNewUser, $response['pagination']['total']);
    }

    /**
     * @throws Throwable
     */
    public function test_it_can_filter_via_event_type(): void
    {
        $totalCreatedEvent = 2;
        AuditLog::factory()->count($totalCreatedEvent)->state(['event' => AuditEvent::CREATED])->create();

        $totalUpdatedEvent = 3;
        AuditLog::factory()->count($totalUpdatedEvent)->state(['event' => AuditEvent::UPDATED])->create();

        $eventParam = AuditEvent::CREATED->value;
        $response = $this->get("$this->baseUri?event=$eventParam");
        $response = $response->decodeResponseJson();
        $this->assertEquals($totalCreatedEvent, $response['pagination']['total']);
    }

    /**
     * @throws Throwable
     */
    public function test_it_can_filter_via_auditable_type(): void
    {
        $totalUserAuditLogs = 2;
        AuditLog::factory()->count($totalUserAuditLogs)
            ->for(User::factory(), 'auditable')
            ->create();

        $totalFamilyAuditLogs = 3;
        AuditLog::factory()->count($totalFamilyAuditLogs)
            ->for(User::factory(), 'auditable')
            ->create();

        $auditableType = DbPolyType::USER->value;
        $response = $this->get("$this->baseUri?auditable_type=$auditableType");
        $response = $response->decodeResponseJson();
        $this->assertEquals($totalFamilyAuditLogs, $response['pagination']['total']);
    }

    /**
     * @throws Throwable
     */
    public function test_it_can_filter_via_auditable_type_and_auditable_id(): void
    {
        $familyOne = $this->produceUsers();
        $totalFamilyOneAuditLogs = 3;
        AuditLog::factory()
            ->count($totalFamilyOneAuditLogs)
            ->state(['auditable_type' => DbPolyType::USER, 'auditable_id' => $familyOne->id])
            ->create();

        $familyTwo = $this->produceUsers();
        $totalFamilyTwoAuditLogs = 2;
        AuditLog::factory()
            ->count($totalFamilyTwoAuditLogs)
            ->state(['auditable_type' => DbPolyType::USER, 'auditable_id' => $familyTwo->id])
            ->create();

        $auditableType = DbPolyType::USER->value;
        $response = $this->get("$this->baseUri?auditable_type=$auditableType&auditable_id=$familyTwo->id");
        $response = $response->decodeResponseJson();
        $this->assertEquals($totalFamilyTwoAuditLogs, $response['pagination']['total']);
    }

    public function test_it_can_read_a_single_audit_log(): void
    {
        $auditLog = AuditLog::factory()->create();
        $response = $this->get("$this->baseUri/$auditLog->id");
        $response->assertStatus(200);
    }

    public function test_only_admins_and_super_users_can_use_routes(): void
    {
        // Log in a regular user
        $user = $this->produceUsers();
        $user->syncRoles([Role::STANDARD_USER->value]);
        Sanctum::actingAs($user);

        $record = AuditLog::factory()->create();

        // Fetch all
        $response = $this->get($this->baseUri);
        $response->assertStatus(403);

        // Read
        $response = $this->get($this->baseUri."/$record->id");
        $response->assertStatus(403);
    }
}
