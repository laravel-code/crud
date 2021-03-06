<?php

namespace LaravelCode\Crud\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EventsTable.
 *
 * @property int                             $id
 * @property string                          $event
 * @property string                          $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\LaravelCode\Crud\Model\EventsTable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\LaravelCode\Crud\Model\EventsTable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\LaravelCode\Crud\Model\EventsTable query()
 * @method static \Illuminate\Database\Eloquent\Builder|\LaravelCode\Crud\Model\EventsTable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\LaravelCode\Crud\Model\EventsTable whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\LaravelCode\Crud\Model\EventsTable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\LaravelCode\Crud\Model\EventsTable wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\LaravelCode\Crud\Model\EventsTable whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EventsTable extends Model
{
    protected $table = 'events';
}
