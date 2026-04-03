<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggaran extends Model
{
    use HasFactory;

    protected $fillable = ['pagu', 'tahun', 'jenis', 'kode', 'uraian', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(Anggaran::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Anggaran::class, 'parent_id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function getPaguRecursive()
    {
        if ($this->children()->count() == 0) {
            return $this->pagu;
        }

        return $this->children->sum(fn($child) => $child->getPaguRecursive());
    }

    public function getRealisasi()
    {
        // If it's a leaf node (e.g. Sub Kegiatan), sum from BkuTransaksi
        // Otherwise, sum from children's realisasi
        if ($this->children()->count() == 0) {
            return \App\Models\BkuTransaksi::where('kode_sub_kegiatan', $this->kode)
                ->whereYear('tanggal', $this->tahun)
                ->sum('nominal');
        }

        return $this->children->sum(fn($child) => $child->getRealisasi());
    }
}
