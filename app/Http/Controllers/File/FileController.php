<?php

namespace App\Http\Controllers\File;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Services\FileService;
use App\Models\File\File;
use App\Http\Requests\File\UploadFileRequest;
use App\Http\Resources\File\FileCollection;
use App\Http\Resources\File\FileResource;

class FileController extends Controller
{
    public function store(UploadFileRequest $request, FileService $fileService)
    {
        try {
            // this determines which table we are uploading the file for
            $fileable = $fileService->resolveFileable(
                $request->input('fileable_type'),
                $request->input('fileable_id')
            );

            $files = [];

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $files[] = $fileService->upload($file, $fileable);
                }
            } else {
                $files[] = $fileService->upload($request->file('file'), $fileable);
            }

            return response()->success(
                count($files) === 1
                    ? new FileResource($files[0])
                    : FileResource::collection($files),
                201,
                count($files) . ' File' . (count($files) > 1 ? 's' : '') . ' uploaded successfully.'
            );

        } catch (\Throwable $e) {
            report($e);
            return response()->error('File upload failed.', 500, $e->getMessage());
        }
    }
}
