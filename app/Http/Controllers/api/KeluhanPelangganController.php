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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'email' => 'required|email',
            'nomor_hp' => 'required|string',
            'keluhan' => 'required|string',
        ]);

        $keluhan = KeluhanPelanggan::create($request->all());
        return response()->json($keluhan, 201);
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
