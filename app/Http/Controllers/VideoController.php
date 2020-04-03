<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class VideoController extends Controller
{
    public function __construct()
    {
        //
    }

    /**
     * @return View
     */
    public function index()
    {
        $videos = [];

        return view('videos')->with(['videos' => $videos]);
    }
}
