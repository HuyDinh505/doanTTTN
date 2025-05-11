<?php

namespace App\Http\Controllers;

use App\Models\LoaiVe;
use Illuminate\Http\Request;

class LoaiVeController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    /**
     * @OA\Get(
     *     path="/api/loaive",
     *     summary="Lấy danh sách các loại vé",
     *     tags={"Loại vé"},
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách loại vé",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="ten_loai_ve", type="string", example="Vé người lớn"),
     *                 @OA\Property(property="gia_ve", type="number", format="float", example=70000)
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $loaive = LoaiVe::all();
        return response()->json($loaive);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
