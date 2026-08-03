<?php

declare(strict_types=1);

return [
    'enabled' => env('AUDIT_ENABLED', true),
    'queue' => false,
    'default_retention_days' => 730,
    'integrity' => ['enabled' => true, 'algorithm' => 'sha256'],
    'excluded_fields' => ['updated_at', 'remember_token', 'password', 'password_confirmation'],
    'sensitive_fields' => ['email', 'mobile', 'phone', 'bank_account', 'account_number', 'aadhaar', 'pan_number', 'medical'],
];
