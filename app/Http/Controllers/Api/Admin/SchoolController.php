<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School;
use App\Http\Resources\SchoolResource;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class SchoolController extends Controller
{
    /**
     * index
     *
     * @return View
     */
    public function index()
    {
        // Get IdentitasSekolah with count of users
        $identitassekolahs = School::withCount('users')
            ->when(request()->search, function($query) {
                $query->where('nama', 'like', '%'. request()->search .'%');
            })->latest()->paginate(5);

        // Append query string to pagination links
        $identitassekolahs->appends(['search' => request()->search]);

        // Return with Api Resource
        return new SchoolResource(true, 'List Identitas Sekolah', $identitassekolahs);
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /**
         * Validate request
         */
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'alamat' => 'required',
            'nama_pembimbing' => 'required',
            'no_hp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create IdentitasSekolah
        $identitassekolah = School::create([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'nama_pembimbing' => $request->nama_pembimbing,
            'no_hp' => $request->no_hp,
        ]);

        if ($identitassekolah) {
            // Return success with Api Resource
            return new SchoolResource(true, 'Identitas Sekolah Berhasil di Simpan!', $identitassekolah);
        }

        // Return failed with Api Resource
        return new SchoolResource(false, 'Identitas Sekolah Gagal di Simpan!', null);
    }

    /**
     * Show the specified resource.
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Get IdentitasSekolah
        $identitassekolah = School::withCount('users')->findOrFail($id);

        if($identitassekolah) {
            // Return success with Api Resource
            return new SchoolResource(true, 'Detail Identitas Sekolah', $identitassekolah);
        }

        // Return failed with Api Resource
        return new SchoolResource(false, 'Identitas Sekolah Tidak Ditemukan', null);
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param \Illuminate\Http\Request $request
     * @param IdentitasSekolah $identitassekolah
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, School $identitassekolah)
    {
        /**
         * Validate request
         */
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'alamat' => 'required',
            'nama_pembimbing' => 'required',
            'no_hp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update IdentitasSekolah
        $identitassekolah->update([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'nama_pembimbing' => $request->nama_pembimbing,
            'no_hp' => $request->no_hp,
        ]);

        if ($identitassekolah) {
            // Return success with Api Resource
            return new SchoolResource(true, 'Identitas Sekolah Berhasil di Update', $identitassekolah);
        }

        // Return failed with Api Resource
        return new SchoolResource(false, 'Identitas Sekolah Gagal di Update', null);
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
{
    // Find IdentitasSekolah by ID
    $identitassekolah = School::findOrFail($id);

    // Delete associated users
    User::where('school_id', $identitassekolah->id)->delete();

    // Delete IdentitasSekolah
    if($identitassekolah->delete()) {
        // Return success with Api Resource
        return new SchoolResource(true, 'Identitas Sekolah Berhasil di Hapus!', null);
    }

    // Return failed with Api Resource
    return new SchoolResource(false, 'Identitas Sekolah Gagal di Hapus!', null);
}

    /**
     * All
     * 
     * @return void
     */
    public function all()
    {
        // Get all IdentitasSekolah
        $identitassekolah =School::withCount('users')->latest()->get();

        // Return with Api Resource
        return new SchoolResource(true, 'List Identitas Sekolah', $identitassekolah);
    }
}
