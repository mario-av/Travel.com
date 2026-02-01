<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Vacation;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * ImageController - Serves images from storage.
 * Handles both public and private image serving.
 */
class ImageController extends Controller
{
    /**
     * Serve image of vacation by ID.
     *
     * @param int $id The vacation ID.
     * @return BinaryFileResponse The image file response.
     */
    public function view(int $id): BinaryFileResponse
    {
        $vacation = Vacation::find($id);
        $photo = $vacation?->photos->first();

        if (
            $vacation == null || $photo == null ||
            !file_exists(storage_path('app/private') . '/' . $photo->path)
        ) {
            return response()->file(base_path('public/assets/img/noimage.png'));
        }

        return response()->file(storage_path('app/private') . '/' . $photo->path);
    }

    /**
     * Serve specific photo by ID.
     *
     * @param int $id The photo ID.
     * @return BinaryFileResponse The image file response.
     */
    public function photo(int $id): BinaryFileResponse
    {
        $photo = Photo::find($id);

        if (
            $photo == null || $photo->path == null ||
            !file_exists(storage_path('app/private') . '/' . $photo->path)
        ) {
            return response()->file(base_path('public/assets/img/noimage.png'));
        }

        return response()->file(storage_path('app/private') . '/' . $photo->path);
    }
}
