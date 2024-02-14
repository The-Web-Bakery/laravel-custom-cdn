<?php

namespace TheWebbakery\CDN\Resources;

use Illuminate\Support\Collection;
use TheWebbakery\CDN\CDNClient;
use TheWebbakery\CDN\Collections\FileCollection;

class FileResource {
    protected ?string $time = null;

    public string $path;
    public string $url;
    public array $details;

    public function __construct(string $path, string $url, array $details) {
        $this->path = $path;
        $this->url = $url;
        $this->details = $details;
    }

    public static function collection(array|Collection $items): FileCollection {
        $collection = new FileCollection();
        foreach($items as $item) {
            $collection->add(
                static::make($item)
            );
        }

        return $collection;
    }

    public static function make(array|Collection $item): FileResource {
        if(is_a($item, Collection::class)) {
            $item = $item->toArray();
        }

        return new static($item['path'], $item['url'], $item['details']);
    }

    public function delete() {
        return app(CDNClient::class)->files()->delete(path: $this->path);
    }

    public function getContent(): string|false {
        return file_get_contents($this->url);
    }
}