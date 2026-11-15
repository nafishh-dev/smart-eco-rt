<?php

namespace App\Http\Controllers;

use App\Models\Bin;
use App\Models\Device;
use App\Models\Notification;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class WebController extends Controller
{
    public function dashboard(): View
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

    public function riwayat(Request $request): View
    {
        $filters = $request->validate([
            'category' => ['nullable', 'in:organik,anorganik,jelantah'],
        ]);
        $category = $filters['category'] ?? null;

        $transactions = Transaction::where('status', 'claimed')
            ->when($category !== null, fn ($query) => $query->where('category', $category))
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

    public function notifikasi(Request $request): View
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

    public function perangkat(): View
    {
        $device = Device::first();
        $isOnline = $device !== null
            && $device->last_seen_at !== null
            && $device->last_seen_at->gt(now()->subSeconds(15));
        $unreadCount = Notification::where('is_read', false)->count();

        return view('perangkat', compact('device', 'isOnline', 'unreadCount'));
    }
}
