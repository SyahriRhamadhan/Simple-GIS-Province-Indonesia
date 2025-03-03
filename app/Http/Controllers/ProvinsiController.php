<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Provinsi;

class ProvinsiController extends Controller {
    public function index() {
        return response()->json(Provinsi::all());
    }
}
