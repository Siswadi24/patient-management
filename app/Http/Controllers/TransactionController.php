<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = auth()->user()->transactions()
            ->with('categoryTransaction')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('transaction_time', 'desc')
            ->get();

        $categories = auth()->user()->categories()->get();

        // return response()->json($transactions);

        return Inertia::render('transactions/Index', [
            'transactions' => $transactions,
            'categories' => $categories
        ]);
    }

    public function create()
    {
        $categories = auth()->user()->categories()->where('is_active', true)->get();
        return Inertia::render('transactions/Create', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'category_transaction_id' => 'required|exists:category_transactions,id',
                'name_transaction' => 'required|string|max:255',
                'amount_transaction' => 'required|numeric|min:0',
                'description_transaction' => 'nullable|string',
                'transaction_date' => 'required|date',
                'transaction_time' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
                'photo_transaction' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            ];

            $messages = [
                'required' => ':attribute harus diisi.',
                'exists' => ':attribute yang dipilih tidak valid.',
                'numeric' => ':attribute harus berupa angka.',
                'min' => ':attribute tidak boleh kurang dari :min.',
                'date' => ':attribute harus berupa tanggal yang valid.',
                'regex' => ':attribute harus dalam format HH:MM yang valid.',
                'image' => ':attribute harus berupa gambar.',
                'mimes' => ':attribute harus berupa file: :values.',
                'max' => ':attribute tidak boleh lebih dari :max kilobytes.',
            ];

            $attributes = [
                'category_transaction_id' => 'Kategori Transaksi',
                'name_transaction' => 'Nama Transaksi',
                'amount_transaction' => 'Jumlah Transaksi',
                'description_transaction' => 'Deskripsi Transaksi',
                'transaction_date' => 'Tanggal Transaksi',
                'transaction_time' => 'Waktu Transaksi',
                'photo_transaction' => 'Foto Transaksi',
            ];

            $validated = $request->validate($rules, $messages, $attributes);

            // Format date properly (convert from ISO to Y-m-d)
            if (isset($validated['transaction_date'])) {
                $date = \Carbon\Carbon::parse($validated['transaction_date']);
                $validated['transaction_date'] = $date->format('Y-m-d');
            }

            // Format time - ensure it's in correct format and append seconds
            if (isset($validated['transaction_time'])) {
                // Pad hours if needed and append seconds
                $timeParts = explode(':', $validated['transaction_time']);
                $hours = str_pad($timeParts[0], 2, '0', STR_PAD_LEFT);
                $minutes = str_pad($timeParts[1], 2, '0', STR_PAD_LEFT);
                $validated['transaction_time'] = $hours . ':' . $minutes . ':00';
            }

            // Handle file upload if present
            if ($request->hasFile('photo_transaction')) {
                $photo = $request->file('photo_transaction');
                $photoName = time() . '_' . $photo->getClientOriginalName();
                $photoPath = $photo->storeAs('transactions', $photoName, 'public');
                $validated['photo_transaction'] = $photoPath;
            }

            $validated['user_id'] = auth()->id();

            $transaction = auth()->user()->transactions()->create($validated);

            return redirect()->route('user.transactions.index')->with('success', 'Transaksi berhasil ditambahkan!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $transaction = auth()->user()->transactions()->with('categoryTransaction')->findOrFail($id);
        return response()->json($transaction);
    }

    public function edit($id)
    {
        $transaction = auth()->user()->transactions()->with('categoryTransaction')->findOrFail($id);
        $categories = auth()->user()->categories()->where('is_active', true)->get();

        return Inertia::render('transactions/Edit', [
            'transaction' => $transaction,
            'categories' => $categories
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $transaction = auth()->user()->transactions()->findOrFail($id);

            $rules = [
                'category_transaction_id' => 'required|exists:category_transactions,id',
                'name_transaction' => 'required|string|max:255',
                'amount_transaction' => 'required|numeric|min:0',
                'description_transaction' => 'nullable|string',
                'transaction_date' => 'required|date',
                'transaction_time' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
                'photo_transaction' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            ];

            $messages = [
                'required' => ':attribute harus diisi.',
                'exists' => ':attribute yang dipilih tidak valid.',
                'numeric' => ':attribute harus berupa angka.',
                'min' => ':attribute tidak boleh kurang dari :min.',
                'date' => ':attribute harus berupa tanggal yang valid.',
                'regex' => ':attribute harus dalam format HH:MM yang valid.',
                'image' => ':attribute harus berupa gambar.',
                'mimes' => ':attribute harus berupa file: :values.',
                'max' => ':attribute tidak boleh lebih dari :max kilobytes.',
            ];

            $attributes = [
                'category_transaction_id' => 'Kategori Transaksi',
                'name_transaction' => 'Nama Transaksi',
                'amount_transaction' => 'Jumlah Transaksi',
                'description_transaction' => 'Deskripsi Transaksi',
                'transaction_date' => 'Tanggal Transaksi',
                'transaction_time' => 'Waktu Transaksi',
                'photo_transaction' => 'Foto Transaksi',
            ];

            $validated = $request->validate($rules, $messages, $attributes);

            // Format date properly (convert from ISO to Y-m-d)
            if (isset($validated['transaction_date'])) {
                $date = \Carbon\Carbon::parse($validated['transaction_date']);
                $validated['transaction_date'] = $date->format('Y-m-d');
            }

            // Format time - ensure it's in correct format and append seconds
            if (isset($validated['transaction_time'])) {
                // Pad hours if needed and append seconds
                $timeParts = explode(':', $validated['transaction_time']);
                $hours = str_pad($timeParts[0], 2, '0', STR_PAD_LEFT);
                $minutes = str_pad($timeParts[1], 2, '0', STR_PAD_LEFT);
                $validated['transaction_time'] = $hours . ':' . $minutes . ':00';
            }

            // Handle file upload if present
            if ($request->hasFile('photo_transaction')) {
                // Delete old photo if exists
                if ($transaction->photo_transaction && Storage::disk('public')->exists($transaction->photo_transaction)) {
                    Storage::disk('public')->delete($transaction->photo_transaction);
                }

                $photo = $request->file('photo_transaction');
                $photoName = time() . '_' . $photo->getClientOriginalName();
                $photoPath = $photo->storeAs('transactions', $photoName, 'public');
                $validated['photo_transaction'] = $photoPath;
            } else {
                // Remove photo_transaction from validated data if no new file is uploaded
                // This prevents overwriting existing photo with null
                unset($validated['photo_transaction']);
            }

            $transaction->update($validated);

            return redirect()->route('user.transactions.index')->with('success', 'Transaksi berhasil diperbarui!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $transaction = auth()->user()->transactions()->findOrFail($id);

            // Delete photo if exists
            if ($transaction->photo_transaction && \Storage::disk('public')->exists($transaction->photo_transaction)) {
                \Storage::disk('public')->delete($transaction->photo_transaction);
            }

            $transaction->delete();

            return redirect()->route('user.transactions.index')->with('success', 'Transaksi berhasil dihapus!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()]);
        }
    }
}
