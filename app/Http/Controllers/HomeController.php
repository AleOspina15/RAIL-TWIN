<?php

namespace App\Http\Controllers;

use App\Models\TipoMision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {
        return view('home');
    }

    public function visorJSON() {

        $tipos_mision = TipoMision::all()->sortBy('id');

        return view('visorJSON',compact('tipos_mision'));
    }

    public function ueview(){
        return view('ueview');
    }

        public function incendio(){
        return view('incendio');
    }

        public function inundacion(){
        return view('inundacion');
    }




}
