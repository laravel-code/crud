<?php

namespace TestApp\Events;

use Illuminate\Http\Request;
use LaravelCode\Crud\Events\CrudEvent;

class ValidatorEvent extends CrudEvent
{
    /**
     * @param Request $request
     * @return array
     */
    public static function rules(Request $request): array
    {
        return [
            'title' => 'required',
            'description' => 'required',
        ];
    }
}
