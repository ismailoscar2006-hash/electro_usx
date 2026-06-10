<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests;
}
