<?php

namespace App\Domain\Media\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Media\Services\MediaService;
use App\Domain\Media\DTO\UploadMediaDTO;
use App\Domain\Media\Policies\MediaCollectionPolicy;

use App\Domain\Media\Models\Media;

class MediaController extends Controller
{
    /** @var MediaService */
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * Upload media file
     */
    public function upload(Request $request)
{
    $request->validate([
        'files.*' => ['required', 'file', 'max:10240'],
        'model_type' => ['required', 'string'],
        'model_id' => ['required']
    ]);

    
    $modelClass = $request->get('model_type');
    if (!class_exists($modelClass)) {
        abort(400, 'Invalid model type');
    }

    $allowedModels = [
        \App\Models\User::class,
        \App\Models\Supplier::class,
        \App\Models\Product::class
    ];

    if (!in_array($modelClass, $allowedModels)) {
        abort(403);
    }

    $model = $modelClass::findOrFail(
        $request->get('model_id')
    );

    $collection = $request->get('collection', 'default');

    $allowedMime = MediaCollectionPolicy::allowedMimeTypes($collection);

    if (empty($allowedMime)) {
        abort(422, 'Invalid media collection');
    }

    
    foreach ($request->file('files', []) as $file) {

        if (!in_array($file->getMimeType(), $allowedMime)) {
            abort(422, 'File type not allowed for this collection');
        }

        $maxSize = MediaCollectionPolicy::maxFileSize($collection);

        if ($file->getSize() / 1024 > $maxSize) {
            abort(422, 'File too large');
        }

        $metadata = $this->mediaService->extractMetadata($file);
        $maxWidth = MediaCollectionPolicy::maxWidth($collection);

        if ($maxWidth && ($metadata['width'] ?? 0) > $maxWidth) {
            abort(422, 'Image width too large');
        }

        $maxHeight = MediaCollectionPolicy::maxHeight($collection);

        if ($maxHeight && ($metadata['height'] ?? 0) > $maxHeight) {
            abort(422, 'Image height too large');
        }


        $dto = new UploadMediaDTO(
            file: $file,
            model: $model,
            collection: $request->get('collection', 'default'),
            private: $request->boolean('private', false),
        );

        $this->mediaService->upload($dto);
    }

    return back()->with('success', 'Files uploaded');
}


public function delete($id)
{
    $media = Media::findOrFail($id);

    app(MediaService::class)->delete($media);

    return response()->json([
        'success' => true
    ]);
}



}