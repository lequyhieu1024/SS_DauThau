<?php

namespace App\Enums;

enum ProjectStatus: int
{
    case AWAITING = 1;
    case REJECT = 2;
    case RECEIVED = 3;
    case SELECTING_CONTRUCTOR = 4;
    case RESULTS_PUBLICED = 5;
}

