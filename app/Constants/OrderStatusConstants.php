<?php

namespace App\Constants;

class OrderStatusConstants
{
    public const SUBMITTED = 'submitted';
    public const PROCESSING = 'processing';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';

    public static function all(): array
    {
        return [
            self::SUBMITTED,
            self::PROCESSING,
            self::COMPLETED,
            self::CANCELLED,
        ];
    }
}
