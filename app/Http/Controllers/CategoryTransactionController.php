<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class CategoryTransactionController extends Controller
{
    public function index()
    {
        $categories = auth()->user()->categories()->get();
        // dd($categories);
        return Inertia::render('categories/Index', [
            'categories' => $categories
        ]);
    }

    public function create()
    {
        return Inertia::render('categories/Create');
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'name_category' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_active' => 'required|boolean',
            ];

            $messages = [
                'required' => ':attribute harus diisi.',
                'boolean' => ':attribute harus berupa true atau false.',
                'string' => ':attribute harus berupa teks.',
                'max' => ':attribute tidak boleh lebih dari :max karakter.',
            ];

            $attributes = [
                'name_category' => 'Nama Kategori',
                'description' => 'Deskripsi Kategori',
                'is_active' => 'Status Kategori',
            ];

            $validated = $request->validate($rules, $messages, $attributes);

            $validated['slug'] = Str::slug($validated['name_category']);

            $category = auth()->user()->categories()->create($validated);

            return redirect()->route('user.categories.index')->with('success', 'Kategori berhasil ditambahkan!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $category = auth()->user()->categories()->findOrFail($id);
        return response()->json($category);
    }

    public function edit($id)
    {
        $category = auth()->user()->categories()->findOrFail($id);
        return Inertia::render('categories/Edit', [
            'category' => $category
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $category = auth()->user()->categories()->findOrFail($id);
            
            $rules = [
                'name_category' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_active' => 'required|boolean',
            ];

            $messages = [
                'required' => ':attribute harus diisi.',
                'boolean' => ':attribute harus berupa true atau false.',
                'string' => ':attribute harus berupa teks.',
                'max' => ':attribute tidak boleh lebih dari :max karakter.',
            ];

            $attributes = [
                'name_category' => 'Nama Kategori',
                'description' => 'Deskripsi Kategori',
                'is_active' => 'Status Kategori',
            ];

            $validated = $request->validate($rules, $messages, $attributes);
            $validated['slug'] = Str::slug($validated['name_category']);
            
            $category->update($validated);

            return redirect()->route('user.categories.index')->with('success', 'Kategori berhasil diperbarui!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        // Hapus kategori
    }
}
