<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Peminjaman extends Model
{
    // Wajib eksplisit: pluralisasi otomatis Laravel bisa menghasilkan "peminjamen".
    protected $table = 'peminjamans';

    protected $fillable = [
        'kode_pinjam',
        'buku_id',
        'nim_peminjam',
        'nama_peminjam',
        'tanggal_pinjam',
        'tanggal_jatuh_tempo',
        'tanggal_kembali',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam'      => 'date',
            'tanggal_jatuh_tempo' => 'date',
            'tanggal_kembali'     => 'date',
        ];
    }

    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class);
    }

    /** Dipakai di view sebagai $peminjaman->terlambat */
    public function getTerlambatAttribute(): bool
    {
        return $this->status === 'dipinjam'
            && $this->tanggal_jatuh_tempo->lt(today());
    }
}