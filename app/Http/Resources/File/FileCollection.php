<?php

namespace App\Http\Resources\File;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\File\FileResource;

class FileCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            FileResource::collection($this->collection),
        ];
    }
}
