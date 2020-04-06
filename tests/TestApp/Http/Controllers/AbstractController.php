<?php

namespace TestApp\Http\Controllers;

use Illuminate\Routing\Controller;
use LaravelCode\Crud\Http\Controllers\CrudControllerTrait;

abstract class AbstractController extends Controller
{
    public $kaas = '1';

    use CrudControllerTrait;
}
