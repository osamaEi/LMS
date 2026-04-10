<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MediaUploadController extends Controller
{
    /**
     * POST /api/v1/student/upload-images
     *
     * Upload one or multiple images and return their public URLs.
     * Field name: image (single) or images[] (multiple)
     */
    public function upload(Request $request)
    {
        $request->validate([
            'images'   => 'required_without:image|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'image'    => 'required_without:images|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $files = $request->hasFile('images')
            ? $request->file('images')
            : [$request->file('image')];

        $urls = [];

        foreach ($files as $file) {
            $path   = $file->store('uploads/images', 'public');
            $urls[] = asset('storage/' . $path);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'urls'  => $urls,
                'count' => count($urls),
            ],
        ], 201);
    }
}
