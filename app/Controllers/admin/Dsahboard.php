<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;

class Dsahboard extends BaseController
{
    public function index()
    {
     $data = [
            'title' => 'Dsahboard',
            'pageName' => 'dsahboard',
            'css' => ['assets/css/admin/dsahboard/index.css'],
            'js' => ['assets/js/dist/admin_dsahboard.bundle.js'],
        ];

       return view('pages/admin/dsahboard/dsahboard', $data);
    }
}