<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Support\Facades\Http;

class ExternalApiController extends Controller
{
    /**
     * Skenario 11: GET Referensi Eksternal OpenLibrary API
     */
    public function getOpenLibraryReference($id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json(['message' => 'Data buku lokal tidak ditemukan.'], 404);
        }

        // Hapus tanda strip (jika ada) pada ISBN untuk query API
        $isbn = str_replace('-', '', $buku->isbn);
        $bookKey = "ISBN:{$isbn}";

        // Memanggil OpenLibrary API menggunakan Laravel HTTP Client
        $response = Http::withoutVerifying()
            ->timeout(10)
            ->get("https://openlibrary.org/api/books?bibkeys={$bookKey}&format=json&jscmd=data");

        $externalData = $response->json();
        $externalFound = !empty($externalData) && isset($externalData[$bookKey]);
        $metadata = null;

        if ($externalFound) {
            $extBook = $externalData[$bookKey];
            $metadata = [
                'title' => $extBook['title'] ?? null,
                'subtitle' => $extBook['subtitle'] ?? null,
                'number_of_pages' => $extBook['number_of_pages'] ?? null,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Metadata buku eksternal berhasil diperkaya dari OpenLibrary API.',
            'data' => [
                'local_book' => [
                    'id' => $buku->id,
                    'isbn' => $buku->isbn,
                    'judul' => $buku->judul,
                    'penulis' => $buku->penulis,
                    'stok' => $buku->stok,
                ],
                'external_provider' => 'OpenLibrary REST API (https://openlibrary.org)',
                'external_found' => $externalFound,
                'external_metadata' => $metadata
            ]
        ], 200);
    }

    /**
     * Skenario 12: GET GitHub Portfolio API
     */
    public function getGithubPortfolio($username)
    {
        // 1. Panggil API Data Profil GitHub
        $profileResponse = Http::withoutVerifying()
            ->timeout(10)
            ->get("https://api.github.com/users/{$username}");

        if ($profileResponse->failed()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dari GitHub API. Pastikan username valid.'
            ], $profileResponse->status());
        }

        $profile = $profileResponse->json();

        // 2. Panggil API Data Repositori GitHub
        $repoResponse = Http::withoutVerifying()
            ->timeout(10)
            ->get("https://api.github.com/users/{$username}/repos?sort=updated&per_page=3");

        $latestRepos = [];
        if ($repoResponse->successful()) {
            foreach ($repoResponse->json() as $repo) {
                $latestRepos[] = [
                    'repo_name' => $repo['name'] ?? null,
                    'language' => $repo['language'] ?? 'N/A',
                    'stars' => $repo['stargazers_count'] ?? 0,
                    'html_url' => $repo['html_url'] ?? null,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Data portofolio koding mahasiswa berhasil diambil dari GitHub API.',
            'data' => [
                'github_login' => $profile['login'] ?? null,
                'display_name' => $profile['name'] ?? null,
                'bio' => $profile['bio'] ?? null,
                'public_repos' => $profile['public_repos'] ?? 0,
                'followers' => $profile['followers'] ?? 0,
                'profile_url' => $profile['html_url'] ?? null,
                'latest_repos' => $latestRepos
            ]
        ], 200);
    }
}