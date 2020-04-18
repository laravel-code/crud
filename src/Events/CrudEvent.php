<?php

namespace LaravelCode\Crud\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Request;

abstract class CrudEvent extends AbstractCrudEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;
    /**
     * @var mixed
     */
    public $id;
    /**
     * @var string
     */
    public $model;
    /**
     * @var array
     */
    public $requestParams;

    public function __construct($id,string $model)
    {
        $this->id = $id;
        $this->model = $model;
        $this->requestParams = Request::all();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    public static function fromPayload($id, string $model, array $payload)
    {
        return new static($id, $model);
    }

    public static function rules(Request $request): array
    {
        return [];
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'model' => $this->getModel(),
            'payload' => $this->toPayload(),
        ];
    }

    public function toPayload(): array
    {
        return [
            'id' => $this->getId(),
        ];
    }

    /**
     * @param array $rules
     * @param $events
     * @return array|mixed
     */
    public static function linkValidators(array $rules, $request)
    {
        foreach(static::chainEvents() as $key => $event) {
            $newRules = call_user_func([$event, 'rules'], $request);
            $rules = $rules + static::makeValidatorRules($key, $newRules);
        }

        return $rules;
    }

    /**
     * @param string $prefix
     * @param array $rules
     * @return mixed
     */
    private static function makeValidatorRules(string $prefix, array $rules) {
        return collect($rules)->map(function ($value, $key) use ($prefix) {
            return [$prefix.'.'.$key => $value];
        })->reduce(function ($rules, $value) {
            if ($rules === null) {
                $rules = [];
            }
            $rules[array_keys($value)[0]] = array_values($value)[0];

            return $rules;
        });
    }

    /**
     * @return array
     */
    public function getRequestParams() {
        return $this->requestParams;
    }

    /**
     * @return array
     */
    public static function chainEvents() {
        return [];
    }
}
