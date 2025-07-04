<?php

namespace App\Http\Controllers;

use App\Notifications\AlertaIncendio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class PublicController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function viewer($id) {
        $str_sql = "SELECT * FROM sch_viewer.peticiones WHERE id = $id";
        $res = DB::select($str_sql);

        $json = '{"view":{"center":[-3.627411,40.007395],"zoom":7},"tools":[],"layers":[{"name":"Mapas Base","type":"group","layers":[{"name":"OpenStreetMap","type":"osmLayer","visible":true}]}]}';
        if (count($res) > 0) {
            $json = $res[0]->json;
        }

        return view('viewer_api',compact('json'));
    }

    public function visor($id) {
        $str_sql = "SELECT * FROM sch_viewer.peticion WHERE id = $id";
        $res = DB::select($str_sql);

        $json = '{"view":{"center":[-3.627411,40.007395],"zoom":7},"tools":[],"layers":[{"name":"Mapas Base","type":"group","layers":[{"name":"OpenStreetMap","type":"osmLayer","visible":true}]}]}';
        if (count($res) > 0) {
            $json = $res[0]->json;
        }

        return view('visorAPI',compact('json'));
    }

    public function visor2D() {
        $json = '{"view":{"center":[-3.627411,40.007395],"zoom":7},"tools":[{"name":"Catálogo"}],"layers":[{"id":6,"name":"Mapas Base","type":"group","layers":[{"id":116,"name":"OpenStreetMap","type":"osmLayer","visible":true}]}]}';

        return view('visor2D',compact('json'));
    }














}
