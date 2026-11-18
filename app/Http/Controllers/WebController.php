<?php

namespace App\Http\Controllers;

use App\Models\Bin;
use App\Models\Device;
use App\Models\Notification;
use App\Models\Resident;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WebController extends Controller
{
    public function publicClaim(): ViewContract
    {
        return view('klaim');
    }

    public function dashboard(): ViewContract
    {
        $bins = Bin::all()->keyBy('category');
        $totalWeight = Bin::sum('current_weight');
        $device = Device::first();
        $isOnline = $device !== null
            && $device->last_seen_at !== null
            && $device->last_seen_at->gt(now()->subSeconds(15));
        $unreadCount = Notification::where('is_read', false)->count();

        return view('dashboard', compact('bins', 'totalWeight', 'device', 'isOnline', 'unreadCount'));
    }

    public function riwayat(Request $request): ViewContract
    {
        $filters = $request->validate([
            'category' => ['nullable', 'in:organik,anorganik,jelantah'],
        ]);
        $category = $filters['category'] ?? null;

        $transactions = Transaction::where('status', 'claimed')
            ->when($category !== null, fn ($query) => $query->where('category', $category))
            ->with('resident')
            ->orderByDesc('created_at')
            ->get();
        $weeklyCount = Transaction::where('status', 'claimed')
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->count();
        $unreadCount = Notification::where('is_read', false)->count();

        return view('riwayat', compact('transactions', 'weeklyCount', 'unreadCount', 'category'));
    }

    public function exportPdf(): Response
    {
        $transactions = Transaction::where('status', 'claimed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->orderByDesc('created_at')
            ->get();

        return Pdf::loadView('pdf.laporan', compact('transactions'))
            ->download('laporan-smart-eco-rt.pdf');
    }

    public function notifikasi(Request $request): ViewContract
    {
        $filters = $request->validate([
            'type' => ['nullable', 'in:warning,info'],
        ]);
        $type = $filters['type'] ?? null;

        $notifications = Notification::when($type !== null, fn ($query) => $query->where('type', $type))
            ->orderBy('is_resolved')
            ->orderByDesc('created_at')
            ->get();
        $unreadCount = Notification::where('is_read', false)->count();

        return view('notifikasi', compact('notifications', 'unreadCount', 'type'));
    }

    public function perangkat(): ViewContract
    {
        $device = Device::first();
        $isOnline = $device !== null
            && $device->last_seen_at !== null
            && $device->last_seen_at->gt(now()->subSeconds(15));
        $unreadCount = Notification::where('is_read', false)->count();

        return view('perangkat', compact('device', 'isOnline', 'unreadCount'));
    }

    public function profil(): ViewContract
    {
        $user = Auth::user();
        $totalJelantah = Transaction::where('category', 'jelantah')
            ->where('status', 'claimed')
            ->sum('amount');
        $totalPoints = Transaction::where('category', 'jelantah')
            ->where('status', 'claimed')
            ->sum('points_earned');
        $unreadCount = Notification::where('is_read', false)->count();

        return view('profil', compact('user', 'totalJelantah', 'totalPoints', 'unreadCount'));
    }

    public function warga(): ViewContract
    {
        $residents = Resident::orderBy('name')->get();
        $unreadCount = Notification::where('is_read', false)->count();

        return view('warga', compact('residents', 'unreadCount'));
    }

    public function storeResident(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:residents,phone'],
        ]);

        Resident::create($validated);

        return redirect()->route('warga')->with('status', 'Data warga berhasil ditambahkan.');
    }

    public function redeemPoints(Request $request, Resident $resident): RedirectResponse
    {
        $validated = $request->validate([
            'points' => ['required', 'integer', 'min:1'],
        ]);

        $redeemed = DB::transaction(function () use ($resident, $validated): bool {
            $lockedResident = Resident::whereKey($resident->id)->lockForUpdate()->firstOrFail();

            if ($lockedResident->points_balance < $validated['points']) {
                return false;
            }

            $lockedResident->decrement('points_balance', $validated['points']);

            return true;
        });

        if (! $redeemed) {
            return redirect()->route('warga')
                ->withErrors(['points' => 'Saldo poin warga tidak mencukupi.']);
        }

        return redirect()->route('warga')->with('status', 'Penukaran poin berhasil dicatat.');
    }
}
