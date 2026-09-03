<?php

namespace Database\Factories;

use App\Enums\AuditEvent;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $auditable = $this->getAuditable();
        $event = $this->getEvent();

        return [
            'user_id' => User::factory(),
            'auditable_type' => $auditable,
            'auditable_id' => $auditable::factory(),
            'new_value' => json_encode($auditable),
            'old_value' => json_encode($auditable),
            'event' => $event,
        ];
    }

    private function getAuditable()
    {
        return $this->faker->randomElement([
            User::class,
        ]);
    }

    private function getEvent(): AuditEvent
    {
        return $this->faker->randomElement([
            AuditEvent::UPDATED, AuditEvent::DELETED, AuditEvent::CREATED, AuditEvent::RETRIEVED,
        ]);
    }
}
