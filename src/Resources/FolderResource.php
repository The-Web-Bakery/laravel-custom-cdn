<?php

namespace TheWebbakery\CDN\Resources;

use Illuminate\Support\Collection;
use TheWebbakery\CDN\CDNClient;
use TheWebbakery\CDN\Collections\FileCollection;
use TheWebbakery\CDN\Collections\FolderCollection;

class FolderResource {
    protected ?string $time = null;

    public ?string $name;
    public ?string $path;

    public ?array $details;
    public ?FolderCollection $folders;
    public ?FileCollection $files;

    public function __construct(?string $name, ?string $path, ?array $details, ?FolderCollection $folders = null, ?FileCollection $files = null) {
        $this->name = $name;
        $this->path = $path;
        $this->details = $details;
        $this->folders = $folders ?: new FolderCollection();
        $this->files = $files ?: new FileCollection();
    }

    public static function collection(array|Collection $items): FolderCollection {
        $collection = new FolderCollection();
        foreach($items as $item) {
            $collection->add(
                static::make($item, $item['folders'], $item['files'])
            );
        }

        return $collection;
    }

    public static function make(array|Collection $item, ?FolderCollection $folders = null, ?FileCollection $files = null): FolderResource {
        if(is_a($item, Collection::class)) {
            $item = $item->toArray();
        }

        return new static($item['name'] ?? null, $item['path'] ?? null, $item['details'] ?? null, $folders, $files);
    }

    public function delete() {
        return app(CDNClient::class)->files()->delete(path: $this->path);
    }
}