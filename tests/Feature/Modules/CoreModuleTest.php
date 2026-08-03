<?php

namespace Tests\Feature\Modules;

use Tests\TestCase;

class CoreModuleTest extends TestCase
{
    public function test_core_module_registers_its_routes(): void
    {
        $response = $this->getJson('/health/modules');

        $response
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
                'modules' => ['core'],
            ]);
    }
}
