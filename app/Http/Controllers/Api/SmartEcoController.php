<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bin;
use App\Models\Device;
use App\Models\Notification;
use App\Models\Resident;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\Facades\MQTT;
use Throwable;

class SmartEcoController extends Controller
{
    public function liveStatus(): JsonResponse
    {
        $bins = Bin::all();
        $device = Device::first();
        $isOnline = $device !== null
            && $device->last_seen_at !== null
            && $device->last_seen_at->gt(now()->subSeconds(15));

        return response()->json([
            'bins' => $bins,
            'total_weight' => $bins->sum('current_weight'),
            'device' => $device,
            'is_online' => $isOnline,
            'unread_count' => Notification::where('is_read', false)->count(),
            'active_warnings_count' => Notification::where('type', 'warning')
                ->where('is_resolved', false)
                ->count(),
        ]);
    }

    public function claimOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'otp_code' => ['required', 'string', 'size:4', 'regex:/^[0-9]{4}$/'],
            'phone' => ['nullable', 'string', 'max:20'],
            'citizen_name' => ['nullable', 'string', 'max:100'],
        ]);

        $result = DB::transaction(function () use ($validated): ?array {
            $resident = isset($validated['phone'])
                ? Resident::where('phone', $validated['phone'])->lockForUpdate()->first()
                : null;

            if (isset($validated['phone']) && $resident === null) {
                return ['resident_not_found' => true];
            }

            $transactions = Transaction::where('otp_code', $validated['otp_code'])
                ->where('status', 'pending')
                ->where('expires_at', '>=', now())
                ->where('created_at', '>=', now()->subMinutes(3))
                ->lockForUpdate()
                ->get();

            if ($transactions->isEmpty()) {
                return null;
            }

            $totalPoints = (int) $transactions
                ->where('category', 'jelantah')
                ->sum('points_earned');
            Transaction::whereKey($transactions->modelKeys())->update([
                'status' => 'claimed',
                'resident_id' => $resident?->id,
                'citizen_name' => $resident?->name
                    ?? (($validated['citizen_name'] ?? null) ?: 'Warga RT'),
            ]);

            if ($resident !== null && $totalPoints > 0) {
                $resident->increment('points_balance', $totalPoints);
            }

            return [
                'total_points' => $totalPoints,
                'resident_name' => $resident?->name,
                'points_balance' => $resident?->points_balance,
            ];
        });

        if (is_array($result) && ($result['resident_not_found'] ?? false) === true) {
            return response()->json(['message' => 'Nomor telepon warga tidak terdaftar.'], 422);
        }

        if ($result === null) {
            return response()->json([
                'message' => 'Kode OTP tidak valid atau sudah kedaluwarsa.',
            ], 422);
        }

        try {
            // The hub uses this existing success topic as its auto-tare trigger.
            MQTT::publish('smarteco/pandes/klaim_sukses', 'OK');
        } catch (Throwable $exception) {
            Log::warning('Gagal mengirim konfirmasi klaim ke broker MQTT.', [
                'exception' => $exception,
            ]);
        }

        return response()->json([
            'message' => 'Klaim berhasil.',
            'total_points' => $result['total_points'],
            'resident_name' => $result['resident_name'],
            'points_balance' => $result['points_balance'],
        ]);
    }

    public function residentBalance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
        ]);
        $resident = Resident::where('phone', $validated['phone'])->first();

        if ($resident === null) {
            return response()->json(['message' => 'Nomor telepon warga tidak terdaftar.'], 404);
        }

        return response()->json([
            'resident_name' => $resident->name,
            'points_balance' => $resident->points_balance,
        ]);
    }

    public function emptyBin(string $category): JsonResponse
    {
        $bin = Bin::where('category', $category)->firstOrFail();

        DB::transaction(function () use ($bin, $category): void {
            $bin->update([
                'current_weight' => 0,
                'capacity_percent' => 0,
                'status' => 'aman',
            ]);

            Notification::where('bin_category', $category)
                ->where('is_resolved', false)
                ->update([
                    'is_resolved' => true,
                    'is_read' => true,
                ]);

            Notification::create([
                'type' => 'info',
                'title' => 'Penjemputan '.ucfirst($category).' Selesai',
                'message' => $category === 'jelantah'
                    ? 'Pengosongan selesai • +50 Poin Eco ditambahkan.'
                    : 'Pengosongan kompartemen selesai • Siap digunakan kembali.',
                'bin_category' => $category,
                'is_read' => false,
                'is_resolved' => true,
            ]);
        });

        return response()->json([
            'message' => 'Bin berhasil dikosongkan.',
            'category' => $category,
        ]);
    }

    public function readAllNotifications(): JsonResponse
    {
        Notification::where('is_read', false)->update(['is_read' => true]);

        return response()->json(['message' => 'Semua notifikasi telah ditandai dibaca.']);
    }

    public function servo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'direction' => ['required', 'in:ORGANIK,ANORGANIK'],
        ]);

        MQTT::publish('smarteco/pandes/perintah_servo', $validated['direction']);

        return response()->json([
            'message' => 'Perintah servo berhasil dikirim.',
            'direction' => $validated['direction'],
        ]);
    }

    public function callOfficer(): JsonResponse
    {
        Notification::create([
            'type' => 'info',
            'title' => 'Petugas Kebersihan Dipanggil',
            'message' => 'Permintaan penjemputan manual telah dikirimkan ke petugas RT.',
            'is_read' => false,
            'is_resolved' => true,
        ]);

        return response()->json(['message' => 'Petugas kebersihan berhasil dipanggil.']);
    }
}
