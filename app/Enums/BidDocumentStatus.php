<?php

namespace App\Enums;

enum BidDocumentStatus: int
{
    case SUBMITTED = 1;
    case UNDER_REVIEW = 2;
    case ACCEPTED = 3;
    case REJECTED = 4;

    public function label(): string
    {
        return match ($this) {
            self::SUBMITTED => 'Đã gửi',
            self::UNDER_REVIEW => 'Đang xem xét',
            self::ACCEPTED => 'Đã chấp nhận',
            self::REJECTED => 'Đã từ chối',
        };
    }
}
