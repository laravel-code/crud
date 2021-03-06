<?php

namespace LaravelCode\Crud\Listeners;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use LaravelCode\Crud\Events\CrudEventLogger;
use LaravelCode\Crud\Model\EventsTable;

class CrudLogListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param CrudEventLogger $event
     * @return void
     */
    public function handle(CrudEventLogger $event)
    {
        $createdAt = Carbon::now();
        $payload = json_encode($event->payload);
        $checksum = env('APP_KEY').$event->event.$payload.$createdAt;

        // save in DB
        $entity = new EventsTable();
        $entity->event = $event->event;
        $entity->entity_id = $event->payload['id'] ?? null;
        $entity->payload = $payload;
        $entity->created_at = $createdAt;
        $entity->checksum = bcrypt($checksum);
        $entity->save();
    }
}
