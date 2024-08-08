<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get users
        $users = User::when(request()->search, function($users) {
            $users = $users->where('name', 'like', '%'. request()->search . '%');
        })->with('roles')->with('schools')->latest()->paginate(5);

        //append query string to pagination links
        $users->appends(['search' => request()->search]);

        //return with Api Resource
        return new UserResource(true, 'List Data Users', $users);
    }
    
    public function getUsersByRole($roleId)
    {
        // Cek apakah peran dengan ID tertentu ada
        $role = Role::find($roleId);
    
        if (!$role) {
            return new UserResource(false, 'Role not found', null);
        }
    
        // Ambil pengguna berdasarkan peran
        $users = User::role($role->name)->with('schools');
    
        // Filter pengguna berdasarkan kueri pencarian
        $users = $users->when(request()->search, function ($query) {
            return $query->where('name', 'like', '%' . request()->search . '%');
        });
    
        // Paginasi hasil
        $users = $users->with('roles')->oldest()->paginate(10);
    
        // Menambahkan parameter pencarian ke tautan paginasi
        $users->appends(['search' => request()->search]);
    
        return new UserResource(true, 'Daftar Data Pengguna', $users);
    }

    /**
     * Store a newly created resource in sstorage.
     * 
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'required|unique:users',
            'password'  => 'required|confirmed',
            'password_confirmation' => 'required',
            'nomor_induk' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
          'jurusan'=> 'required',
           'kelas' => 'required',
           'school_id' => 'required|exists:schools,id',
            'gender' => 'required',
           'alamat' => 'required',
          'nama_ortu' => 'required',
          'alamat_ortu'=> 'required',
            'no_hp_ortu'=> 'required',
            'no_hp'=> 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->input('image');
        $image = $request->file('image')->store('users', 'public');
        

        //create user
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'nomor_induk'  => $request->nomor_induk,
            'jurusan' => $request->jurusan,
             'kelas' => $request->kelas,
             'school_id' => $request->school_id,
              'gender' => $request->gender,
             'alamat' => $request->alamat,
            'nama_ortu' => $request->nama_ortu,
            'alamat_ortu' => $request->alamat_ortu,
              'no_hp_ortu' => $request->no_hp_ortu,
              'no_hp' => $request->no_hp,
              'image'   => $image,
            
        ]);

       



        //assign roles to user
        $user->assignRole($request->roles);

        if($user) {
            //return success with Api Resource
            return new UserResource(true, 'Data User Berhasil Disimpan', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'Data User Gagal Disimpan!', null);
    }

    /**
     * Display the specified resource.
     * 
     * @param   int $id
     * @return \Illuminate\Http\Response
     */

     public function show($id)
     {
        $user = User::with('roles')->whereId($id)->first();

        if($user) {
            //return success with Api Resource
            return new UserResource(true, 'Detail Data User!', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'Detail Data User Gagal Ditemukan!', null);
     }

    /**
     * Update the specified resource in storage.
     * 
     * @param \Illuminate\Http\Request  $request
     * @param   int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'required|unique:users,email,'.$user->id,
            'password'  => 'confirmed',
            'nomor_induk' => 'required',
          'jurusan'=> 'required',
           'kelas' => 'required',
           'school_id' => 'required|exists:schools,id',
            'gender' => 'required',
           'alamat' => 'required',
          'nama_ortu' => 'required',
          'alamat_ortu'=> 'required',
            'no_hp_ortu'=> 'required',
            'no_hp'=> 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if($request->password == "") {

            //update user without password
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
                'nomor_induk'  => $request->nomor_induk,
            'jurusan' => $request->jurusan,
             'kelas' => $request->kelas,
             'school_id' => $request->school_id,
              'gender' => $request->gender,
             'alamat' => $request->alamat,
            'nama_ortu' => $request->nama_ortu,
            'alamat_ortu' => $request->alamat_ortu,
              'no_hp_ortu' => $request->no_hp_ortu,
              'no_hp' => $request->no_hp,
              
            ]);

        } else {

            //update user with new password
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => bcrypt($request->password),
                'nomor_induk'  => $request->nomor_induk,
            'jurusan' => $request->jurusan,
             'kelas' => $request->kelas,
             'school_id' => $request->school_id,
              'gender' => $request->gender,
             'alamat' => $request->alamat,
            'nama_ortu' => $request->nama_ortu,
            'alamat_ortu' => $request->alamat_ortu,
              'no_hp_ortu' => $request->no_hp_ortu,
             
            ]);

        }

    

        //assign roles to user
        $user->syncRoles($request->roles);

        if($user) {
            //return succes with Api Resource
            return new UserResource(true, 'Data User Berhasil Diupdate!', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'Data User Gagal Diupdate!', null);
    }

    public function UpdateDokumen(Request $request, $id)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
           
        ]);
          // Temukan submission yang akan diedit
    $user = User::find($id);

    // Jika submission tidak ditemukan, kembalikan respons gagal
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
    }

    if ($request->hasFile('image')) {
        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }
        $user->image = $request->file('image')->store('users', 'public');
        Log::info('Gambar yang diunggah:', ['path' => $user->image]);
    }
    $user->save();

    Log::info('Updated Image:', $user->toArray());

    // Return success response
    return response()->json(['success' => true, 'message' => 'Foto berhasil diperbarui!', 'data' => $user], 200);


    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param   int $id
     * @return \Illuminateg\Http\Response
     */
    
     public function destroy(User $user)
     {
         // Cek apakah user memiliki gambar, jika ada, hapus dari storage
         if ($user->image) {
             Storage::disk('public')->delete($user->image);
         }
         
         // Hapus user
         if($user->delete()) {
             //return success with Api resource
             return new UserResource(true, 'Data User Berhasil Dihapus!', null);
         }
     
         //return failed with Api Resource
         return new UserResource(false, 'Data User Gagal Dihapus!', null);
     }

}
