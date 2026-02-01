<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

/**
 * ImageController - Serves private images from storage.
 * Used for vacation photos stored in private storage.
 */
class ImageController extends Controller
{
    /**
     * Serve a private image from storage.
     *
     * @param string $path The path to the image file.
     * @return Response The image response.
     */
    public function show(string $path): Response
    {
        try {
            $fullPath = 'vacations/' . $path;

            if (!Storage::disk('public')->exists($fullPath)) {
                abort(404, 'Image not found.');
            }

            $file = Storage::disk('public')->get($fullPath);
            $mimeType = Storage::disk('public')->mimeType($fullPath);

            return response($file, 200)->header('Content-Type', $mimeType);
        } catch (\Exception $e) {
            abort(500, 'Error loading image.');
        }
    }
}
