<?php

namespace TestApp\Events;

use Illuminate\Http\Request;
use LaravelCode\Crud\Events\CrudEvent;

class TagStoreEvent extends CrudEvent
{
    /**
     * @var string
     */
    public $tag;

    /**
     * AccountUpdate constructor.
     *
     * @param $id
     * @param string $model
     * @param string $tag
     */
    public function __construct($id, string $model, string $tag)
    {
        parent::__construct($id, $model);
        $this->tag = $tag;
    }

    /**
     * @param $id
     * @param string $model
     * @param array $payload
     * @return CrudEvent|static
     */
    public static function fromPayload($id, string $model, array $payload)
    {
        return new static(
            null,
            $model,
            $payload['title']
        );
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function rules(Request $request): array
    {
        return [
            'tag' => 'required',
        ];
    }

    /**
     * @return array
     */
    public function toPayload(): array
    {
        return [
            'id' => $this->getId(),
            'tag' => $this->getTag(),
        ];
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }
}
