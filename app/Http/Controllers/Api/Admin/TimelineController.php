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



}
