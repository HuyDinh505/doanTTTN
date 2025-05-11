<?php

namespace App\Http\Controllers;

use App\Models\VaiTro;
use Illuminate\Http\Request;

class VaiTroController extends Controller
{
    public function index()
    {
        return response()->json(VaiTro::all());
    }
}
