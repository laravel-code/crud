<?php

namespace LaravelCode\Crud\Events;

use Illuminate\Http\Request;

interface CrudEventInterface
{
    public static function fromPayload($id, string $model, array $payload);

    public function toPayload(): array;

    public static function rules(Request $request): array;

    public function setId(string $id);

    public function getId();
}
