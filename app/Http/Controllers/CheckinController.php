<?php

namespace App\Http\Controllers;

use App\Models\Checkin;
use App\Models\Recommendation;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'energy_level' => 'required|integer|min:1|max:5',
            'workload_level' => 'required|in:light,normal,heavy',
            'note' => 'nullable|string|max:1000',
        ]);

        $checkin = Checkin::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'date' => today(),
            ],
            $validated
        );


        /*
        |--------------------------------------------------------------------------
        | Recovery Recommendation
        |--------------------------------------------------------------------------
        |
        | MindCraft tidak mendiagnosis kondisi mental.
        | Sistem hanya memberikan saran pengaturan aktivitas
        | berdasarkan energy dan workload yang diisi pengguna.
        |
        */


        // Hapus rekomendasi recovery hari ini jika sebelumnya ada.
        Recommendation::where('user_id', auth()->id())
            ->where('type', 'recovery')
            ->whereDate('created_at', today())
            ->delete();


        /*
        |--------------------------------------------------------------------------
        | Energy rendah + workload berat
        |--------------------------------------------------------------------------
        */

        if (
            $checkin->energy_level <= 2 &&
            $checkin->workload_level === 'heavy'
        ) {

            Recommendation::create([
                'user_id' => auth()->id(),
                'type' => 'recovery',
                'title' => 'Waktu recovery disarankan',
                'message' =>
                    'Energi kamu sedang rendah sementara beban aktivitas hari ini cukup tinggi. ' .
                    'Pertimbangkan mengambil waktu istirahat sebelum melanjutkan aktivitas berikutnya. ' .
                    'Jika memungkinkan, task dengan prioritas rendah dapat dijadwalkan kembali.',
                'status' => 'pending',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Energy rendah + workload normal
        |--------------------------------------------------------------------------
        */

        elseif (
            $checkin->energy_level <= 2 &&
            $checkin->workload_level === 'normal'
        ) {

            Recommendation::create([
                'user_id' => auth()->id(),
                'type' => 'recovery',
                'title' => 'Berikan waktu untuk beristirahat',
                'message' =>
                    'Energi kamu sedang rendah. Pertimbangkan mengambil jeda ' .
                    'dan hindari menambahkan aktivitas yang tidak mendesak hari ini.',
                'status' => 'pending',
            ]);
        }


        return back()->with(
            'success',
            'Check-in hari ini berhasil disimpan.'
        );
    }
}