<?php

use App\Enums\AuditEvent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->morphs('auditable');
            $table->enum('event', ConversionHelper::enumToArray(AuditEvent::class))->index();
            $table->json('old_value')->nullable(); // This is populated if `event` column is `updated` or `deleted`
            $table->json('new_value')->nullable(); // This is populated if the `event` column is `updated`, `created`, or `retrieved`
            $table->timestamps();

            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
