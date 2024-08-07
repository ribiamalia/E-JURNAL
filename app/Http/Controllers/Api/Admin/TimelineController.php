<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TimelineResource;
use Illuminate\Http\Request;
use App\Models\Timeline;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Type\Time;

class TimelineController extends Controller
{
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name'        => 'required',
        'user_id'     => 'required|exists:users,id', // Perbaikan rule validator
        'tanggal'     => 'required|date', // Tambahkan validasi format tanggal jika diperlukan
        'start_time'  => 'required|date_format:H:i:s', // Tambahkan validasi format waktu jika diperlukan
        'end_time'    => 'required|date_format:H:i:s'  // Tambahkan validasi format waktu jika diperlukan
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // Create Timeline
    $timeline = Timeline::create([
        'name'       => $request->name,
        'user_id'    => $request->user_id,
        'tanggal'    => $request->tanggal,
        'start_time' => $request->start_time,
        'end_time'   => $request->end_time
    ]);

    // Return success response
    if ($timeline) {
        return new TimelineResource(true, 'Data Timeline berhasil ditambahkan', $timeline);
    }

    // Return failure response
    return new TimelineResource(false, 'Data Timeline gagal ditambahkan', null);
}

public function index()
{
    // Ambil data dengan mengelompokkan berdasarkan 'user_id'
    $timelines = Timeline::select('user_id')
        ->with('users') // Jika ingin memuat relasi user, bisa ditambahkan
        ->get()
        ->groupBy('user_id');

    // Mengubah data ke dalam format yang diinginkan
    $data = $timelines->map(function($groupedTimelines, $userId) {
        return [
            'user_id' => $userId,
            'timelines' => $groupedTimelines->map(function($timeline) {
                return [
                    'id' => $timeline->id,
                    'name' => $timeline->name,
                    'tanggal' => $timeline->tanggal,
                    'start_time' => $timeline->start_time,
                    'end_time' => $timeline->end_time,
                    'created_at' => $timeline->created_at,
                    'updated_at' => $timeline->updated_at
                ];
            })
        ];
    });
    // Return response
    return response()->json([
        'success' => true,
        'message' => 'Data Timeline berhasil diambil',
        'data'    => $data
    ]);
}

public function update(Request $request, $id)
{
    // Validasi data yang diterima
    $validator = Validator::make($request->all(), [
        'name'        => 'required',
        'user_id'     => 'required|exists:users,id',
        'tanggal'     => 'required|date',
        'start_time'  => 'required|date_format:H:i:s',
        'end_time'    => 'required|date_format:H:i:s'
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // Cari entri Timeline berdasarkan ID
    $timeline = Timeline::find($id);

    // Cek apakah entri ditemukan
    if (!$timeline) {
        return response()->json([
            'success' => false,
            'message' => 'Data Timeline tidak ditemukan'
        ], 404);
    }

    // Perbarui entri Timeline dengan data baru
    $timeline->update([
        'name'       => $request->name,
        'user_id'    => $request->user_id,
        'tanggal'    => $request->tanggal,
        'start_time' => $request->start_time,
        'end_time'   => $request->end_time
    ]);

    // Kembalikan respons sukses
    return new TimelineResource(true, 'Data Timeline berhasil diperbarui', $timeline);
}

public function destroy(Timeline $timeline)
    {
        if($timeline->delete()) {
            //return success with Api resource
            return new TimelineResource(true, 'Timeline Berhasil Dihapus!', null);
        }

        //return failed with Api Resource
        return new TimelineResource(false, 'Timeline Gagal Dihapus!', null);
    }

    public function show($id)
    {
        $timeline = Timeline::find($id);

        if($timeline) {
            //return succes with Api Resource
            return new TimelineResource(true, 'Detail Timeline!', $timeline);
        }

        //return failed with Api Resource
        return new TimelineResource(false, 'Timeline Tidak Ditemukan!', null);
    }





}
