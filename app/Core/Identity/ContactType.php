<?php

declare(strict_types=1);

namespace App\Core\Identity;

enum ContactType: string
{
    case Email = 'email';
    case Mobile = 'mobile';
    case Phone = 'phone';
    case WhatsApp = 'whatsapp';
    case Emergency = 'emergency';
}
