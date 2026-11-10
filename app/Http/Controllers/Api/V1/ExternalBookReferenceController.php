<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Throwable;

class ExternalBookReferenceController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/v1/bukus/{id}/referensi-eksternal
     * SIPUS-Del bertindak sebagai REST SERVICE CONSUMER ke OpenLibrary REST API berdasarkan ISBN
     */
    public function show(string $id): JsonResponse
    {
        $buku = Buku::with('kategori')->find($id);

        if (!$buku) {
            return $this->problemResponse(
                title: 'Resource Not Found',
                detail: "Buku lokal dengan ID '{$id}' tidak ditemukan di SIPUS-Del.",
                status: 404,
                errorSlug: 'book-not-found'
            );
        }

        // Bersihkan tanda hubung pada ISBN (contoh: 978-0132350884 -> 9780132350884)
        $cleanIsbn = preg_replace('/[^0-9Xx]/', '', (string) $buku->isbn);
        $bibKey = "ISBN:{$cleanIsbn}";

        try {
            $response = Http::acceptJson()
                ->timeout(8)
                ->get('https://openlibrary.org/api/books', [
                    'bibkeys' => $bibKey,
                    'format'  => 'json',
                    'jscmd'   => 'data',
                ]);

            if ($response->failed()) {
                return $this->problemResponse(
                    title: 'Upstream Service Unavailable',
                    detail: 'Gagal menghubungi layanan eksternal OpenLibrary REST API.',
                    status: 502,
                    errorSlug: 'openlibrary-upstream-error'
                );
            }

            $payload = $response->json();
            $externalBook = $payload[$bibKey] ?? null;

            return $this->successResponse(
                data: [
                    'local_book' => [
                        'id'       => $buku->id,
                        'isbn'     => $buku->isbn,
                        'judul'    => $buku->judul,
                        'penulis'  => $buku->penulis,
                        'stok'     => (int) $buku->stok,
                    ],
                    'external_provider' => 'OpenLibrary REST API (https://openlibrary.org)',
                    'external_found'    => !is_null($externalBook),
                    'external_metadata' => $externalBook ? [
                        'title'          => $externalBook['title'] ?? $buku->judul,
                        'subtitle'       => $externalBook['subtitle'] ?? null,
                        'number_of_pages'=> $externalBook['number_of_pages'] ?? null,
                        'publish_date'   => $externalBook['publish_date'] ?? null,
                        'publishers'     => collect($externalBook['publishers'] ?? [])->pluck('name')->all(),
                        'authors'        => collect($externalBook['authors'] ?? [])->pluck('name')->all(),
                        'cover_url'      => $externalBook['cover']['medium'] ?? null,
                        'preview_url'    => $externalBook['url'] ?? null,
                    ] : null,
                ],
                message: $externalBook
                    ? 'Metadata buku eksternal berhasil diperkaya dari OpenLibrary API.'
                    : "Buku ditemukan di lokal SIPUS-Del, namun ISBN '{$cleanIsbn}' belum tercatat di OpenLibrary API."
            );
        } catch (Throwable $e) {
            return $this->problemResponse(
                title: 'External API Gateway Timeout',
                detail: 'Waktu koneksi ke OpenLibrary API habis (timeout > 8 detik) atau jaringan terputus.',
                status: 504,
                errorSlug: 'openlibrary-timeout'
            );
        }
    }
}