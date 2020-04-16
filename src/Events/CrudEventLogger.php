<?php

namespace LaravelCode\Crud\Events;

use Illuminate\Contracts\Queue\ShouldQueue;

class CrudEventLogger implements ShouldQueue
{
    /**
     * @var string
     */
    public $event;

    /**
     * @var array
     */
    public $payload;

    /**
     * LoggingEvent constructor.
     * @param string $event
     * @param array $payload
     */
    public function __construct(string $event, array $payload)
    {
        $this->event = $event;
        $payload = $this->replaceValue($payload, 'password', '********');

        $this->payload = $payload + [
                'meta' => [
                    'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? null,
                    'HTTP_REFERER' => $_SERVER['HTTP_REFERER'] ?? null,
                    'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? null,
                ],
            ];
    }

    /**
     * Replace all found keys with a given value
     * in a multi dimensional associative array.
     *
     * @param array $payload
     * @param string $key
     * @param null $newValue
     * @return array
     */
    public function replaceValue(array $payload, string $key, $newValue = null)
    {
        return collect($payload)->map(function ($item, $vKey) use ($key, $newValue) {
            if ($vKey === $key) {
                return $newValue;
            }

            if (is_array($item)) {
                return $this->replaceValue($item, $key, $newValue);
            }

            return $item;
        })->toArray();
    }
}
