<?php

namespace App\Http\Resources\File;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FileResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'path' => $this->path,
            'disk' => $this->disk,
            'created_at' => $this->created_at,
            'metadata' => [
                'original_name' => $this->metadata->original_name ?? null,
                'mime_type' => $this->metadata->mime_type ?? null,
                'size' => $this->metadata->size ?? null,
                'type' => $this->metadata->type ?? null,
                'visibility' => $this->metadata->visibility ?? null,
            ],
        ];
    }
}

