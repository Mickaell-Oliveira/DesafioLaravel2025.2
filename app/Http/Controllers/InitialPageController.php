<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InitialPageController extends Controller
{
    public function index()
    {
        return view('initialPage.index');
    }
}
