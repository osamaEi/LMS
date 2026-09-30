<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        $query = Offer::with(['programs', 'creator']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('program_id')) {
            $query->whereHas('programs', fn($q) => $q->where('programs.id', $request->program_id));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('title_ar', 'like', "%{$search}%")
                                      ->orWhere('title_en', 'like', "%{$search}%")
                                      ->orWhere('code', 'like', "%{$search}%"));
        }

        $offers   = $query->orderBy('created_at', 'desc')->paginate(12);
        $programs = Program::orderBy('name_ar')->get();

        $stats = [
            'total'    => Offer::count(),
            'active'   => Offer::active()->count(),
            'expired'  => Offer::expired()->count(),
            'upcoming' => Offer::upcoming()->count(),
        ];

        return view('admin.offers.index', compact('offers', 'programs', 'stats'));
    }

    public function create()
    {
        $programs = Program::orderBy('name_ar')->get();
        return view('admin.offers.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $isOverride = $request->input('discount_type') === 'override';

        $data = $request->validate([
            'title_ar'       => 'required|string|max:255',
            'title_en'       => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'code'           => 'nullable|string|max:50|unique:offers,code',
            'discount_type'  => 'required|in:percentage,fixed,override',
            'discount_value' => $isOverride ? 'nullable|numeric|min:0' : 'required|numeric|min:0.01',
            'offer_price'    => $isOverride ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'program_ids'    => 'nullable|array',
            'program_ids.*'  => 'integer|exists:programs,id',
            'start_date'     => 'required|date',
            'end_date'       => $request->boolean('no_end_date') ? 'nullable' : 'required|date|after_or_equal:start_date',
            'max_uses'       => 'nullable|integer|min:1',
            'status'         => 'required|in:active,inactive',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'video_url'      => 'nullable|url|max:500',
            'video_file'     => 'nullable|file|mimes:mp4,webm,mov|max:102400',
        ], [
            'title_ar.required'       => 'العنوان بالعربية مطلوب',
            'discount_value.required' => 'قيمة الخصم مطلوبة',
            'offer_price.required'    => 'سعر العرض المباشر مطلوب عند اختيار هذا النوع',
            'video_url.url'           => 'رابط الفيديو غير صالح',
            'video_file.mimes'        => 'صيغة الفيديو يجب أن تكون MP4 أو WebM أو MOV',
            'video_file.max'          => 'حجم الفيديو يجب ألا يتجاوز 100MB',
            'start_date.required'     => 'تاريخ البداية مطلوب',
            'end_date.required'       => 'تاريخ الانتهاء مطلوب — أو فعّل خيار «بدون تاريخ انتهاء»',
            'end_date.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البداية',
            'code.unique'             => 'كود العرض مستخدم مسبقاً',
        ]);

        if ($request->boolean('no_end_date')) $data['end_date'] = null;

        // discount_value column is NOT NULL; override offers use offer_price instead
        $data['discount_value'] = $data['discount_value'] ?? 0;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('offers', 'public');
        }

        unset($data['video_file']);
        if ($request->hasFile('video_file')) {
            $data['video_path'] = $request->file('video_file')->store('offers/videos', 'public');
            $data['video_url']  = null;
        }

        $data['created_by'] = auth()->id();

        if (empty($data['code'])) {
            $data['code'] = strtoupper(Str::random(8));
        } else {
            $data['code'] = strtoupper($data['code']);
        }

        $programIds = array_values(array_unique($data['program_ids'] ?? []));
        unset($data['program_ids']);
        $data['program_id'] = $programIds[0] ?? null; // legacy single-program column

        $offer = Offer::create($data);
        $offer->programs()->sync($programIds);

        return redirect()->route('admin.offers.index')
            ->with('success', 'تم إنشاء العرض بنجاح');
    }

    public function show(Offer $offer)
    {
        $offer->load(['programs', 'creator']);
        return view('admin.offers.show', compact('offer'));
    }

    public function edit(Offer $offer)
    {
        $programs = Program::orderBy('name_ar')->get();
        return view('admin.offers.edit', compact('offer', 'programs'));
    }

    public function update(Request $request, Offer $offer)
    {
        $isOverride = $request->input('discount_type') === 'override';

        $data = $request->validate([
            'title_ar'       => 'required|string|max:255',
            'title_en'       => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'code'           => 'nullable|string|max:50|unique:offers,code,' . $offer->id,
            'discount_type'  => 'required|in:percentage,fixed,override',
            'discount_value' => $isOverride ? 'nullable|numeric|min:0' : 'required|numeric|min:0.01',
            'offer_price'    => $isOverride ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'program_ids'    => 'nullable|array',
            'program_ids.*'  => 'integer|exists:programs,id',
            'start_date'     => 'required|date',
            'end_date'       => $request->boolean('no_end_date') ? 'nullable' : 'required|date|after_or_equal:start_date',
            'max_uses'       => 'nullable|integer|min:1',
            'status'         => 'required|in:active,inactive',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'video_url'      => 'nullable|url|max:500',
            'video_file'     => 'nullable|file|mimes:mp4,webm,mov|max:102400',
        ], [
            'title_ar.required'       => 'العنوان بالعربية مطلوب',
            'discount_value.required' => 'قيمة الخصم مطلوبة',
            'offer_price.required'    => 'سعر العرض المباشر مطلوب عند اختيار هذا النوع',
            'video_url.url'           => 'رابط الفيديو غير صالح',
            'video_file.mimes'        => 'صيغة الفيديو يجب أن تكون MP4 أو WebM أو MOV',
            'video_file.max'          => 'حجم الفيديو يجب ألا يتجاوز 100MB',
            'end_date.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البداية',
            'code.unique'             => 'كود العرض مستخدم مسبقاً',
        ]);

        if ($request->boolean('no_end_date')) $data['end_date'] = null;

        // discount_value column is NOT NULL; override offers use offer_price instead
        $data['discount_value'] = $data['discount_value'] ?? 0;

        if ($request->hasFile('image')) {
            if ($offer->image) Storage::disk('public')->delete($offer->image);
            $data['image'] = $request->file('image')->store('offers', 'public');
        }

        unset($data['video_file']);
        if ($request->hasFile('video_file')) {
            if ($offer->video_path) Storage::disk('public')->delete($offer->video_path);
            $data['video_path'] = $request->file('video_file')->store('offers/videos', 'public');
            $data['video_url']  = null;
        } elseif ($request->boolean('remove_video')) {
            if ($offer->video_path) Storage::disk('public')->delete($offer->video_path);
            $data['video_path'] = null;
            $data['video_url']  = null;
        } elseif (!empty($data['video_url']) && $offer->video_path) {
            // a new link replaces the previously uploaded file
            Storage::disk('public')->delete($offer->video_path);
            $data['video_path'] = null;
        }

        if (!empty($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        $programIds = array_values(array_unique($data['program_ids'] ?? []));
        unset($data['program_ids']);
        $data['program_id'] = $programIds[0] ?? null; // legacy single-program column

        $offer->update($data);
        $offer->programs()->sync($programIds);

        return redirect()->route('admin.offers.index')
            ->with('success', 'تم تحديث العرض بنجاح');
    }

    public function destroy(Offer $offer)
    {
        if ($offer->image) Storage::disk('public')->delete($offer->image);
        if ($offer->video_path) Storage::disk('public')->delete($offer->video_path);
        $offer->delete();

        return redirect()->route('admin.offers.index')
            ->with('success', 'تم حذف العرض بنجاح');
    }

    public function toggleStatus(Offer $offer)
    {
        $offer->update(['status' => $offer->status === 'active' ? 'inactive' : 'active']);
        return back()->with('success', 'تم تغيير حالة العرض');
    }
}
