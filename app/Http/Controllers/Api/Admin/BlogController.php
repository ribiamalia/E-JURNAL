<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Http\Resources\BlogResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
{
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'judul' => 'required', 
            'konten'    => 'required',  
            'dokumen'   => 'nullable|file|mimes:jpg,jpeg,png,pdf',



        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $dokumen = null;
        if ($request->hasFile('dokumen')) {
            $dokumen = $request->file('dokumen')->store('blogs', 'public');
        }

        $siswa = Auth::user();

        $blog = Blog::create([
            'judul' => $request->judul,
            'konten' => $request->konten,
            'slug' => Str::slug($request->judul, '-'),
            'dokumen' => $dokumen,
            'user_id' => $siswa->id
        ]);

        if ($blog) {
            // Return success with Api Resource
            return new BlogResource(true, 'Blog baru berhasil disimpan', $blog);
        }

        // Return failure response if something went wrong
        return new BlogResource(false, 'Blog gagal disimpan', null);
    }

    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'judul' => 'required', 
            'konten' => 'required',  
            
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        $blog = Blog::findOrFail($id);
    
    
        $blog->update([
            'judul' => $request->judul,
            'konten' => $request->konten,
            'slug' => Str::slug($request->judul, '-'),
           
        ]);
    
        if ($blog) {
            // Return success with Api Resource
            return new BlogResource(true, 'Blog berhasil diperbarui', $blog);
        }
    
        // Return failure response if something went wrong
        return new BlogResource(false, 'Blog gagal diperbarui', null);
    }


    public function UpdateDokumen(Request $request, $id)
    {
        $request->validate([
            'dokumen' => 'nullable|file|mimes:jpeg,png,jpg,pdf',
           
        ]);
          // Temukan submission yang akan diedit
    $blog = Blog::find($id);

    // Jika submission tidak ditemukan, kembalikan respons gagal
    if (!$blog) {
        return response()->json(['success' => false, 'message' => 'Blog tidak ditemukan.'], 404);
    }

    if ($request->hasFile('dokumen')) {
        if ($blog->dokumen) {
            Storage::disk('public')->delete($blog->dokumen);
        }
        $blog->dokumen = $request->file('dokumen')->store('blogs', 'public');
        Log::info('Dokumen yang diunggah:', ['path' => $blog->dokumen]);
    }
    $blog->save();

    Log::info('Updated dokumen:', $blog->toArray());

    // Return success response
    return response()->json(['success' => true, 'message' => 'Dokumen berhasil diperbarui!', 'data' => $blog], 200);


    }


    
    public function show($id)
    {
        $blogs = Blog::find($id);

        if($blogs) {
            //return succes with Api Resource
            return new BlogResource(true, 'Detail blog!', $blogs);
        }

        //return failed with Api Resource
        return new BlogResource(false, 'Detail blog Tidak Ditemukan!', null);
    }

    public function index()
    {
        // Mendapatkan daftar academic programs dari database
        $blogs = Blog::when(request()->search, function($query) {
            // Jika ada parameter pencarian (search) di URL
            // Maka tambahkan kondisi WHERE untuk mencari academic programs berdasarkan nama
            $query->where('judul', 'like', '%' . request()->search . '%');
        })->oldest() // Mengurutkan academic programs dari yang terbaru
        ->paginate(5); // Membuat paginasi dengan 5 item per halaman

        // Menambahkan parameter pencarian ke URL pada hasil paginasi
        $blogs->appends(['search' => request()->search]);

        // Mengembalikan response dalam bentuk AcademicProgramResource (asumsi resource sudah didefinisikan)
        return new BlogResource(true, 'List Data Blog', $blogs);
    }

    public function destroy($id)
    {
       //find role by id
       $blog = Blog::findOrFail($id);

       if($blog->delete()) {
           return new BlogResource(true, 'Data blog Berhasil Dihapus', null);
       }

       return new BlogResource(false, 'Data blog Gagal Dihapus!', null);

    }



}
