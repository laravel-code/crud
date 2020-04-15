<?php

namespace TestApp\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use LaravelCode\Crud\Http\Controllers\CrudControllerTrait;

class BlogController extends Controller
{
    use AuthorizesRequests;
    use CrudControllerTrait;
}
