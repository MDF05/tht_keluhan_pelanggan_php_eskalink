<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KeluhanStatusHis;
use Illuminate\Http\Request;

class KeluhanStatusHisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statusHis = KeluhanStatusHis::all();
        return response()->json($statusHis);
    }

    /**
     * Search status history by status_keluhan
     */
    public function searchByStatus(Request $request)
    {
        $status = $request->query('status_keluhan');
        if (!$status) {
            return response()->json(['message' => 'Parameter status_keluhan diperlukan'], 400);
        }
        $statusHis = KeluhanStatusHis::where('status_keluhan', 'LIKE', "%{$status}%")->get();
        return response()->json($statusHis);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'keluhan_id' => 'required|exists:keluhan_pelanggans,id',
            'status_keluhan' => 'required|string',
        ]);
        $statusHis = KeluhanStatusHis::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Status history berhasil ditambahkan',
            'data' => $statusHis
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $statusHis = KeluhanStatusHis::findOrFail($id);
        return response()->json($statusHis);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $statusHis = KeluhanStatusHis::findOrFail($id);
        $statusHis->update($request->all());
        return response()->json($statusHis);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $statusHis = KeluhanStatusHis::findOrFail($id);
        $statusHis->delete();
        return response()->json(null, 204);
    }
} 