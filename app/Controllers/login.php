<?php

namespace App\Controllers;

use App\Models\loginModel;
use App\Controllers\Home;


class login extends BaseController
{
    public function __construct()
    {
        helper('form');
        $this->loginModel = new loginModel();
    }
    public function index()
    {
        echo view('v_login');
    }

    public function cek_login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $cek =  $this->loginModel->cek_login($username, $password);

        if (($cek['username'] == $username) && ($cek['password'] == $password)) {
            session()->set('username', $cek['username']);
            session()->set('email', $cek['email']);
            session()->set('level', $cek['level']);

            return redirect()->to(base_url('home'));
        } else {
            //jika username pasword salah
            session()->setFlashdata('gagal', 'Password dan username salah');
            return redirect()->to(base_url('login'));
        }
    }
}
 