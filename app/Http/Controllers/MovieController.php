<?php

namespace App\Http\Controllers;

use App\Models\Phim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MovieController extends Controller
{
    /**
     * Display a listing of the movies.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $movies = Phim::all();
        return response()->json($movies);
    }

    /**
     * Store a newly created movie in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ten_phim' => 'required|string|max:255',
            'mo_ta' => 'required|string',
            'thoi_luong' => 'required|integer',
            'ngay_khoi_chieu' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_khoi_chieu',
            'poster' => 'required|string',
            'trailer' => 'required|string',
            'trang_thai' => 'required|in:dang_chieu,sap_chieu,ngung_chieu',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $movie = Phim::create($request->all());
        return response()->json($movie, 201);
    }

    /**
     * Display the specified movie.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $movie = Phim::find($id);
        if (!$movie) {
            return response()->json(['message' => 'Movie not found'], 404);
        }
        return response()->json($movie);
    }

    /**
     * Update the specified movie in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $movie = Phim::find($id);
        if (!$movie) {
            return response()->json(['message' => 'Movie not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'ten_phim' => 'string|max:255',
            'mo_ta' => 'string',
            'thoi_luong' => 'integer',
            'ngay_khoi_chieu' => 'date',
            'ngay_ket_thuc' => 'date|after:ngay_khoi_chieu',
            'poster' => 'string',
            'trailer' => 'string',
            'trang_thai' => 'in:dang_chieu,sap_chieu,ngung_chieu',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $movie->update($request->all());
        return response()->json($movie);
    }

    /**
     * Remove the specified movie from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $movie = Phim::find($id);
        if (!$movie) {
            return response()->json(['message' => 'Movie not found'], 404);
        }

        $movie->delete();
        return response()->json(null, 204);
    }
}
