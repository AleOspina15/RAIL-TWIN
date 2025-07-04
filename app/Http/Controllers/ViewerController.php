<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ViewerController extends Controller{
    
    public function index(): View {
        return view('viewer');
    }
}