<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Jurnal;
use App\Models\School;
use App\Models\Timeline;
use App\Models\User;


class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        //count categories
        $user = User::count();

        //count post
        $school = School::count();

        //count slider
        $timeline = Timeline::count();

        //count user
        $jurnal = Jurnal::count();

        $blog = Blog::count();

    
        //return response json
        return response()->json([
            'succes'    => true,
            'message'   => 'List Data on Dashboard',
            'data'      => [
                'user' => $user,
                'timeline'       => $timeline,
                'sekolah'     => $school,
                'blog'       => $blog,
                'jurnal'       => $jurnal,
                
            ]
        ]);
    }
}
