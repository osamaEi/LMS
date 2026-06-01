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
        $fieldName = $request->hasFile('file') ? 'file' : ($request->hasFile('name') ? 'name' : 'file');

        $request->validate([
            $fieldName => 'required|file|mimes:jpeg,jpg,png,gif,webp,pdf,mp4,mov,avi,mkv,webm|max:51200',
        ], [
            $fieldName . '.required' => 'يرجى اختيار ملف',
            $fieldName . '.mimes'    => 'نوع الملف غير مدعوم. المسموح به: صورة، PDF، فيديو',
            $fieldName . '.max'      => 'حجم الملف لا يتجاوز 50 ميجابايت',
        ]);

        $file = $request->file($fieldName);
        $mime = $file->getMimeType();

        if (str_starts_with($mime, 'video/')) {
            $folder = 'uploads/videos';
        } elseif ($mime === 'application/pdf') {
            $folder = 'uploads/pdfs';
        } else {
            $folder = 'uploads/images';
        }

        $path = $file->store($folder, 'public');

        return response()->json([
            'name' => basename($path),
            'url'  => asset('storage/' . $path),
            'type' => str_starts_with($mime, 'video/') ? 'video' : ($mime === 'application/pdf' ? 'pdf' : 'image'),
        ], 201);
    }
}
