<?php

namespace LaravelCode\Crud\Traits;

use Illuminate\Support\Str;
use LaravelCode\Crud\Contracts\ChainedEvents;
use LaravelCode\Crud\Events\CrudEventInterface;

trait ChainedEventsTrait
{
    public function handleChainedEvents()
    {
        if (! $this instanceof ChainedEvents) {
            return;
        }

        $events = get_class($this->event)::chainEvents();
        if (count($events) === 0) {
            return;
        }

        /**
         * @var string $key
         * @var CrudEventInterface $event
         */
        foreach ($events as $key => $event) {
            if ('.*' === substr($key, -2)) {
                $field = substr($key, 0, -2);
                $model = config('crud.models.plural') ? Str::plural($field) : Str::Singular($field);
                $model = Str::studly($model);
                $model = config('crud.namespacePrefix.models').'\\'.$model;
                collect($this->request->get($field, []))->each(function ($data) use ($model, $event) {
                    $data[Str::snake(Str::singular(last(explode('\\', $this->model)))).'_id'] = $this->entity->id;
                    event($event::fromPayload(null, $model, $data));
                });
                continue;
            }

            if ($this->request->has($key)) {
                $model = config('crud.models.plural') ? Str::plural($key) : Str::Singular($key);
                $model = Str::studly($model);
                $model = config('crud.namespacePrefix.models').'\\'.$model;

                $data = $this->request->get($key);
                $data[Str::snake(Str::singular(last(explode('\\', $this->model)))).'_id'] = $this->entity->id;

                event($event::fromPayload(null, $model, $data));
            }
        }
    }
}
