<?php

namespace App\Enums;

enum ProjectStatus: int
{
    case AWAITING = 1;
    case REJECT = 2;
    case APPROVED = 3;
}

