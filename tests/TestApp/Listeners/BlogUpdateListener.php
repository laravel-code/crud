<?php

namespace TestApp\Listeners;

use LaravelCode\Crud\Listeners\CrudListener;
use TestApp\Events\BlogUpdateEvent;

class BlogUpdateListener extends CrudListener
{
    /**
     * @var BlogUpdateEvent
     */
    protected $event;

    /**
     * @param BlogUpdateEvent $event
     */
    public function handle(BlogUpdateEvent $event)
    {
        $this->process($event);
    }

    public function setTitle($value)
    {
        $this->entity->title = $value;
    }

    public function setDescription($value)
    {
        $this->entity->description = $value;
    }

    protected function afterDelete()
    {
    }
}
