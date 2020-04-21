<?php

namespace LaravelCode\Crud\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

abstract class CrudEvent extends AbstractCrudEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $id;
    public $model;
    public $requestParams = [];
    private $modelIds = [];

    public function __construct($id, $model)
    {
        $this->id = $id;
        $this->model = $model;
        $this->requestParams = app()->get(Request::class)->all();
    }

    public static function fromPayload($id, string $model, array $payload)
    {
        return new static($id, $model);
    }

    public static function rules(Request $request): array
    {
        return [];
    }

    /**
     * @param array $rules
     * @param Request $request
     * @return array|mixed
     */
    public static function linkValidators(array $rules, Request $request)
    {
        return static::combineRules($rules, static::chainEvents(), $request);
    }

    /**
     * @param array $rules
     * @param array $chainedRules
     * @param Request $request
     * @return array|mixed
     */
    private static function combineRules(array $rules, array $chainedRules, Request $request)
    {
        foreach ($chainedRules as $key => $event) {
            $newRules = call_user_func([$event, 'rules'], $request);
            $rules = $rules +
                static::makeValidatorRules($key, $newRules) +
                static::makeValidatorRules($key, static::combineRules([], call_user_func([$event, 'chainEvents']), $request));
        }

        return $rules;
    }

    /**
     * @param string $prefix
     * @param array $rules
     * @return mixed
     */
    private static function makeValidatorRules(string $prefix, array $rules)
    {
        return collect($rules)->map(function ($value, $key) use ($prefix) {
            return [$prefix.'.'.$key => $value];
        })->filter(function ($values) {
            $value = array_values($values)[0];
            switch (gettype($value)) {
                case 'string':
                    return ! stristr($value, 'chained');
                case 'array':
                    return ! in_array('chained', $value);
                default:
                    return true;
            }
        })->reduce(function ($rules, $value) {
            if ($rules === null) {
                $rules = [];
            }
            $rules[array_keys($value)[0]] = array_values($value)[0];

            return $rules;
        }) ?: [];
    }

    /**
     * @return array
     */
    public static function chainEvents()
    {
        return [];
    }

    /**
     * @return mixed
     */
    public function getRequestParams(): array
    {
        return $this->requestParams;
    }

    public function setRequestParams(array $params): void
    {
        $this->requestParams = $params;
    }

    /**
     * Append multiple validators.
     * Get rules from other events and append
     * them too the rules of this event.
     *
     * {
     *      "organization": "Laravel-code",
     *      "users: [
     *          {"username": "user 1},
     *          {"username": "user 2},
     *       ]
     * }
     *
     * Within the rules of Organization import the rules for creating a user.
     *
     * $userRules = static::linkValidators('users.*', UserCreateEvent::rules);
     *
     * return [
     *  'organization' => 'required',
     * ] + $userRules;
     *
     * @param array $rules
     * @param string $prefix
     * @param array $newRules
     * @return mixed
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'model' => $this->getModel(),
            'payload' => $this->toPayload(),
        ];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    public function toPayload(): array
    {
        return [
            'id' => $this->getId(),
        ];
    }

    public function addModelId($name, $value = null): void
    {
        if (typeOf($name) === 'string') {
            $this->modelIds[$name] = $value;

            return;
        }

        if (typeOf($name) === 'array') {
            $this->modelIds = $this->modelIds + $name;
        }
    }

    public function getModelIds(): array
    {
        return $this->modelIds;
    }
}
