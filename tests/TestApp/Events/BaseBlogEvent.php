<?php

namespace TestApp\Events;

use Illuminate\Http\Request;
use LaravelCode\Crud\Events\CrudEvent;

class BaseBlogEvent extends CrudEvent
{
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $description;

    /**
     * AccountUpdate constructor.
     *
     * @param $id
     * @param string $model
     * @param string $tag
     * @param string $description
     */
    public function __construct($id, string $model, string $tag, string $description)
    {
        parent::__construct($id, $model);
        $this->title = $tag;
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
        $rules = [
            'title' => 'required',
            'description' => 'required',
        ];

        return static::linkValidators($rules, $request);
    }

    /**
     * @return array
     */
    public function toPayload(): array
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
