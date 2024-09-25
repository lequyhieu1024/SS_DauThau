<?php

namespace App\Enums;

enum BidDocumentStatus: int
{
    case SUBMITTED = 1;
    case UNDER_REVIEW = 2;
    case ACCEPTED = 3;
    case REJECTED = 4;
}
