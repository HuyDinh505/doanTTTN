<?php

namespace App\Http\Controllers;

use App\Models\GheNgoi;
use Illuminate\Http\Request;

class GheController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ghe = GheNgoi::with('phong_chieu')->get();
        return response()->json($ghe);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ma_phong' => 'required|exists:phong_chieu,ma_phong',
            'so_ghe' => 'required|string|max:10|unique:ghe_ngoi,so_ghe,NULL,id,ma_phong,' . $request->ma_phong,
        ]);

        $ghe = GheNgoi::create([
            'ma_phong' => $request->ma_phong,
            'so_ghe' => $request->so_ghe,
            'ngay_tao_ghe' => now(),
        ]);

        return response()->json($ghe, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ghe = GheNgoi::with('phong_chieu')->findOrFail($id);
        return response()->json($ghe);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $ghe = GheNgoi::findOrFail($id);
        $request->validate([
            'so_ghe' => 'required|string|max:10|unique:ghe_ngoi,so_ghe,' . $id . ',ma_ghe,ma_phong,' . $ghe->ma_phong,
        ]);
        $ghe->update([
            'so_ghe' => $request->so_ghe,
        ]);
        return response()->json($ghe);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ghe = GheNgoi::findOrFail($id);
        $ghe->delete();
        return response()->json(['message' => 'Ghế đã được xóa']);
    }
}
