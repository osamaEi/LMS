<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZoomController extends Controller
{
    protected $zoomService;

    public function __construct(ZoomService $zoomService)
    {
        $this->zoomService = $zoomService;
    }

    /**
     * Create a Zoom meeting
     */
    public function createMeeting(Request $request)
    {
        try {
            $validated = $request->validate([
                'topic' => 'required|string|max:255',
                'start_time' => 'required|date',
                'duration' => 'integer|min:1',
                'agenda' => 'nullable|string',
                'password' => 'nullable|string|min:1|max:10',
            ]);

            // Prepare meeting data
            $meetingData = [
                'topic' => $validated['topic'],
                'type' => 2, // Scheduled meeting
                'start_time' => \Carbon\Carbon::parse($validated['start_time'])->toIso8601String(),
                'duration' => $validated['duration'] ?? 60,
                'timezone' => 'Asia/Riyadh',
                'agenda' => $validated['agenda'] ?? '',
                'settings' => [
                    'host_video' => true,
                    'participant_video' => true,
                    'join_before_host' => false,
                    'mute_upon_entry' => true,
                    'watermark' => false,
                    'audio' => 'voip',
                    'auto_recording' => 'cloud',
                    'waiting_room' => true,
                ],
            ];

            if (isset($validated['password'])) {
                $meetingData['password'] = $validated['password'];
            }

            $meeting = $this->zoomService->createMeeting($meetingData);

            if (!$meeting) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل إنشاء اجتماع Zoom. يرجى التحقق من إعدادات Zoom API.'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء اجتماع Zoom بنجاح',
                'data' => [
                    'id' => $meeting['id'],
                    'join_url' => $meeting['join_url'],
                    'start_url' => $meeting['start_url'] ?? null,
                    'password' => $meeting['password'] ?? null,
                    'meeting_id' => $meeting['id'],
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Zoom meeting creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الاجتماع: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get meeting details
     */
    public function getMeeting($meetingId)
    {
        try {
            $meeting = $this->zoomService->getMeeting($meetingId);

            if (!$meeting) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على الاجتماع'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $meeting
            ]);
        } catch (\Exception $e) {
            Log::error('Zoom get meeting error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب بيانات الاجتماع'
            ], 500);
        }
    }

    /**
     * Delete a Zoom meeting
     */
    public function deleteMeeting($meetingId)
    {
        try {
            $result = $this->zoomService->deleteMeeting($meetingId);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل حذف الاجتماع'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الاجتماع بنجاح'
            ]);
        } catch (\Exception $e) {
            Log::error('Zoom delete meeting error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الاجتماع'
            ], 500);
        }
    }

    /**
     * Generate Zoom SDK signature for joining meetings
     */
    public function generateSignature(Request $request)
    {
        try {
            $validated = $request->validate([
                'meeting_number' => 'required|string',
                'role' => 'integer|in:0,1', // 0=participant, 1=host
            ]);

            $signature = $this->zoomService->generateSignature(
                $validated['meeting_number'],
                $validated['role'] ?? 0
            );

            if (!$signature) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل إنشاء التوقيع الرقمي. يرجى التحقق من إعدادات Zoom SDK.'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'signature' => $signature
            ]);
        } catch (\Exception $e) {
            Log::error('Zoom signature generation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء التوقيع الرقمي'
            ], 500);
        }
    }
}
