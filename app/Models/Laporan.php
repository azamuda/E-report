<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Laporan extends Model
{
    use HasFactory;

    public const STATUES = [
        'Pending' => 'menunggu',
        'proses' => 'diproses',
        'selesai' => 'selesai',
    ];

    protected $fillable = [
        'judul',
        'deskripsi',
        'tanggal',
        'foto',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    public function statusDescription(): string
    {
        return match ($this->status) {
            'proses' => 'pengaduan sedang ditindaklanjuti',
            'selesai' => 'pengaduan telah selesai ditangani',
            default => 'pengaduan menunggu peninjauan admin',
        };
    }
}
