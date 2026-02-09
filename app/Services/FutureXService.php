<?php

namespace App\Services;

use App\Models\{Program, User, Enrollment};
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FutureXService
{
    private string $baseUrl;
    private string $apiKey;
    private string $providerId;

    public function __construct()
    {
        $this->baseUrl = config('services.futurex.api_base_url', '');
        $this->apiKey = config('services.futurex.api_key', '');
        $this->providerId = config('services.futurex.provider_id', '');
    }

    public function registerCourse(Program $program): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/courses', [
                'provider_id' => $this->providerId,
                'name_ar' => $program->name_ar,
                'name_en' => $program->name_en,
                'description_ar' => $program->description_ar,
                'description_en' => $program->description_en,
                'duration_months' => $program->duration_months,
                'price' => $program->price,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $program->update(['futurex_course_id' => $data['course_id']]);
                return $data;
            }

            Log::error('Failed to register course with FutureX', [
                'program_id' => $program->id,
                'status' => $response->status(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exception registering course with FutureX: ' . $e->getMessage());
            return null;
        }
    }

    public function enrollLearner(Enrollment $enrollment): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post($this->baseUrl . '/enrollments', [
                'course_id' => $enrollment->subject->program->futurex_course_id,
                'learner_id' => $enrollment->student->futurex_id,
                'enrollment_date' => $enrollment->created_at->toDateString(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $enrollment->update(['futurex_enrollment_id' => $data['enrollment_id']]);
                return $data;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Exception enrolling learner with FutureX: ' . $e->getMessage());
            return null;
        }
    }

    public function reportProgress(Enrollment $enrollment, float $percentage): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post($this->baseUrl . '/progress', [
                'enrollment_id' => $enrollment->futurex_enrollment_id,
                'progress_percentage' => $percentage,
                'updated_at' => now()->toIso8601String(),
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Exception reporting progress to FutureX: ' . $e->getMessage());
            return false;
        }
    }

    public function requestCertificate(Enrollment $enrollment): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post($this->baseUrl . '/certificates', [
                'enrollment_id' => $enrollment->futurex_enrollment_id,
                'completion_date' => now()->toDateString(),
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Exception requesting certificate from FutureX: ' . $e->getMessage());
            return null;
        }
    }
}
