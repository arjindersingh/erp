<?php

declare(strict_types=1);

namespace Tests\Feature\Core;

use App\Core\Settings\SettingDefinition;
use App\Core\Settings\SettingGroup;
use App\Core\Settings\SettingOptionSet;
use Database\Seeders\SettingFoundationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('settings')]
final class SettingFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_setting_foundation_seeder_creates_groups_option_sets_and_definitions(): void
    {
        $this->seed(SettingFoundationSeeder::class);

        $this->assertDatabaseHas('setting_groups', ['code' => 'localization', 'name' => 'Localization']);
        $this->assertDatabaseHas('setting_option_sets', ['code' => 'locales']);
        $this->assertDatabaseHas('setting_definitions', ['key' => 'localization.locale', 'name' => 'Default Locale']);

        $group = SettingGroup::query()->where('code', 'localization')->firstOrFail();
        $optionSet = SettingOptionSet::query()->where('code', 'locales')->firstOrFail();
        $definition = SettingDefinition::query()->where('key', 'localization.locale')->firstOrFail();

        $this->assertSame($group->id, $definition->setting_group_id);
        $this->assertSame($optionSet->id, $definition->setting_option_set_id);
        $this->assertCount(3, $optionSet->options);
    }
}
