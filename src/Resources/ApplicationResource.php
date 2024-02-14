<?php

namespace TheWebbakery\CDN\Resources;

use Illuminate\Support\Collection;
use TheWebbakery\CDN\CDNClient;
use TheWebbakery\CDN\Collections\ApplicationCollection;

class ApplicationResource {
    protected ?string $time = null;

    protected string $id;
    protected string $name;
    protected string $secret;

    public function __construct(string $id, string $name, string $secret) {
        $this->id = $id;
        $this->name = $name;
        $this->secret = $secret;
    }

    public static function collection(array|Collection $items): ApplicationCollection {
        $collection = new ApplicationCollection();
        foreach($items as $item) {
            $collection->add(
                static::make($item)
            );
        }

        return $collection;
    }

    public static function make(array|Collection $item): ApplicationResource {
        if(is_a($item, Collection::class)) {
            $item = $item->toArray();
        }

        return new static($item['id'], $item['name'], $item['secret']);
    }

    public function delete() {
        return app(CDNClient::class)->applications()->delete(id: $this->id);
    }

    public function refresh(): ApplicationResource {
        return app(CDNClient::class)->applications()->find(id: $this->id);
    }
}