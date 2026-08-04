<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Settings\SettingDefinition;
use App\Core\Settings\SettingGroup;
use App\Core\Settings\SettingOption;
use App\Core\Settings\SettingOptionSet;
use Illuminate\Database\Seeder;

final class SettingFoundationSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            'general' => 'General Settings',
            'localization' => 'Localization',
            'system' => 'System Defaults',
        ];

        foreach ($groups as $code => $name) {
            SettingGroup::query()->updateOrCreate(
                ['code' => $code],
                ['name' => $name, 'description' => "{$name} for tenants and platform-wide settings", 'display_order' => array_search($code, array_keys($groups), true) * 10, 'status' => 'active'],
            );
        }

        $localeOptionSet = SettingOptionSet::query()->updateOrCreate(
            ['code' => 'locales'],
            ['name' => 'Locales', 'description' => 'Supported locale identifiers', 'value_type' => 'string', 'supports_translations' => false, 'status' => 'active'],
        );

        $dateFormatOptionSet = SettingOptionSet::query()->updateOrCreate(
            ['code' => 'date_formats'],
            ['name' => 'Date Formats', 'description' => 'Date display and parsing formats', 'value_type' => 'string', 'status' => 'active'],
        );

        $timeFormatOptionSet = SettingOptionSet::query()->updateOrCreate(
            ['code' => 'time_formats'],
            ['name' => 'Time Formats', 'description' => 'Time display formats', 'value_type' => 'string', 'status' => 'active'],
        );

        $currencyOptionSet = SettingOptionSet::query()->updateOrCreate(
            ['code' => 'currencies'],
            ['name' => 'Currencies', 'description' => 'Currency codes for tenant billing and display', 'value_type' => 'string', 'status' => 'active'],
        );

        $this->seedOptions($localeOptionSet, [
            ['code' => 'en', 'label' => 'English', 'value_json' => ['value' => 'en']],
            ['code' => 'hi', 'label' => 'Hindi', 'value_json' => ['value' => 'hi']],
            ['code' => 'bn', 'label' => 'Bengali', 'value_json' => ['value' => 'bn']],
        ]);

        $this->seedOptions($dateFormatOptionSet, [
            ['code' => 'Y-m-d', 'label' => 'YYYY-MM-DD', 'value_json' => ['value' => 'Y-m-d']],
            ['code' => 'd/m/Y', 'label' => 'DD/MM/YYYY', 'value_json' => ['value' => 'd/m/Y']],
            ['code' => 'm/d/Y', 'label' => 'MM/DD/YYYY', 'value_json' => ['value' => 'm/d/Y']],
        ]);

        $this->seedOptions($timeFormatOptionSet, [
            ['code' => 'H:i', 'label' => '24 Hour', 'value_json' => ['value' => 'H:i']],
            ['code' => 'h:i A', 'label' => '12 Hour', 'value_json' => ['value' => 'h:i A']],
        ]);

        $this->seedOptions($currencyOptionSet, [
            ['code' => 'INR', 'label' => 'Indian Rupee', 'value_json' => ['value' => 'INR']],
            ['code' => 'USD', 'label' => 'US Dollar', 'value_json' => ['value' => 'USD']],
            ['code' => 'EUR', 'label' => 'Euro', 'value_json' => ['value' => 'EUR']],
        ]);

        SettingDefinition::query()->updateOrCreate(
            ['key' => 'localization.locale'],
            [
                'setting_group_id' => SettingGroup::query()->where('code', 'localization')->firstOrFail()->id,
                'setting_option_set_id' => $localeOptionSet->id,
                'name' => 'Default Locale',
                'description' => 'Default locale for the tenant or platform',
                'help_text' => 'Select the locale used for content and formatting.',
                'value_type' => 'string',
                'default_value_json' => ['value' => 'en'],
                'allowed_scopes_json' => ['platform', 'tenant'],
                'validation_rules_json' => ['required', 'string', 'max:5'],
                'ui_component' => 'select',
                'display_order' => 10,
                'is_required' => true,
                'is_inheritable' => true,
                'is_cacheable' => true,
                'status' => 'active',
            ],
        );

        SettingDefinition::query()->updateOrCreate(
            ['key' => 'localization.date_format'],
            [
                'setting_group_id' => SettingGroup::query()->where('code', 'localization')->firstOrFail()->id,
                'setting_option_set_id' => $dateFormatOptionSet->id,
                'name' => 'Date Format',
                'description' => 'Preferred date formatting style',
                'help_text' => 'Choose how dates are shown across the application.',
                'value_type' => 'string',
                'default_value_json' => ['value' => 'd/m/Y'],
                'allowed_scopes_json' => ['platform', 'tenant'],
                'validation_rules_json' => ['required', 'string'],
                'ui_component' => 'select',
                'display_order' => 20,
                'is_required' => true,
                'is_inheritable' => true,
                'is_cacheable' => true,
                'status' => 'active',
            ],
        );

        SettingDefinition::query()->updateOrCreate(
            ['key' => 'localization.time_format'],
            [
                'setting_group_id' => SettingGroup::query()->where('code', 'localization')->firstOrFail()->id,
                'setting_option_set_id' => $timeFormatOptionSet->id,
                'name' => 'Time Format',
                'description' => 'Preferred time formatting style',
                'help_text' => 'Choose how times are shown across the application.',
                'value_type' => 'string',
                'default_value_json' => ['value' => 'H:i'],
                'allowed_scopes_json' => ['platform', 'tenant'],
                'validation_rules_json' => ['required', 'string'],
                'ui_component' => 'select',
                'display_order' => 30,
                'is_required' => true,
                'is_inheritable' => true,
                'is_cacheable' => true,
                'status' => 'active',
            ],
        );

        SettingDefinition::query()->updateOrCreate(
            ['key' => 'localization.currency'],
            [
                'setting_group_id' => SettingGroup::query()->where('code', 'localization')->firstOrFail()->id,
                'setting_option_set_id' => $currencyOptionSet->id,
                'name' => 'Currency',
                'description' => 'Default currency for transactions and displays',
                'help_text' => 'Select the default currency code for this tenant.',
                'value_type' => 'string',
                'default_value_json' => ['value' => 'INR'],
                'allowed_scopes_json' => ['platform', 'tenant'],
                'validation_rules_json' => ['required', 'string'],
                'ui_component' => 'select',
                'display_order' => 40,
                'is_required' => true,
                'is_inheritable' => true,
                'is_cacheable' => true,
                'status' => 'active',
            ],
        );
    }

    private function seedOptions(SettingOptionSet $optionSet, array $options): void
    {
        foreach ($options as $definition) {
            SettingOption::query()->updateOrCreate(
                ['setting_option_set_id' => $optionSet->id, 'code' => $definition['code']],
                ['label' => $definition['label'], 'value_json' => $definition['value_json'], 'display_order' => $definition['display_order'] ?? 100, 'status' => 'active'],
            );
        }
    }
}
