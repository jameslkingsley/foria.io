<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Video $video)
    {
        $trending = $video->trending();
        $feed = $video->feed();
        $recent = $video->recent();

        return vue('f-video-library', compact('trending', 'feed', 'recent'));
    }
}
