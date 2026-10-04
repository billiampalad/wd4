<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use App\Models\ShowcaseImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ShowcaseController extends Controller
{
    /**
     * Menampilkan halaman manajemen galeri showcase gambar humas
     */
    public function index()
    {
        $showcases = ShowcaseImage::latest()->get();
        return view('auth.unit', compact('showcases'));
    }

    /**
     * Menyimpan foto penghargaan baru dari form upload
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'nullable',
            'image.*' => 'image|mimes:jpeg,png,jpg,webp,avif|max:5120',
        ]);

        $uploadedCount = 0;

        if ($request->hasFile('image')) {
            $uploadPath = public_path('uploads/showcase');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true, true);
            }

            $files = is_array($request->file('image')) ? $request->file('image') : [$request->file('image')];

            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '_' . uniqid() . '.' . $extension;
                    $file->move($uploadPath, $filename);

                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $judul = (count($files) === 1 && $request->filled('judul')) ? $request->input('judul') : ($originalName ?: 'Foto Penghargaan Kerjasama');

                    ShowcaseImage::create([
                        'judul' => $judul,
                        'image_path' => 'uploads/showcase/' . $filename,
                    ]);

                    $uploadedCount++;
                }
            }
        }

        return redirect()->back()->with('success', $uploadedCount . ' foto penghargaan kerjasama berhasil diunggah!');
    }

    /**
     * Menghapus foto showcase dari penyimpanan dan database
     */
    public function destroy($id)
    {
        $showcase = ShowcaseImage::findOrFail($id);

        $filePath = public_path($showcase->image_path);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        $showcase->delete();

        return redirect()->back()->with('success', 'Foto galeri berhasil dihapus.');
    }
}
