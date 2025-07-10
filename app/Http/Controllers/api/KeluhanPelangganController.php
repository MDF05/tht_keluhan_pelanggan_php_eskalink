<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KeluhanPelanggan;
use Illuminate\Http\Request;
use App\Exports\KeluhanPelangganExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

            // Tambahkan ke history status
            \App\Models\KeluhanStatusHis::create([
                'keluhan_id' => $keluhan->id,
                'status_keluhan' => $keluhan->status_keluhan,
            ]);
            
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

        // Tambahkan ke history status setiap kali update
        \App\Models\KeluhanStatusHis::create([
            'keluhan_id' => $keluhan->id,
            'status_keluhan' => $keluhan->status_keluhan,
        ]);

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

    /**
     * Export keluhan pelanggan ke berbagai format.
     *
     * @param string $format
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export($format)
    {
        $allowed = ['csv', 'xls', 'xlsx', 'txt', 'pdf'];
        if (!in_array($format, $allowed)) {
            abort(404);
        }
        $filename = 'keluhan_pelanggan_' . date('Ymd_His') . '.' . $format;
        return Excel::download(new \App\Exports\KeluhanPelangganExport, $filename, match($format) {
            'csv' => \Maatwebsite\Excel\Excel::CSV,
            'xls' => \Maatwebsite\Excel\Excel::XLS,
            'xlsx' => \Maatwebsite\Excel\Excel::XLSX,
            'txt' => \Maatwebsite\Excel\Excel::TSV,
            'pdf' => \Maatwebsite\Excel\Excel::DOMPDF,
        });
    }

    /**
     * API summary status keluhan (pie chart)
     */
    public function summaryStatus()
    {
        $result = KeluhanPelanggan::select('status_keluhan', DB::raw('count(*) as total'))
            ->groupBy('status_keluhan')
            ->get();
        return response()->json($result);
    }

    /**
     * API jumlah keluhan per status per bulan (6 bulan terakhir, bar chart)
     */
    public function statusPerMonth()
    {
        $now = Carbon::now();
        $start = $now->copy()->subMonths(5)->startOfMonth();
        $data = DB::table('keluhan_status_his')
            ->select(DB::raw('DATE_FORMAT(updated_at, "%Y-%m") as bulan'), 'status_keluhan', DB::raw('count(*) as total'))
            ->where('updated_at', '>=', $start)
            ->groupBy(DB::raw('DATE_FORMAT(updated_at, "%Y-%m")'), 'status_keluhan')
            ->orderBy('bulan')
            ->get();
        return response()->json($data);
    }

    /**
     * API top 10 keluhan dengan umur terlama (table)
     */
    public function topAging()
    {
        $now = Carbon::now()->toDateString();
        $data = KeluhanPelanggan::select('*', DB::raw("DATEDIFF('$now', created_at) as umur"))
            ->orderByDesc('umur')
            ->limit(10)
            ->get();
        return response()->json($data);
    }
}
