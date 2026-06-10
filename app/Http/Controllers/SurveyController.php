<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SurveyController extends Controller
{
    /**
     * Halaman 1: Pilih Unit Layanan
     */
    public function selectUnit()
    {
        // Ambil semua unit yang aktif dengan count responden periode ini
        $activePeriod = Period::active()->first();
        
        $units = Unit::active()
            ->orderBy('name')
            ->withCount(['respondents' => function($query) use ($activePeriod) {
                if ($activePeriod) {
                    $query->where('period_id', $activePeriod->id);
                }
            }])
            ->get();
        
        if (!$activePeriod) {
            return redirect()->route('home')
                ->with('error', 'Belum ada periode survei aktif. Silahkan hubungi admin.');
        }
        
        return view('survey.select-unit', compact('units', 'activePeriod'));
    }
    
    /**
     * Proses pilihan unit (simpan ke session)
     */
    public function postUnit(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id'
        ]);
        
        // Simpan pilihan unit ke session
        Session::put('survey.unit_id', $request->unit_id);
        
        // Redirect ke halaman pilih layanan
        return redirect()->route('survey.service');
    }
    
    /**
     * Halaman 2: Pilih Jenis Layanan
     */
    public function selectService()
    {
        // Cek apakah sudah pilih unit sebelumnya
        if (!Session::has('survey.unit_id')) {
            return redirect()->route('survey.unit')
                ->with('error', 'Silahkan pilih unit terlebih dahulu.');
        }
        
        $unitId = Session::get('survey.unit_id');
        $unit = Unit::findOrFail($unitId);
        
        // Daftar layanan (nanti bisa diambil dari database atau hardcoded dulu)
        $services = $this->getServicesByUnit($unit->code);
        
        return view('survey.select-service', compact('unit', 'services'));
    }
    
    /**
     * Proses pilihan layanan
     */
    public function postService(Request $request)
    {
        $request->validate([
            'selected_service' => 'required|string|min:3'
        ]);
        
        // Simpan layanan ke session
        Session::put('survey.selected_service', $request->selected_service);
        
        // Redirect ke halaman data diri (Tahap 4)
        return redirect()->route('survey.data-diri');
    }

    /**
     * Halaman 3: Data Diri Responden
     */
    public function dataDiri()
    {
        // Cek apakah sudah pilih unit dan layanan
        if (!Session::has('survey.unit_id') || !Session::has('survey.selected_service')) {
            return redirect()->route('survey.unit')
                ->with('error', 'Silahkan lengkapi pilihan unit dan layanan terlebih dahulu.');
        }
        
        $unitId = Session::get('survey.unit_id');
        $unit = Unit::findOrFail($unitId);
        $selectedService = Session::get('survey.selected_service');
        
        // Ambil data dari session jika sudah pernah diisi (untuk edit)
        $oldInput = Session::get('survey.respondent_data', []);
        
        return view('survey.data-diri', compact('unit', 'selectedService', 'oldInput'));
    }

    /**
     * Proses simpan data diri ke session
     */
    public function postDataDiri(Request $request)
    {
        // Validasi data
        $validated = $request->validate([
            'nik' => [
                'required',
                'string',
                'size:16',
                'regex:/^[0-9]+$/',
            ],
            'full_name' => 'required|string|min:3|max:100',
            'phone' => 'required|string|min:10|max:15|regex:/^[0-9]+$/',
            'age_group' => 'required|in:<20,20-30,31-40,41-50,>50',
            'gender' => 'required|in:male,female',
            'education' => 'required|in:sd,smp,sma,d1,d2,d3,s1,s2,s3',
            'occupation' => 'required|in:pns,swasta,wirausaha,petani,nelayan,ibu_rumah_tangga,pelajar,mahasiswa,lainnya',
            'other_occupation' => 'required_if:occupation,lainnya|nullable|string|min:2|max:100',
        ], [
            // Custom error messages
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus 16 digit angka.',
            'nik.regex' => 'NIK hanya boleh berisi angka.',
            'nik.unique' => 'NIK ini sudah mengisi survei untuk unit ini di periode berjalan.',
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'full_name.min' => 'Nama lengkap minimal 3 karakter.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.regex' => 'Nomor HP hanya boleh berisi angka.',
            'age_group.required' => 'Kelompok usia wajib dipilih.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'education.required' => 'Pendidikan terakhir wajib dipilih.',
            'occupation.required' => 'Pekerjaan wajib dipilih.',
            'other_occupation.required_if' => 'Silahkan isi pekerjaan lainnya.',
        ]);
        
        // Validasi custom: cek apakah NIK sudah pernah mengisi di unit dan periode yang sama
        $unitId = Session::get('survey.unit_id');
        $activePeriod = Period::active()->first();
        
        if (!$activePeriod) {
            return back()->with('error', 'Tidak ada periode survei aktif.');
        }
        
        $existingRespondent = \App\Models\Respondent::where('nik', $validated['nik'])
            ->where('unit_id', $unitId)
            ->where('period_id', $activePeriod->id)
            ->exists();
        
        if ($existingRespondent) {
            return back()->withErrors(['nik' => 'NIK ini sudah mengisi survei untuk unit ini di periode berjalan.'])->withInput();
        }
        
        // Siapkan data untuk disimpan ke session
        $respondentData = [
            'nik' => $validated['nik'],
            'full_name' => ucwords(strtolower($validated['full_name'])),
            'phone' => $validated['phone'],
            'age_group' => $validated['age_group'],
            'gender' => $validated['gender'],
            'education' => $validated['education'],
            'occupation' => $validated['occupation'],
            'other_occupation' => $validated['other_occupation'] ?? null,
        ];
        
        // Simpan ke session
        Session::put('survey.respondent_data', $respondentData);
        
        // Redirect ke halaman pertanyaan (Tahap 5)
        return redirect()->route('survey.question', ['step' => 1]);
    }

    /**
     * Halaman 4: 9 Pertanyaan SKM (multi-step)
     */
    public function showQuestion($step)
    {
        // Validasi step (1-9)
        if ($step < 1 || $step > 9) {
            return redirect()->route('survey.question', ['step' => 1]);
        }
        
        // Cek apakah sudah pilih unit, layanan, dan data diri
        if (!Session::has('survey.unit_id') || 
            !Session::has('survey.selected_service') || 
            !Session::has('survey.respondent_data')) {
            return redirect()->route('survey.unit')
                ->with('error', 'Silahkan lengkapi data survei terlebih dahulu.');
        }
        
        // Ambil jawaban yang sudah disimpan di session
        $answers = Session::get('survey.answers', []);
        
        // Dapatkan data pertanyaan
        $questions = \App\Models\Answer::getQuestions();
        $currentQuestion = $questions[$step];
        $currentAnswer = $answers[$step] ?? null;
        
        // Hitung progress percentage
        $progress = ($step / 9) * 100;
        
        return view('survey.questions', compact('step', 'questions', 'currentQuestion', 'currentAnswer', 'progress', 'answers'));
    }

    /**
     * Proses simpan jawaban per step
     */
    public function postQuestion(Request $request, $step)
    {
        // Validasi step
        if ($step < 1 || $step > 9) {
            return redirect()->route('survey.question', ['step' => 1]);
        }
        
        // Validasi jawaban
        $request->validate([
            'score' => 'required|integer|in:1,2,3,4'
        ], [
            'score.required' => 'Silahkan pilih jawaban terlebih dahulu.',
            'score.in' => 'Jawaban tidak valid.'
        ]);
        
        // Ambil answers yang sudah ada dari session
        $answers = Session::get('survey.answers', []);
        
        // Simpan jawaban untuk step ini
        $answers[$step] = $request->score;
        
        // Simpan kembali ke session
        Session::put('survey.answers', $answers);
        
        // Cek apakah ini step terakhir
        if ($step == 9) {
            // Redirect ke halaman konfirmasi / submit
            return redirect()->route('survey.confirm');
        }
        
        // Lanjut ke step berikutnya
        return redirect()->route('survey.question', ['step' => $step + 1]);
    }

    /**
    * Halaman konfirmasi sebelum submit (review semua jawaban)
    */
    public function confirm()
    {
        // Cek semua data survei lengkap
        if (!Session::has('survey.unit_id') || 
            !Session::has('survey.selected_service') || 
            !Session::has('survey.respondent_data') ||
            !Session::has('survey.answers') ||
            count(Session::get('survey.answers')) < 9) {
            return redirect()->route('survey.unit')
                ->with('error', 'Data survei tidak lengkap. Silahkan isi ulang.');
        }
        
        $unit = Unit::findOrFail(Session::get('survey.unit_id'));
        $selectedService = Session::get('survey.selected_service');
        $respondentData = Session::get('survey.respondent_data');
        $answers = Session::get('survey.answers'); // Array [1 => 3, 2 => 4, ...]
        $questions = \App\Models\Answer::getQuestions();
        
        // Hitung IKM
        $averageScore = collect($answers)->avg();
        $ikm = ($averageScore / 4) * 100;
        
        // Konversi mutu
        if ($ikm >= 88.31) {
            $mutu = ['grade' => 'A', 'label' => 'Sangat Baik', 'color' => 'green'];
        } elseif ($ikm >= 76.61) {
            $mutu = ['grade' => 'B', 'label' => 'Baik', 'color' => 'blue'];
        } elseif ($ikm >= 65.00) {
            $mutu = ['grade' => 'C', 'label' => 'Kurang Baik', 'color' => 'yellow'];
        } else {
            $mutu = ['grade' => 'D', 'label' => 'Tidak Baik', 'color' => 'red'];
        }
        
        // Debug: Log answers untuk memastikan data ada
        \Log::info('Confirm answers:', $answers);
        
        return view('survey.confirm', compact(
            'unit', 
            'selectedService', 
            'respondentData', 
            'answers', 
            'questions', 
            'ikm', 
            'mutu'
        ));
    }

    /**
    * Submit final - simpan ke database
    */
    public function submit(Request $request)
    {
        // Cek semua data survei lengkap
        if (!Session::has('survey.unit_id') || 
            !Session::has('survey.selected_service') || 
            !Session::has('survey.respondent_data') ||
            !Session::has('survey.answers') ||
            count(Session::get('survey.answers')) < 9) {
            return redirect()->route('survey.unit')
                ->with('error', 'Data survei tidak lengkap. Silahkan isi ulang.');
        }
        
        $unitId = Session::get('survey.unit_id');
        $period = Period::active()->first();
        
        if (!$period) {
            return redirect()->route('survey.unit')
                ->with('error', 'Tidak ada periode survei aktif.');
        }
        
        $respondentData = Session::get('survey.respondent_data');
        $answers = Session::get('survey.answers');
        $selectedService = Session::get('survey.selected_service');
        
        // Generate unique hash
        $uniqueHash = hash('sha256', $respondentData['nik'] . $unitId . $period->id);
        
        // Cek duplikat
        $existing = \App\Models\Respondent::where('unique_hash', $uniqueHash)->exists();
        if ($existing) {
            return redirect()->route('survey.unit')
                ->with('error', 'Anda sudah mengisi survei untuk unit ini di periode berjalan.');
        }
        
        // Simpan ke database
        \DB::beginTransaction();
        
        try {
            // 1. Simpan respondent
            $respondent = \App\Models\Respondent::create([
                'unit_id' => $unitId,
                'period_id' => $period->id,
                'nik' => $respondentData['nik'],
                'full_name' => $respondentData['full_name'],
                'phone' => $respondentData['phone'],
                'selected_service' => $selectedService,
                'age_group' => $respondentData['age_group'],
                'gender' => $respondentData['gender'],
                'education' => $respondentData['education'],
                'occupation' => $respondentData['occupation'],
                'other_occupation' => $respondentData['other_occupation'] ?? null,
                'ip_address' => $request->ip(),
                'unique_hash' => $uniqueHash,
            ]);
            
            // 2. Simpan semua jawaban
            foreach ($answers as $questionNumber => $score) {
                \App\Models\Answer::create([
                    'respondent_id' => $respondent->id,
                    'question_number' => $questionNumber,
                    'score' => $score,
                ]);
            }
            
            \DB::commit();
            
            // 3. Clear ALL survey session data
            Session::forget('survey.unit_id');
            Session::forget('survey.selected_service');
            Session::forget('survey.respondent_data');
            Session::forget('survey.answers');
            
            // 4. Redirect ke halaman terima kasih dengan flash message
            return redirect()->route('survey.thankyou')
                ->with('success', 'Terima kasih atas partisipasi Anda! Survei Anda telah tersimpan.');
            
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Survey submit error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->route('survey.unit')
                ->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Terima Kasih
     */
    public function thankyou()
    {
        return view('survey.thankyou');
    }

    /**
     * Halaman untuk mengedit data diri (kembali dari halaman pertanyaan)
     */
    public function editDataDiri()
    {
        if (!Session::has('survey.respondent_data')) {
            return redirect()->route('survey.unit');
        }
        
        $unitId = Session::get('survey.unit_id');
        $unit = Unit::findOrFail($unitId);
        $selectedService = Session::get('survey.selected_service');
        $oldInput = Session::get('survey.respondent_data', []);
        
        return view('survey.data-diri', compact('unit', 'selectedService', 'oldInput'));
    }
    
    /**
     * Helper: Dapatkan daftar layanan berdasarkan unit
     * TODO: Pindahkan ke database nanti
     */
    private function getServicesByUnit($unitCode)
    {
        $services = [
            'DISPENDIK' => [
                'Pendaftaran Sekolah',
                'Ijazah & Transkrip Nilai',
                'Beasiswa Pendidikan',
                'Administrasi Guru',
                'Informasi Pendidikan'
            ],
            'DINKES' => [
                'Pendaftaran Berobat',
                'Kartu Sehat',
                'Imunisasi',
                'KB & Keluarga Berencana',
                'Informasi Kesehatan'
            ],
            'DUKCAPIL' => [
                'Pembuatan KTP',
                'Pembuatan KK',
                'Akta Kelahiran',
                'Akta Kematian',
                'Kartu Identitas Anak'
            ],
            'DINSOS' => [
                'Bantuan Sosial',
                'Rehabilitasi Sosial',
                'Pemberdayaan Masyarakat',
                'Penanganan PMKS',
                'Informasi Sosial'
            ],
            'DISPERIN' => [
                'Perizinan Usaha',
                'Sertifikasi Produk',
                'Pembinaan UMKM',
                'Informasi Perindustrian',
                'Kemitraan Bisnis'
            ],
        ];
        
        return $services[$unitCode] ?? [
            'Administrasi Umum',
            'Perizinan',
            'Informasi Publik',
            'Pengaduan Masyarakat',
            'Layanan Konsultasi'
        ];
    }
}