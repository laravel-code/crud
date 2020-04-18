<?php

namespace Tester\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Response;
use LaravelCode\Crud\Exceptions\MissingEventException;
use LaravelCode\Crud\Exceptions\MissingListenerException;
use LaravelCode\Crud\Exceptions\MissingModelException;
use LaravelCode\Crud\Model\EventsTable;
use Mockery;
use Orchestra\Testbench\TestCase;
use TestApp\Events\BlogStoreEvent;
use TestApp\Events\BlogUpdateEvent;
use TestApp\Models\Blog;
use TestApp\Models\User;

class EventTest extends TestCase
{
    public function testValidatorsLinked()
    {
        $rules = BlogStoreEvent::rules(app()->get('Illuminate\Http\Request'));

        $this->assertEquals([
            'title' => 'required',
            'description' => 'required',
            'tags.*.tag' => 'required',
        ], $rules);
    }

    public function testValidators()
    {
        $rules = BlogUpdateEvent::rules(app()->get('Illuminate\Http\Request'));

        $this->assertEquals([
            'title' => 'required',
            'description' => 'required',
        ], $rules);
    }
}
