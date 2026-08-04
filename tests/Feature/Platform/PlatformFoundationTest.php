<?php

declare(strict_types=1);

namespace Tests\Feature\Platform;

use App\Core\Platform\DomainEventRecorder;
use App\Core\Platform\OutboxMessage;
use App\Core\Platform\OutboxProcessor;
use App\Core\Platform\ReferenceDataImportService;
use App\Core\Platform\ReferenceGroup;
use App\Core\Platform\ReferenceValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PlatformFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reference_values_can_be_imported_and_resolved(): void
    {
        ReferenceGroup::query()->create([
            'uuid' => '11111111-1111-1111-1111-111111111111',
            'code' => 'genders',
            'name' => 'Genders',
            'status' => 'active',
            'is_system' => true,
        ]);

        $service = app(ReferenceDataImportService::class);
        $imported = $service->import('genders', [
            ['code' => 'male', 'label' => 'Male'],
        ]);

        $this->assertSame(1, $imported);
        $this->assertDatabaseHas('reference_values', ['code' => 'male']);
    }

    public function test_domain_events_create_outbox_messages(): void
    {
        $recorder = app(DomainEventRecorder::class);
        $event = $recorder->record('admission.application.created', ['id' => 1]);

        $this->assertNotNull($event->id);
        $this->assertDatabaseHas('outbox_messages', ['domain_event_id' => $event->id]);
    }

    public function test_outbox_processor_marks_pending_messages_as_processed(): void
    {
        $event = app(DomainEventRecorder::class)->record('admission.application.created', ['id' => 1]);
        $message = OutboxMessage::query()->where('domain_event_id', $event->id)->firstOrFail();
        $message->forceFill(['status' => 'pending'])->save();

        $processed = app(OutboxProcessor::class)->processPending();

        $this->assertSame(1, $processed);
        $this->assertSame('processed', $message->fresh()->status);
    }
}
