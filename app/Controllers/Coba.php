<?php

namespace App\Controllers;

class Coba extends BaseController
{
    public function index()
    {
        echo 'ini controller coba method index';
    }

    public function about1()
    {
        echo "hello nama saya $this->nama";
    }
    public function about($nama = '')
    {
        echo "hello nama saya $nama";
    }
    //--------------------------------------------------------------------

}
