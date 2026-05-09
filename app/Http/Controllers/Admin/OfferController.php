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
        $query = Offer::with(['program', 'creator']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('title_ar', 'like', "%{$search}%")
                                      ->orWhere('title_en', 'like', "%{$search}%")
                                      ->orWhere('code', 'like', "%{$search}%"));
        }

        $offers   = $query->orderBy('created_at', 'desc')->paginate(12);
        $programs = Program::where('status', 'active')->orderBy('name_ar')->get();

        $stats = [
            'total'    => Offer::count(),
            'active'   => Offer::active()->count(),
            'expired'  => Offer::where('end_date', '<', now())->count(),
            'upcoming' => Offer::upcoming()->count(),
        ];

        return view('admin.offers.index', compact('offers', 'programs', 'stats'));
    }

    public function create()
    {
        $programs = Program::where('status', 'active')->orderBy('name_ar')->get();
        return view('admin.offers.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title_ar'       => 'required|string|max:255',
            'title_en'       => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'code'           => 'nullable|string|max:50|unique:offers,code',
            'discount_type'  => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0.01',
            'program_id'     => 'nullable|exists:programs,id',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after_or_equal:start_date',
            'max_uses'       => 'nullable|integer|min:1',
            'status'         => 'required|in:active,inactive',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'title_ar.required'    => 'العنوان بالعربية مطلوب',
            'discount_value.required' => 'قيمة الخصم مطلوبة',
            'start_date.required'  => 'تاريخ البداية مطلوب',
            'end_date.required'    => 'تاريخ الانتهاء مطلوب',
            'end_date.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البداية',
            'code.unique'          => 'كود العرض مستخدم مسبقاً',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('offers', 'public');
        }

        $data['created_by'] = auth()->id();

        if (empty($data['code'])) {
            $data['code'] = strtoupper(Str::random(8));
        } else {
            $data['code'] = strtoupper($data['code']);
        }

        Offer::create($data);

        return redirect()->route('admin.offers.index')
            ->with('success', 'تم إنشاء العرض بنجاح');
    }

    public function show(Offer $offer)
    {
        $offer->load(['program', 'creator']);
        return view('admin.offers.show', compact('offer'));
    }

    public function edit(Offer $offer)
    {
        $programs = Program::where('status', 'active')->orderBy('name_ar')->get();
        return view('admin.offers.edit', compact('offer', 'programs'));
    }

    public function update(Request $request, Offer $offer)
    {
        $data = $request->validate([
            'title_ar'       => 'required|string|max:255',
            'title_en'       => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'code'           => 'nullable|string|max:50|unique:offers,code,' . $offer->id,
            'discount_type'  => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0.01',
            'program_id'     => 'nullable|exists:programs,id',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after_or_equal:start_date',
            'max_uses'       => 'nullable|integer|min:1',
            'status'         => 'required|in:active,inactive',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'title_ar.required'    => 'العنوان بالعربية مطلوب',
            'discount_value.required' => 'قيمة الخصم مطلوبة',
            'end_date.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البداية',
            'code.unique'          => 'كود العرض مستخدم مسبقاً',
        ]);

        if ($request->hasFile('image')) {
            if ($offer->image) Storage::disk('public')->delete($offer->image);
            $data['image'] = $request->file('image')->store('offers', 'public');
        }

        if (!empty($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        $offer->update($data);

        return redirect()->route('admin.offers.index')
            ->with('success', 'تم تحديث العرض بنجاح');
    }

    public function destroy(Offer $offer)
    {
        if ($offer->image) Storage::disk('public')->delete($offer->image);
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
