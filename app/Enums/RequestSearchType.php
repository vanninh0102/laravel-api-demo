<?php

namespace App\Enums;

enum RequestSearchType
{
    case string;
    case number;
    case date;
}