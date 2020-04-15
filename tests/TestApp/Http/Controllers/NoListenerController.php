<?php

namespace TestApp\Http\Controllers;

use LaravelCode\Crud\Http\Controllers\CrudControllerTrait;
use TestApp\Models\Blog;

class NoListenerController extends AbstractController
{
    use CrudControllerTrait;

    protected function setModel()
    {
        return Blog::class;
    }
}
