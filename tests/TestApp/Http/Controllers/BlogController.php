<?php

namespace TestApp\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LaravelCode\Crud\Http\Controllers\CrudControllerTrait;
use TestApp\Events\ValidatorEvent;

class BlogController extends Controller
{
    use AuthorizesRequests;
    use CrudControllerTrait;

    public function testValidator(Request $request)
    {
        $this->validate(ValidatorEvent::class, $request);

        return response()->json(['ok']);
    }
}
