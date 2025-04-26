<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\File\File;
use App\Models\File\FileMetadata;

class FileService
{
    public function upload(UploadedFile $file, $fileable = null, string $folder = 'uploads', $visibility='private'): File
    {
        try {
            $disk = env('FILESYSTEM_DISK', 'local');
            if($disk === 'local') {
                $visibility = 'public';
                $disk = 'public';
            }
            $path = $file->store($folder, $disk);
            $url = Storage::disk($disk)->url($path);

            DB::beginTransaction();

            $fileModel = File::create([
                'url' => $url,
                'path' => $path,
                'disk' => $disk,
                'fileable_type' => $fileable ? get_class($fileable) : null,
                'fileable_id' => $fileable?->id,
            ]);

            $fileModel->metadata()->create([
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'type' => strtok($file->getMimeType(), '/'),
                'visibility' =>  $visibility,
            ]);

            DB::commit();

            return $fileModel;

        } catch (\Throwable $e) {
            DB::rollBack();

            // Clean up uploaded file to prevent orphan
            if (isset($path) && Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }

            Log::error('File upload failed', [
                'exception' => $e->getMessage(),
                'path' => $path,
                'file_name' => $file->getClientOriginalName(),
            ]);

            throw $e;
        }
    }

    public function resolveFileable(?string $type, ?int $id): ?Model
    {
        // these are both required
        if (!$type || !$id) {
            return null;
        }

        // this will be a list of models that need to have file upload support
        $map = [
            'category' => \App\Models\Category\Category::class,
        ];

        $key = strtolower(trim($type));

        if (!array_key_exists($key, $map)) {
            throw new \InvalidArgumentException("Invalid fileable_type: '{$type}'.");
        }

        $modelClass = $map[$key];

        $model = $modelClass::find($id);

        if (!$model) {
            throw (new ModelNotFoundException())->setModel($modelClass, $id);
        }

        return $model;
    }
}
