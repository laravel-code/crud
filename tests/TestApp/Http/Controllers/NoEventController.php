<?php

namespace TestApp\Http\Controllers;

use Illuminate\Routing\Controller;
use LaravelCode\Crud\Http\Controllers\CrudControllerTrait;
use TestApp\Models\Blog;

class NoEventController extends Controller
{
    use CrudControllerTrait;

    protected function setModel()
    {
        return Blog::class;
    }
}
