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

        $requestData = $this->request->all();
        if ($this->event instanceof ChainedEvents) {
            $requestData = $this->event->getRequestParams();
        }

        /**
         * @var string $key
         * @var CrudEventInterface $event
         */
        foreach ($events as $key => $event) {
            if ('.*' === substr($key, -2)) {
                $field = last(explode('.', substr($key, 0, -2)));
                $model = config('crud.models.plural') ? Str::plural($field) : Str::Singular($field);
                $model = Str::studly($model);
                $model = config('crud.namespacePrefix.models').'\\'.$model;
                collect($requestData[$field] ?? [])->each(function ($data) use ($model, $event) {
                    $modelIdField = Str::snake(Str::singular(last(explode('\\', $this->model)))).'_id';
                    $data[$modelIdField] = $this->entity->id;

                    if ($this->event instanceof ChainedEvents) {
                        $data = $data + $this->event->getModelIds();
                    }

                    $newEvent = $event::fromPayload(null, $model, $data);

                    if ($newEvent instanceof ChainedEvents) {
                        $newEvent->setRequestParams($data);
                        $newEvent->addModelId($modelIdField, $this->entity->id);
                    }

                    if ($this->event instanceof ChainedEvents) {
                        $newEvent->addModelId($this->event->getModelIds());
                    }

                    event($newEvent);
                });
                continue;
            }

            $field = last(explode('.', $key));
            if ($requestData[$field] ?? null) {
                $model = config('crud.models.plural') ? Str::plural($field) : Str::Singular($field);
                $model = Str::studly($model);
                $model = config('crud.namespacePrefix.models').'\\'.$model;
                $data = $requestData[$field];

                if ($this->event instanceof ChainedEvents) {
                    $data = $data + $this->event->getModelIds();
                }

                $modelIdField = Str::snake(Str::singular(last(explode('\\', $this->model)))).'_id';
                $data[$modelIdField] = $this->entity->id;

                $newEvent = $event::fromPayload(null, $model, $data);

                if ($newEvent instanceof ChainedEvents) {
                    $newEvent->setRequestParams($data);
                    $newEvent->addModelId($modelIdField, $this->entity->id);
                }

                if ($this->event instanceof ChainedEvents) {
                    $newEvent->addModelId($this->event->getModelIds());
                }

                event($newEvent);
            }
        }
    }
}
