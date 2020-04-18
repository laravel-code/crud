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

    /**
     * @param Request $request
     * @return array
     */
    public static function rules(Request $request): array
    {
        $rules = [
            'title' => 'required',
            'description' => 'required',
        ];

        return static::linkValidators($rules, $request);
    }

    public static function chainEvents()
    {
        return [
            'tags.*' => TagStoreEvent::class,
        ];
    }
}
