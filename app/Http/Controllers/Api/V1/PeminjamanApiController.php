<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PeminjamanResource;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PeminjamanApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->query('per_page', 10), 1), 50);

        $paginator = Peminjaman::with('buku')
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->query('nim'), fn ($q, $nim) => $q->where('nim_peminjam', 'like', "%{$nim}%"))
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return $this->successResponse(
            data: PeminjamanResource::collection($paginator->items()),
            message: 'Daftar transaksi sirkulasi peminjaman berhasil diambil.',
            status: 200,
            meta: [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total_items'  => $paginator->total(),
                'total_pages'  => $paginator->lastPage(),
            ]
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'buku_id'       => 'required|integer',
            'nim_peminjam'  => 'required|string|max:20',
            'nama_peminjam' => 'required|string|max:150',
            'durasi_hari'   => 'nullable|integer|min:1|max:30',
        ]);

        return DB::transaction(function () use ($validated) {
            $buku = Buku::where('id', $validated['buku_id'])->lockForUpdate()->first();

            if (!$buku) {
                return $this->problemResponse('Resource Not Found', 'Buku tidak ditemukan.', 404, 'book-not-found');
            }

            $nim = strtoupper(trim($validated['nim_peminjam']));
            $durasi = (int) ($validated['durasi_hari'] ?? 7);

            // ---> PENGISIAN KODE_PINJAM DISERTAKAN DI SINI <---
            $payloadPinjam = [
                'kode_pinjam'         => 'PJM-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                'buku_id'             => $buku->id,
                'nim_peminjam'        => $nim,
                'nama_peminjam'       => $validated['nama_peminjam'],
                'tanggal_pinjam'      => Carbon::today()->toDateString(),
                'tanggal_jatuh_tempo' => Carbon::today()->addDays($durasi)->toDateString(),
                'status'              => 'dipinjam',
            ];

            $masihMeminjam = Peminjaman::where('buku_id', $buku->id)
                ->where('nim_peminjam', $nim)
                ->where('status', 'dipinjam')
                ->exists();

            if ($masihMeminjam) {
                return $this->problemResponse(
                    title: 'Duplicate Active Loan Conflict',
                    detail: "Mahasiswa dengan NIM '{$nim}' masih meminjam buku ini dan belum dikembalikan.",
                    status: 409,
                    errorSlug: 'duplicate-active-loan'
                );
            }

            if ($buku->stok < 1) {
                return $this->problemResponse(
                    title: 'Book Out of Stock',
                    detail: "Stok buku sedang habis (0 eksemplar tersisa).",
                    status: 422,
                    errorSlug: 'stock-exhausted'
                );
            }

            $buku->decrement('stok');
            $peminjaman = Peminjaman::create($payloadPinjam);
            $peminjaman->load('buku');

            return $this->createdResponse(
                data: new PeminjamanResource($peminjaman),
                locationUrl: url("/api/v1/peminjamans/{$peminjaman->id}"),
                message: "Peminjaman buku berhasil dicatat."
            );
        });
    }

    public function kembalikan(string $id): JsonResponse
    {
        return DB::transaction(function () use ($id) {
            $peminjaman = Peminjaman::where('id', $id)->lockForUpdate()->first();

            if (!$peminjaman) {
                return $this->problemResponse('Resource Not Found', 'Transaksi peminjaman tidak ditemukan.', 404, 'loan-not-found');
            }

            if ($peminjaman->status === 'dikembalikan') {
                return $this->problemResponse('Loan Already Returned Conflict', 'Transaksi peminjaman sudah dikembalikan.', 409, 'loan-already-returned');
            }

            $buku = Buku::where('id', $peminjaman->buku_id)->lockForUpdate()->first();

            $peminjaman->update([
                'status'          => 'dikembalikan',
                'tanggal_kembali' => Carbon::today()->toDateString(),
            ]);

            if ($buku) {
                $buku->increment('stok');
            }

            $peminjaman->load('buku');

            return $this->successResponse(
                data: new PeminjamanResource($peminjaman),
                message: "Buku berhasil dikembalikan. Stok buku bertambah menjadi: {$buku?->fresh()->stok}.",
                status: 200
            );
        });
    }
}