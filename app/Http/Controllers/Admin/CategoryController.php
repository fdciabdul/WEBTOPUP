<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        // Sorting
        $sortField = $request->get('sort', 'sort_order');
        $sortDir = $request->get('dir', 'asc');
        $allowedSorts = ['sort_order', 'name', 'slug', 'is_active', 'products_count'];

        if (in_array($sortField, $allowedSorts)) {
            if ($sortField === 'products_count') {
                $query->withCount('products')->orderBy('products_count', $sortDir);
            } else {
                $query->orderBy($sortField, $sortDir);
            }
        } else {
            $query->orderBy('sort_order', 'asc');
        }

        $perPage = $request->get('per_page', 10);
        $categories = $query->withCount('products')->paginate($perPage)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Toggle category status (AJAX)
     */
    public function toggleStatus(Category $category)
    {
        $category->update([
            'is_active' => !$category->is_active
        ]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status kategori berhasil diubah',
                'is_active' => $category->is_active
            ]);
        }

        return back()->with('success', 'Status kategori berhasil diubah');
    }

    /**
     * Update category field inline (AJAX)
     */
    public function updateInline(Request $request, Category $category)
    {
        $field = $request->input('field');
        $value = $request->input('value');

        $allowedFields = ['sort_order', 'slug'];

        if (!in_array($field, $allowedFields)) {
            return response()->json(['success' => false, 'message' => 'Field tidak diizinkan'], 422);
        }

        // Validate based on field
        if ($field === 'sort_order') {
            $validated = $request->validate(['value' => 'required|integer|min:0']);
            $category->update(['sort_order' => (int) $value]);
        } elseif ($field === 'slug') {
            $slug = Str::slug($value);
            // Check unique
            $exists = Category::where('slug', $slug)->where('id', '!=', $category->id)->exists();
            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Slug sudah digunakan'], 422);
            }
            $category->update(['slug' => $slug]);
        }

        return response()->json([
            'success' => true,
            'message' => ucfirst($field) . ' berhasil diupdate',
            'value' => $category->$field
        ]);
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug',
            'icon' => 'nullable|string|max:255',
            'icon_file' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle icon - prefer uploaded file over remix icon class
        if ($request->hasFile('icon_file')) {
            $validated['icon'] = $request->file('icon_file')->store('categories', 'public');
        }
        unset($validated['icon_file']);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    public function show(Category $category)
    {
        $category->load('products');
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $category->loadCount('products');
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug,' . $category->id,
            'icon' => 'nullable|string|max:255',
            'icon_file' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle icon - prefer uploaded file over remix icon class
        if ($request->hasFile('icon_file')) {
            // Delete old icon file if exists
            if ($category->icon && !str_starts_with($category->icon, 'ri-') && \Storage::disk('public')->exists($category->icon)) {
                \Storage::disk('public')->delete($category->icon);
            }
            $validated['icon'] = $request->file('icon_file')->store('categories', 'public');
        }
        unset($validated['icon_file']);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diupdate');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Tidak bisa menghapus kategori yang memiliki produk');
        }

        // Delete icon file if exists
        if ($category->icon && !str_starts_with($category->icon, 'ri-') && \Storage::disk('public')->exists($category->icon)) {
            \Storage::disk('public')->delete($category->icon);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus');
    }
}
