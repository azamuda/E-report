<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    public const STATUES = [
        'Pedding' => 'menunggu'
        'proses' => 'diproses'
        'selesai' => 'selesai'
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

    protected function pelapor(): BelongTo
    {
        return $this->belongTo(user::class, 'pelapor_id' );
    }

    public function statusDescription(): string
    {
        return match ($this->status) {
            'proses' => 'pengaduan sedang ditindaklanjuti',
            'selesai' => 'pengaduan telah selesai ditangani',
            'default' => 'pengaduan menunggu peninjauan admin',
            };
    }