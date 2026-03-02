<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Faq;
use App\Models\MediaCoverage;
use App\Models\BonusFile;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller
{
    public function index()
    {
        $reviews = Review::orderBy('sort_order')->get();
        $faqs = Faq::orderBy('sort_order')->get();
        $medias = MediaCoverage::orderBy('sort_order')->get();
        $files = BonusFile::latest()->get();
        $pages = Page::latest()->get();

        return view('admin.content.index', compact('reviews', 'faqs', 'medias', 'files', 'pages'));
    }

    // ===== REVIEWS =====
    public function storeReview(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        $maxOrder = Review::max('sort_order') ?? 0;
        $validated['sort_order'] = $maxOrder + 1;
        $validated['is_active'] = true;

        $review = Review::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil ditambahkan',
            'data' => $review
        ]);
    }

    public function updateReview(Request $request, Review $review)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        $review->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil diupdate',
            'data' => $review
        ]);
    }

    public function destroyReview(Review $review)
    {
        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil dihapus'
        ]);
    }

    public function toggleReviewStatus(Review $review)
    {
        $review->update(['is_active' => !$review->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Status ulasan berhasil diubah',
            'is_active' => $review->is_active
        ]);
    }

    // ===== FAQ =====
    public function storeFaq(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
        ]);

        $maxOrder = Faq::max('sort_order') ?? 0;
        $validated['sort_order'] = $maxOrder + 1;
        $validated['is_active'] = true;

        $faq = Faq::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'FAQ berhasil ditambahkan',
            'data' => $faq
        ]);
    }

    public function updateFaq(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
        ]);

        $faq->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'FAQ berhasil diupdate',
            'data' => $faq
        ]);
    }

    public function destroyFaq(Faq $faq)
    {
        $faq->delete();

        return response()->json([
            'success' => true,
            'message' => 'FAQ berhasil dihapus'
        ]);
    }

    public function toggleFaqStatus(Faq $faq)
    {
        $faq->update(['is_active' => !$faq->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Status FAQ berhasil diubah',
            'is_active' => $faq->is_active
        ]);
    }

    // ===== MEDIA COVERAGE =====
    public function storeMedia(Request $request)
    {
        $validated = $request->validate([
            'media_name' => 'required|string|max:255',
            'url' => 'required|url',
            'logo' => 'nullable|string',
            'logo_file' => 'nullable|image|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo_file')) {
            $validated['logo'] = $request->file('logo_file')->store('media', 'public');
        }
        unset($validated['logo_file']);

        $maxOrder = MediaCoverage::max('sort_order') ?? 0;
        $validated['sort_order'] = $maxOrder + 1;
        $validated['is_active'] = true;

        $media = MediaCoverage::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Media liputan berhasil ditambahkan',
            'data' => $media
        ]);
    }

    public function updateMedia(Request $request, MediaCoverage $media)
    {
        $validated = $request->validate([
            'media_name' => 'required|string|max:255',
            'url' => 'required|url',
            'logo' => 'nullable|string',
            'logo_file' => 'nullable|image|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo_file')) {
            // Delete old logo
            if ($media->logo && Storage::disk('public')->exists($media->logo)) {
                Storage::disk('public')->delete($media->logo);
            }
            $validated['logo'] = $request->file('logo_file')->store('media', 'public');
        }
        unset($validated['logo_file']);

        $media->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Media liputan berhasil diupdate',
            'data' => $media
        ]);
    }

    public function destroyMedia(MediaCoverage $media)
    {
        // Delete logo file
        if ($media->logo && Storage::disk('public')->exists($media->logo)) {
            Storage::disk('public')->delete($media->logo);
        }

        $media->delete();

        return response()->json([
            'success' => true,
            'message' => 'Media liputan berhasil dihapus'
        ]);
    }

    // ===== BONUS FILES =====
    public function storeFile(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file_path' => 'required|string',
            'required_level' => 'required|in:visitor,reseller,reseller_vip,reseller_vvip',
        ]);

        $validated['is_active'] = true;

        $file = BonusFile::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bonus file berhasil ditambahkan',
            'data' => $file
        ]);
    }

    public function updateFile(Request $request, BonusFile $file)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file_path' => 'required|string',
            'required_level' => 'required|in:visitor,reseller,reseller_vip,reseller_vvip',
        ]);

        $file->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bonus file berhasil diupdate',
            'data' => $file
        ]);
    }

    public function destroyFile(BonusFile $file)
    {
        $file->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bonus file berhasil dihapus'
        ]);
    }

    public function toggleFileStatus(BonusFile $file)
    {
        $file->update(['is_active' => !$file->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Status file berhasil diubah',
            'is_active' => $file->is_active
        ]);
    }

    // ===== PAGES (Informasi) =====
    public function storePage(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        // Ensure unique slug
        $counter = 1;
        $originalSlug = $validated['slug'];
        while (Page::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter++;
        }

        $validated['is_active'] = true;

        $page = Page::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Halaman informasi berhasil ditambahkan',
            'data' => $page
        ]);
    }

    public function updatePage(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $page->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Halaman informasi berhasil diupdate',
            'data' => $page
        ]);
    }

    public function destroyPage(Page $page)
    {
        $page->delete();

        return response()->json([
            'success' => true,
            'message' => 'Halaman informasi berhasil dihapus'
        ]);
    }

    public function togglePageStatus(Page $page)
    {
        $page->update(['is_active' => !$page->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Status halaman berhasil diubah',
            'is_active' => $page->is_active
        ]);
    }
}
