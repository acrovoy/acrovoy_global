<?php

namespace App\Domain\Media\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Media\Services\MediaService;
use App\Domain\Media\DTO\UploadMediaDTO;

class MediaController extends Controller
{
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
    ]);

    foreach ($request->file('files', []) as $file) {

        $dto = new UploadMediaDTO(
            file: $file,
            user: $request->user(),
            collection: $request->get('collection', 'default'),
            private: $request->boolean('private', false),
        );

        $this->mediaService->upload($dto);
    }

    return back()->with('success', 'Files uploaded');
}
}