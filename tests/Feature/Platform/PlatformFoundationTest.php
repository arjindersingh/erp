<?php

declare(strict_types=1);

namespace Tests\Feature\Platform;

use App\Core\Platform\DomainEventRecorder;
use App\Core\Platform\OutboxMessage;
use App\Core\Platform\OutboxProcessor;
use App\Core\Platform\ReferenceDataImportService;
use App\Core\Platform\ReferenceGroup;
use App\Core\Platform\ReferenceValue;
use App\Core\Tenancy\Tenant;
use App\Http\Responses\ErrorResponseFactory;
use App\Shared\Exceptions\ApplicationException;
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

    public function test_error_response_factory_returns_safe_payload_for_application_exception(): void
    {
        request()->attributes->set('request_id', 'req-123');
        request()->attributes->set('correlation_id', 'corr-123');

        $exception = new ApplicationException('validation_error', 'Please correct the submitted values.', 422, ['field' => 'status']);
        $response = app(ErrorResponseFactory::class)->fromException($exception, request());

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame('validation_error', $response->getData(true)['error']['code']);
        $this->assertSame('Please correct the submitted values.', $response->getData(true)['error']['message']);
        $this->assertSame('req-123', $response->getData(true)['error']['request_id']);
        $this->assertSame('corr-123', $response->getData(true)['error']['correlation_id']);
    }

    public function test_reference_data_resolver_honors_tenant_scope(): void
    {
        Tenant::query()->forceCreate([
            'id' => 1001,
            'uuid' => 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            'name' => 'Tenant One',
            'slug' => 'tenant-one',
            'code' => 'tenant-one',
        ]);

        Tenant::query()->forceCreate([
            'id' => 1002,
            'uuid' => 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
            'name' => 'Tenant Two',
            'slug' => 'tenant-two',
            'code' => 'tenant-two',
        ]);

        $group = ReferenceGroup::query()->create([
            'uuid' => '22222222-2222-2222-2222-222222222222',
            'code' => 'marital_statuses',
            'name' => 'Marital statuses',
            'status' => 'active',
            'is_system' => true,
        ]);

        ReferenceValue::query()->create([
            'uuid' => '33333333-3333-3333-3333-333333333333',
            'tenant_id' => null,
            'reference_group_id' => $group->id,
            'code' => 'single',
            'label' => 'Single',
            'status' => 'active',
        ]);

        ReferenceValue::query()->create([
            'uuid' => '44444444-4444-4444-4444-444444444444',
            'tenant_id' => 1001,
            'reference_group_id' => $group->id,
            'code' => 'married',
            'label' => 'Married',
            'status' => 'active',
            'created_by_user_id' => null,
            'created_actor_type' => null,
            'created_authentication_state' => null,
            'created_via' => null,
            'created_request_id' => null,
            'created_correlation_id' => null,
        ]);

        $resolver = app(\App\Core\Platform\ReferenceDataResolver::class);
        $tenantOneValues = $resolver->values('marital_statuses', 1001);
        $tenantTwoValues = $resolver->values('marital_statuses', 1002);

        $this->assertTrue($tenantOneValues->contains('code', 'single'));
        $this->assertTrue($tenantOneValues->contains('code', 'married'));
        $this->assertTrue($tenantTwoValues->contains('code', 'single'));
        $this->assertFalse($tenantTwoValues->contains('code', 'married'));
    }

    public function test_outbox_processor_can_dead_letter_a_handler_failure(): void
    {
        $event = app(DomainEventRecorder::class)->record('admission.application.created', ['id' => 1]);
        $message = OutboxMessage::query()->where('domain_event_id', $event->id)->firstOrFail();
        $message->forceFill(['status' => 'pending'])->save();

        $processed = app(OutboxProcessor::class)->processPending(fn (): never => throw new \RuntimeException('boom'));

        $this->assertSame(1, $processed);
        $this->assertSame('dead_lettered', $message->fresh()->status);
        $this->assertDatabaseHas('dead_letter_messages', ['outbox_message_id' => $message->id]);
    }

    public function test_platform_foundation_seeder_populates_reference_data(): void
    {
        $this->artisan('db:seed', ['--class' => 'PlatformFoundationSeeder'])->assertSuccessful();

        $this->assertDatabaseHas('reference_groups', ['code' => 'genders']);
        $this->assertDatabaseHas('reference_values', ['code' => 'male']);
        $this->assertDatabaseHas('reference_values', ['code' => 'female']);
    }
}
