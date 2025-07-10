<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KeluhanPelanggan;
use Illuminate\Http\Request;

class KeluhanPelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $keluhan = KeluhanPelanggan::all();
        return response()->json($keluhan);
    }

    /**
     * Search keluhan by nama
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function searchByNama(Request $request)
    {
        $nama = $request->query('nama');
        
        if (!$nama) {
            return response()->json(['message' => 'Parameter nama diperlukan'], 400);
        }

        $keluhan = KeluhanPelanggan::where('nama', 'LIKE', "%{$nama}%")->get();
        
        return response()->json($keluhan);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            // Debug: Log semua data yang diterima
            \Log::info('Received data:', $request->all());
            
            $request->validate([
                'nama' => 'required|string',
                'email' => 'required|email',
                'nomor_hp' => 'required|string',
                'keluhan' => 'required|string',
                'status_keluhan' => 'nullable|string',
            ]);

            $data = $request->all();
            if (isset($data['name']) && !isset($data['nama'])) {
                $data['nama'] = $data['name'];
                unset($data['name']);
            }

            // Set default value untuk status_keluhan jika tidak ada
            if (!isset($data['status_keluhan'])) {
                $data['status_keluhan'] = '0';
            }

            // Debug: Log data yang akan disimpan
            \Log::info('Data to save:', $data);

            $keluhan = KeluhanPelanggan::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil ditambahkan',
                'data' => $keluhan
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data_received' => $request->all()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $keluhan = KeluhanPelanggan::findOrFail($id);
        return response()->json($keluhan);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $keluhan = KeluhanPelanggan::findOrFail($id);
        $keluhan->update($request->all());
        return response()->json($keluhan);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $keluhan = KeluhanPelanggan::findOrFail($id);
        $keluhan->delete();
        return response()->json(null, 204);
    }
}
