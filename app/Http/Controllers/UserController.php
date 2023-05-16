<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class UserController extends Controller
{
    function index(){
        return view('LandingPage', ['title'=>'Home']);
    }

    function riwayat_konsultasi() {
        $konsultasi = DB::table('konsultasi as a')
                    ->join('users as b', 'a.id_dokter', '=', 'b.id')
                    ->join('users as c', 'a.id_pasien', '=', 'c.id')
                    ->join('dokters as d', 'b.email', '=', 'd.email')
                    ->select('a.*', 'b.name as nama_dokter', 'c.name as nama_pasien', 'd.spesialis')
                    ->where('a.id_pasien', '=', Auth::user()->id)
                    ->get();
        return view('riwayat-konsultasi-pasien-view', compact('konsultasi'));
    }
}
