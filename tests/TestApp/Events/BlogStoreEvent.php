<?php

namespace TestApp\Events;

use Illuminate\Http\Request;

class BlogStoreEvent extends BaseBlogEvent
{
    /**
     * @param $id
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
}
