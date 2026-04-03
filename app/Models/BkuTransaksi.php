<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BkuTransaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'no_bukti',
        'kode_rekening',
        'kode_sub_kegiatan',
        'nama_sub_kegiatan',
        'uraian',
        'penerima',
        'nominal',
        'jenis_pencairan',
        'qr_code_hash',
        'status_cetak',
        'pptk_id',
        'pph21',
        'pph22',
        'pph23',
        'ppn',
        'pajak_daerah',
        'pph4_final',
        'status_validasi',
        'nama_camat',
        'nip_camat',
        'nama_pptk',
        'nip_pptk',
        'nama_bendahara',
        'nip_bendahara',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'status_cetak' => 'boolean',
        'status_validasi' => 'boolean',
    ];

    public function pptk()
    {
        return $this->belongsTo(Pejabat::class, 'pptk_id');
    }
}
