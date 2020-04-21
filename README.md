![PHP Composer](https://github.com/laravel-code/crud/workflows/PHP%20Composer/badge.svg)

# CRUD & Event logging for laravel 6/7

## Why?

This package makes it easy to setup a crud microservice with event logging in Laravel.

## How to install

```shell script
composer require lemoncms/laravel-crud
```


## What can it do?

it can generate Provide a JSON-file with the api-routes you need and generate the code.

It will generate
 - routes/api.php
 - Controllers
 - Models
 - Events
 - Listeners 
 
It will NOT generate
 - migrations
 - working code and never will
 
 After the files are generate you will need to implement your code.
 
 
 # How to use
 
Setup the laravel ```EventServiceProvider``` to autodiscover events.
```php
    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return true;
    }
```

In your project root create a file called [.crud-specs.json](crud-example.json) containing the routes their middleware etc.

Then run:
```shell script
php artisan crud:generate
```

This will create all the files necessary. For existing files you will be asked what to do.


# How to implement in an existing project

You are not required to use the generator. It is mostly convenient when you are starting a new project. 

There are two Traits available for your Controller and Model classes.

And thow extended classes for your Event and Listener.


Controller.php
````php

use Illuminate\Routing\Controller;
use LaravelCode\Crud\Http\Controllers\CrudControllerTrait;

class BlogController extends Controller
{
    use CrudControllerTrait;
}
````

Model.php
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelCode\Crud\Model\CrudTrait;

class Blog extends Model
{
    use CrudTrait;
    use SoftDeletes;

   /**
     * Provided includes can be requested through the comma separated include param
     * E.g.
     * /api/blogs?include=tags
     *
     * @var array
     */
    protected $includes = [
        'tags',
    ];

    /**
     * Fields that can be used to sort by through the order_field and order_dir param
     * E.g.
     * /api/blogs?include=tags&order_field=created_at&order_dir=desc
     *
     * @var array
     */
    protected $orderFields = [
        'id', 'title', 'created_at', 'modified_at',
    ];


    /**
     * Define fields where a full matched can be performed on
     * E.g.
     * /api/blogs?id=1
     *
     * Or provide a callback to create a custom filter
     * E.g.
     * /api/blogs?title=blo
     *
     * @return array
     */
    protected function search()
    {
        return [
            'id',
            'title' => function (Builder $query, $value) {
                return $query->where('title', 'like', "%{$value}%");
            },
        ];
    }

    public function tags()
    {
        return $this->hasMany('App\Models\BlogTag');
    }
}
```

Event.php
````php
<?php

namespace App\Events;

use Illuminate\Http\Request;
use LaravelCode\Crud\Events\CrudEvent;

class BaseBlogEvent extends CrudEvent
{
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $description;

    /**
     * AccountUpdate constructor.
     *
     * @param $id
     * @param string $model
     * @param string $title
     * @param string $description
     */
    public function __construct($id, string $model, string $title, string $description)
    {
        parent::__construct($id, $model);
        $this->title = $title;
        $this->description = $description;
    }

    /**
     * @param $id
     * @param string $model
     * @param array $payload
     * @return BaseBlogEvent
     */
    public static function fromPayload($id, string $model, array $payload)
    {
        return new static(
            null,
            $model,
            $payload['title'],
            $payload['description']
        );
    }

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

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
        ];
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}

````

Listener.php
````php
<?php

namespace App\Listeners;

use LaravelCode\Crud\Listeners\CrudListener;
use TestApp\Events\BlogStoreEvent;

class BlogStoreListener extends CrudListener
{
    /**
     * @var BlogStoreEvent
     */
    protected $event;

    /**
     * @param BlogStoreEvent $event
     */
    public function handle(BlogStoreEvent $event)
    {
        $this->process($event);
    }

    public function beforeRun()
    {
        $this->entity->title = $this->event->getTitle();
        $this->entity->description = $this->event->getDescription();
    }

    public function beforeSave()
    {
        // Do stuff
    }

    public function afterSave()
    {
        // Do stuff
    }
}

````

## Chaining events

Sometimes it could be handy to chain events.

Let say whe have an ```BlogCreateEvent``` and ```TagCreatEvent```
They both have their own validator rules. We have the following json

```json
{
	"title": "Project 3",
	"description": "Description",
	"active": true,
	"tags": [
		{
			"name": "tag 1",
			"description": "some content",
			"active": true
		}
	]
}
```

```BlogCreateEvent```
```php

class BlogCreateEvent extends CrudEvent {

    public static function chainEvents()
    {
        return [
            'tags.*' => TagsCreateEvent::class,
        ];
    }
}
```

```TagCreateEvent``` 

In the rules you see the ```chained``` validation rule, this rule will be used to ignore this field from validation.
When the validation rules al processed, we do not yet have the blog_id, this id will get inserted from its caller class.

This id field will consist of ```Str::snake(Str::singular($this->model))``` with suffix ```_id```

```php

class TagCreateEvent extends CrudEvent {

    public static function rules(Request $request) : array
    {
        return [
            'blog_id' => 'required|chained',
            'name' => 'required',
            'description' => 'required',
            'active' => 'required|boolean',
        ];
    }

}
```


Add ```LaravelCode\Crud\Contracts\ChainedEvent``` contract on the ListenerClass.
This gives you control over which Listeners should emit chainedEvents.

```php

use LaravelCode\Crud\Contracts\ChainedEvents;

class StoreListener implements ChainedEvents {


}
```


## Todo
 - Implement Collection support
 - implement caching
 

