<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InitialPageController extends Controller
{
    public function index() // exibe a página inicial
    {
        return view('initialPage.index');
    }
}
