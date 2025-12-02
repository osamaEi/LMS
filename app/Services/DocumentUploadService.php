<?php

namespace App\Services;

use App\Models\StudentDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DocumentUploadService
{
    /**
     * Upload and store a document for a user
     */
    public function uploadDocument(User $user, UploadedFile $file, string $documentType): StudentDocument
    {
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Store file in storage/app/public/documents/{user_id}/{document_type}
        $path = $file->storeAs(
            "documents/{$user->id}/{$documentType}",
            $filename,
            'public'
        );

        // Create or update document record
        $document = StudentDocument::updateOrCreate(
            [
                'user_id' => $user->id,
                'document_type' => $documentType,
            ],
            [
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'status' => 'pending',
            ]
        );

        return $document;
    }

    /**
     * Delete a document file from storage
     */
    public function deleteDocument(StudentDocument $document): bool
    {
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        return $document->delete();
    }

    /**
     * Get document URL
     */
    public function getDocumentUrl(StudentDocument $document): string
    {
        return Storage::disk('public')->url($document->file_path);
    }
}
