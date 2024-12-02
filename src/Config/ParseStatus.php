<?php

namespace App\Config;

enum ParseStatus: string
{
    case None = 'none';
    case Success = 'success';
    case Failed = 'failed';
}
