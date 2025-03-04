<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Provinsi;
use Illuminate\Support\Facades\Validator;

class ProvinsiController extends Controller
{
    public function index()
    {
        return response()->json(Provinsi::all());
    }

    // Menyimpan data provinsi baru
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama'    => 'required|string|max:255',
            'geojson' => 'required|json'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $provinsi = Provinsi::create([
            'nama'    => $request->nama,
            'geojson' => $request->geojson // Pastikan input geojson dalam bentuk JSON string
        ]);

        return response()->json([
            'success' => true,
            'data'    => $provinsi
        ], 201);
    }

    // Menampilkan detail satu provinsi
    public function show($id)
    {
        $provinsi = Provinsi::find($id);

        if (!$provinsi) {
            return response()->json([
                'success' => false,
                'message' => 'Provinsi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $provinsi
        ]);
    }

    // Mengupdate data provinsi
    public function update(Request $request, $id)
    {
        $provinsi = Provinsi::find($id);

        if (!$provinsi) {
            return response()->json([
                'success' => false,
                'message' => 'Provinsi tidak ditemukan'
            ], 404);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama'    => 'sometimes|required|string|max:255',
            'geojson' => 'sometimes|required|json'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $provinsi->update($request->only(['nama', 'geojson']));

        return response()->json([
            'success' => true,
            'data'    => $provinsi
        ]);
    }

    // Menghapus data provinsi
    public function destroy($id)
    {
        $provinsi = Provinsi::find($id);

        if (!$provinsi) {
            return response()->json([
                'success' => false,
                'message' => 'Provinsi tidak ditemukan'
            ], 404);
        }

        $provinsi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Provinsi berhasil dihapus'
        ]);
    }
}
