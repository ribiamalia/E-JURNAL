<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Jurnal;
use App\Http\Resources\JurnalResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class JurnalController extends Controller
{
    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
            'nama_projek' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'prioritas' => 'required|string|max:255',
            'deadline' => 'nullable|date',
            'dokumen' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $dokumen = null;
        if ($request->hasFile('dokumen')) {
            $dokumen = $request->file('dokumen')->store('jurnals', 'public');
        }

        $siswa = Auth::user();

        $jurnal = Jurnal::create([
            'nama_projek' => $request->nama_projek,
            'status' => $request->status,
            'prioritas' => $request->prioritas,
            'deadline' => $request->deadline,
            'dokumen' => $dokumen,
            'user_id' => $siswa->id
        ]);

        if ($jurnal) {
            // Return success with Api Resource
            return new JurnalResource(true, 'Data Jurnal berhasil disimpan', $jurnal);
        }

        // Return failure response if something went wrong
        return new JurnalResource(false, 'Data Jurnal gagal disimpan', null);
    }

    public function show($id)
    {
        $jurnal = Jurnal::find($id);

        if($jurnal) {
            //return succes with Api Resource
            return new JurnalResource(true, 'Detail jurnal!', $jurnal);
        }

        //return failed with Api Resource
        return new JurnalResource(false, 'Detail Jurnal Tidak Ditemukan!', null);
    }

    public function index()
    {
        // Mendapatkan daftar academic programs dari database
        $jurnal = Jurnal::when(request()->search, function($query) {
            // Jika ada parameter pencarian (search) di URL
            // Maka tambahkan kondisi WHERE untuk mencari academic programs berdasarkan nama
            $query->where('nama_projek', 'like', '%' . request()->search . '%');
        })->oldest() // Mengurutkan academic programs dari yang terbaru
        ->paginate(5); // Membuat paginasi dengan 5 item per halaman

        // Menambahkan parameter pencarian ke URL pada hasil paginasi
        $jurnal->appends(['search' => request()->search]);

        // Mengembalikan response dalam bentuk AcademicProgramResource (asumsi resource sudah didefinisikan)
        return new JurnalResource(true, 'List Data jurnal', $jurnal);
    }

    public function destroy($id)
    {
       //find role by id
       $jurnal = Jurnal::findOrFail($id);

       if($jurnal->delete()) {
           return new JurnalResource(true, 'Data jurnal Berhasil Dihapus', null);
       }

       return new JurnalResource(false, 'Data jurnal Gagal Dihapus!', null);

    }

    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'nama_projek' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'prioritas' => 'required|string|max:255',
            'deadline' => 'nullable|date',
            
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        $jurnal = Jurnal::findOrFail($id);
    
    
        $jurnal->update([
            'nama_projek' => $request->nama_projek,
            'status' => $request->status,
            'prioritas' => $request->prioritas,
            'deadline' => $request->deadline,
           
        ]);
    
        if ($jurnal) {
            // Return success with Api Resource
            return new JurnalResource(true, 'Jurnal berhasil diperbarui', $jurnal);
        }
    
        // Return failure response if something went wrong
        return new jurnalResource(false, 'Blog gagal diperbarui', null);
    }


    public function UpdateDokumen(Request $request, $id)
    {
        $request->validate([
            'dokumen' => 'nullable|file|mimes:jpeg,png,jpg,pdf',
           
        ]);
          // Temukan submission yang akan diedit
    $jurnal = Jurnal::find($id);

    // Jika submission tidak ditemukan, kembalikan respons gagal
    if (!$jurnal) {
        return response()->json(['success' => false, 'message' => 'jurnal tidak ditemukan.'], 404);
    }

    if ($request->hasFile('dokumen')) {
        if ($jurnal->dokumen) {
            Storage::disk('public')->delete($jurnal->dokumen);
        }
        $jurnal->dokumen = $request->file('dokumen')->store('jurnals', 'public');
        Log::info('Dokumen yang diunggah:', ['path' => $jurnal->dokumen]);
    }
    $jurnal->save();

    Log::info('Updated dokumen:', $jurnal->toArray());

    // Return success response
    return response()->json(['success' => true, 'message' => 'Dokumen berhasil diperbarui!', 'data' => $jurnal], 200);


    }


    

}
