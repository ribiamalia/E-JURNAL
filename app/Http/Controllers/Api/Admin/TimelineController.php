<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Timeline;
use Illuminate\Support\Facades\Validator;

class TimelineController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'user_id'   => 'required|exist:users, id',
            'tanggal'   => 'required',
            'start_time'    => 'required',
            'end_time'  => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $timeline = Timeline::create([
            
        ])
    }
}
