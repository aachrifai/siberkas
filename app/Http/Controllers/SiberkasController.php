<?php

namespace App\Http\Controllers;

use App\Models\BerkasUpload;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SiberkasController extends Controller
{
    // === 1. HALAMAN USER (FORM) ===
    public function index()
    {
        $bg = Setting::where('key', 'app_background')->value('value');
        return view('form', compact('bg'));
    }

    public function store(Request $request)
    {
        // 1. Definisikan Aturan Validasi
        $rules = [
            'no_permohonan'    => 'required|numeric',
            'nama_lengkap'     => 'required|string',
            'no_wa'            => 'required|numeric',
            'lokasi_wawancara' => 'required',
            'tanggal_foto'     => 'required|date',
            // max:5120 = 5MB
            'file_berkas'      => 'required|file|mimes:pdf,jpg,png,jpeg|max:5120',
        ];

        // 2. Definisikan Pesan Error (Bahasa Indonesia)
        $messages = [
            'required'             => 'Kolom ini wajib diisi.',
            'numeric'              => 'Isian harus berupa angka.',
            'file_berkas.required' => 'Anda belum mengupload file berkas.',
            'file_berkas.mimes'    => 'Format file salah. Gunakan PDF, JPG, atau PNG.',
            'file_berkas.max'      => 'Maaf, ukuran file terlalu besar! Maksimal size adalah 5MB.', 
        ];

        $request->validate($rules, $messages);

        // --- Proses Simpan ---
        $file = $request->file('file_berkas');
        $filename = $request->no_permohonan . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads', $filename, 'public');

        BerkasUpload::create([
            'no_permohonan'    => $request->no_permohonan,
            'nama_lengkap'     => $request->nama_lengkap,
            'no_wa'            => $this->formatWa($request->no_wa),
            'lokasi_wawancara' => $request->lokasi_wawancara,
            'tanggal_foto'     => $request->tanggal_foto,
            'nama_file_asli'   => $file->getClientOriginalName(),
            'path_file'        => $path,
            'is_checked'       => 0,
        ]);

        return back()->with('success', 'Berkas berhasil terkirim! Terima kasih.');
    }

    // === 2. AUTHENTICATION (LOGIN/LOGOUT) ===
    public function showLogin()
    {
        $bg = Setting::where('key', 'app_background')->value('value');
        return view('login', compact('bg'));
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Email atau password salah!']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // === 3. DASHBOARD ADMIN (PAGINATION 5 DATA) ===
    public function admin(Request $request)
    {
        // 1. Query Dasar
        $query = BerkasUpload::query();

        // Filter Tanggal
        if ($request->has('tanggal') && $request->tanggal != '') {
            $query->whereDate('created_at', $request->tanggal);
        }

        // 2. PAGINATION LOGIC:
        // Ambil daftar No Permohonan (Unik), urutkan dari yang paling baru uploadnya
        // Pagination: 7 Permohonan per halaman
        $permohonanPage = $query->select('no_permohonan')
                                ->groupBy('no_permohonan')
                                ->orderByRaw('MAX(created_at) DESC')
                                ->paginate(5); 

        // 3. Ambil Detail File hanya untuk ID yang ada di halaman ini
        $ids = $permohonanPage->pluck('no_permohonan');
        
        $files = BerkasUpload::whereIn('no_permohonan', $ids)
                             ->orderBy('created_at', 'desc')
                             ->get();

        // Grouping data agar struktur view tidak error ($data->first())
        // SortByDesc agar urutan di tampilan tetap yang terbaru di atas
        $data = $files->groupBy('no_permohonan')->sortByDesc(function($item) {
            return $item->max('created_at');
        });
        
        $bg = Setting::where('key', 'app_background')->value('value');
        
        $months = BerkasUpload::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month")
                    ->distinct()
                    ->orderBy('month', 'desc')
                    ->pluck('month');

        // Kirim $data (file) dan $permohonanPage (untuk tombol navigasi di view)
        return view('admin', compact('data', 'permohonanPage', 'bg', 'months'));
    }

    // === 4. FITUR UPDATE DATA PEMOHON ===
    public function updateData(Request $request, $no_permohonan)
    {
        $request->validate([
            'nama_lengkap'     => 'required',
            'no_wa'            => 'required',
            'lokasi_wawancara' => 'required'
        ]);

        BerkasUpload::where('no_permohonan', $no_permohonan)->update([
            'nama_lengkap'     => $request->nama_lengkap,
            'no_wa'            => $this->formatWa($request->no_wa),
            'lokasi_wawancara' => $request->lokasi_wawancara,
        ]);

        return back()->with('success', 'Data pemohon berhasil diperbarui!');
    }

    // === FITUR CEKLIS / TANDAI SUDAH DICEK ===
    public function toggleCheck($no_permohonan)
    {
        $sample = BerkasUpload::where('no_permohonan', $no_permohonan)->first();
        
        if ($sample) {
            $newStatus = $sample->is_checked ? 0 : 1;
            
            BerkasUpload::where('no_permohonan', $no_permohonan)->update([
                'is_checked' => $newStatus
            ]);
            
            $msg = $newStatus ? 'Data ditandai SUDAH dicek.' : 'Tanda ceklis dihapus.';
            return back()->with('success', $msg);
        }

        return back()->with('error', 'Data tidak ditemukan.');
    }

    // === 5. PENGATURAN SYSTEM ===
    public function updateBackground(Request $request)
    {
        $request->validate(['bg_image' => 'required|image|max:10240']);
        
        $oldBg = Setting::where('key', 'app_background')->value('value');
        if ($oldBg) {
            Storage::disk('public')->delete($oldBg);
        }

        $path = $request->file('bg_image')->store('settings', 'public');
        
        Setting::updateOrCreate(
            ['key' => 'app_background'],
            ['value' => $path]
        );

        return back()->with('success', 'Background berhasil diubah!');
    }

    // Hapus satu file
    public function destroy($id)
    {
        $item = BerkasUpload::findOrFail($id);
        Storage::disk('public')->delete($item->path_file);
        $item->delete();

        return back()->with('success', 'File berhasil dihapus!');
    }

    // Hapus per bulan
    public function destroyMonth(Request $request)
    {
        $request->validate(['bulan' => 'required']);
        
        $year = substr($request->bulan, 0, 4);
        $month = substr($request->bulan, 5, 2);

        $files = BerkasUpload::whereYear('created_at', $year)
                             ->whereMonth('created_at', $month)
                             ->get();

        if ($files->isEmpty()) {
            return back()->with('error', 'Tidak ada data di bulan tersebut.');
        }

        foreach ($files as $file) {
            Storage::disk('public')->delete($file->path_file);
            $file->delete();
        }

        return back()->with('success', 'Semua data bulan ' . $request->bulan . ' berhasil dihapus!');
    }

    // Helper Format WA
    private function formatWa($number) {
        if (substr($number, 0, 1) == '0') { return '62' . substr($number, 1); }
        return $number;
    }
}