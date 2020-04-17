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

    public function __construct($id, $model)
    {
        $this->id = $id;
        $this->model = $model;
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
     *
     * @param $prefix
     * @param $rules
     * @return mixed
     */
    public static function linkValidators($prefix, $rules)
    {
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
}
