<?php

namespace App\Controllers;

class Pages extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Home | Web Saya'
        ];
        return view('pages/home', $data);
    }
    public function about()
    {
        $data = [
            'title' => 'About Me| Web Saya'
        ];

        return view('pages/about', $data);
    }
    //--------------------------------------------------------------------

}
