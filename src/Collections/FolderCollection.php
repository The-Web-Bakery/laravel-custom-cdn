<?php

namespace TheWebbakery\CDN\Collections;

use Illuminate\Support\Collection;
use TheWebbakery\CDN\Resources\FolderResource;

class FolderCollection extends Collection
{
    public static function fromResponse($response) {
        $collection = collect([
            'folders' => FolderResource::collection($response['folders']),
            'root' => FolderResource::make($response['root_folder']),
            'files' => $response['files'],
        ]);

        parent::__construct($collection);
    }
//    public function root() {
//        return $this->get('root_folder');
//    }
//    public function folders() {
//        return $this->get('folders');
//    }
//    public function files() {
//        return $this->get('files');
//    }
}
