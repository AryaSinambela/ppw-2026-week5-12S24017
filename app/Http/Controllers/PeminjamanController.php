<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class PeminjamanController extends Controller
{
    public function index(Request $request): View
    {
        $peminjamans = Peminjaman::with('buku') // eager load, bebas N+1
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->when($request->q, function ($query, $keyword) {
                $query->where(function ($w) use ($keyword) {
                    $w->where('kode_pinjam', 'like', "%{$keyword}%")
                      ->orWhere('nim_peminjam', 'like', "%{$keyword}%")
                      ->orWhere('nama_peminjam', 'like', "%{$keyword}%");
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('peminjaman.index', compact('peminjamans'));
    }

    public function create(Request $request): View
    {
        return view('peminjaman.create', [
            'bukus'    => Buku::orderBy('judul')->get(['id', 'judul', 'isbn', 'stok']),
            'selected' => $request->integer('buku_id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'buku_id'       => 'required|integer|exists:bukus,id',
            'nim_peminjam'  => 'required|string|min:6|max:15',
            'nama_peminjam' => 'required|string|min:3|max:150',
            'lama_pinjam'   => 'required|integer|min:1|max:30',
        ]);

        try {
            $peminjaman = DB::transaction(function () use ($validated) {
                // Pessimistic locking: transaksi lain mengantre sampai commit/rollback
                $buku = Buku::where('id', $validated['buku_id'])->lockForUpdate()->firstOrFail();

                if ($buku->stok < 1) {
                    throw new RuntimeException("Stok buku \"{$buku->judul}\" habis (0). Peminjaman ditolak.");
                }

                $nim = strtoupper(trim($validated['nim_peminjam']));

                $masihDipinjam = Peminjaman::where('buku_id', $buku->id)
                    ->where('nim_peminjam', $nim)
                    ->where('status', 'dipinjam')
                    ->exists();

                if ($masihDipinjam) {
                    throw new RuntimeException("NIM {$nim} masih meminjam buku ini dan belum mengembalikannya.");
                }

                $buku->decrement('stok');

                return Peminjaman::create([
                    'kode_pinjam'         => 'PJM-' . now()->format('ymd') . '-' . strtoupper(Str::random(5)),
                    'buku_id'             => $buku->id,
                    'nim_peminjam'        => $nim,
                    'nama_peminjam'       => $validated['nama_peminjam'],
                    'tanggal_pinjam'      => now()->toDateString(),
                    'tanggal_jatuh_tempo' => now()->addDays((int) $validated['lama_pinjam'])->toDateString(),
                    'status'              => 'dipinjam',
                ]);
            }, 3);

            // Session state: peminjam terakhir
            session(['last_borrower' => $peminjaman->nama_peminjam . ' (' . $peminjaman->nim_peminjam . ')']);

            return redirect()->route('peminjaman.index')
                ->with('success', "Peminjaman {$peminjaman->kode_pinjam} berhasil dicatat secara atomik.");
        } catch (RuntimeException $e) {
            Log::error('[SIRKULASI][PINJAM] ' . $e->getMessage());

            return back()->withInput()->with('error', $e->getMessage());
        } catch (QueryException $e) {
            Log::error('[DB EXCEPTION][PINJAM] ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Terjadi kesalahan basis data. Transaksi dibatalkan (rollback).');
        }
    }

    public function kembali(int $id): RedirectResponse
    {
        try {
            $peminjaman = DB::transaction(function () use ($id) {
                $pinjam = Peminjaman::where('id', $id)->lockForUpdate()->firstOrFail();

                if ($pinjam->status === 'dikembalikan') {
                    throw new RuntimeException("Transaksi {$pinjam->kode_pinjam} sudah dikembalikan sebelumnya.");
                }

                $buku = Buku::where('id', $pinjam->buku_id)->lockForUpdate()->firstOrFail();

                $pinjam->update([
                    'status'          => 'dikembalikan',
                    'tanggal_kembali' => now()->toDateString(),
                ]);
                $buku->increment('stok');

                return $pinjam;
            }, 3);

            return back()->with('success', "Buku pada transaksi {$peminjaman->kode_pinjam} berhasil dikembalikan.");
        } catch (RuntimeException $e) {
            Log::error('[SIRKULASI][KEMBALI] ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        } catch (QueryException $e) {
            Log::error('[DB EXCEPTION][KEMBALI] ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan basis data. Pengembalian dibatalkan (rollback).');
        }
    }
}