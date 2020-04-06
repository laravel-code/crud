<?php

namespace TestApp\Listeners;

use LaravelCode\Crud\Listeners\CrudListener;
use TestApp\Events\BlogDestroyEvent;

class BlogDestroyListener extends CrudListener
{
    /**
     * @var BlogDestroyEvent
     */
    protected $event;

    /**
     * @param BlogDestroyEvent $event
     */
    public function handle(BlogDestroyEvent $event)
    {
        $this->setDelete(true);
        $this->process($event);
    }
}
