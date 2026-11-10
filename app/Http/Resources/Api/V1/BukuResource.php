<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BukuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $stokTersedia = (int) $this->stok;
        $isAvailable = $stokTersedia > 0;
        $totalPeminjaman = $this->whenCounted('peminjamans', fn () => $this->peminjamans_count, 0);

        $links = [
            'self' => [
                'href'   => url("/api/v1/bukus/{$this->id}"),
                'method' => 'GET',
            ],
            'referensi_eksternal' => [
                'href'   => url("/api/v1/bukus/{$this->id}/referensi-eksternal"),
                'method' => 'GET',
            ],
            'update_full' => [
                'href'   => url("/api/v1/bukus/{$this->id}"),
                'method' => 'PUT',
            ],
            'update_partial' => [
                'href'   => url("/api/v1/bukus/{$this->id}"),
                'method' => 'PATCH',
            ],
            'delete' => [
                'href'   => url("/api/v1/bukus/{$this->id}"),
                'method' => 'DELETE',
            ],
        ];

        // HATEOAS Dinamis: Link pinjam_buku HANYA muncul apabila stok > 0
        if ($isAvailable) {
            $links['pinjam_buku'] = [
                'href'   => url('/api/v1/peminjamans'),
                'method' => 'POST',
                'body_template' => [
                    'buku_id'       => $this->id,
                    'nim_peminjam'  => '12S24017',
                    'nama_peminjam' => 'Arya Pratama Sinambela',
                    'durasi_hari'   => 7,
                ],
            ];
        }

        return [
            'id'                 => $this->id,
            'isbn'               => $this->isbn,
            'judul'              => $this->judul,
            'penulis'            => $this->penulis,
            'penerbit'           => $this->penerbit ?? null,
            'tahun_terbit'       => isset($this->tahun_terbit) ? (int) $this->tahun_terbit : null,
            'stok'               => $stokTersedia,
            'status_ketersediaan'=> $isAvailable ? 'AVAILABLE' : 'OUT_OF_STOCK',
            'total_peminjaman'   => $totalPeminjaman,
            'kategori'           => $this->whenLoaded('kategori', fn () => [
                'id'            => $this->kategori->id,
                'nama_kategori' => $this->kategori->nama_kategori ?? $this->kategori->nama,
            ]),
            'riwayat_peminjaman' => PeminjamanResource::collection($this->whenLoaded('peminjamans')),
            'updated_at'         => $this->updated_at?->toIso8601String(),
            '_links'             => $links,
        ];
    }
}