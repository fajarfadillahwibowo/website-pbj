<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\JenisAset;
use Carbon\Carbon;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'data_aset';
    protected $primaryKey = 'kode_aset';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_aset',
        'kode_jenis_aset',
        'nama_aset',
        'tanggal_pembelian',
        'harga_aset',
        'no_polisi',
        'no_mesin',
        'no_rangka',
        'merek_aset',
        'muatan',
        'jenis_kendaraan',
        'tahun_pembuatan',
        'tanggal_kir',
        'tanggal_pajak',
        'status_aset',
        'nama_pemilik',
    ];

    protected $casts = [
        'tanggal_pembelian' => 'date',
        'tanggal_kir' => 'date',
        'tanggal_pajak' => 'date',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
        'harga_aset' => 'decimal:2',
        'tahun_pembuatan' => 'integer',
    ];

    protected $appends = [
        'harga_aset_rupiah',
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
        'status_kir_info',
        'status_pajak_info',
    ];

    /**
     * Relasi ke Master Jenis Aset.
     */
    public function jenisAset()
    {
        return $this->belongsTo(JenisAset::class, 'kode_jenis_aset', 'kode_jenis_aset');
    }

    /**
     * Accessor format Rupiah untuk harga perolehan aset.
     */
    public function getHargaAsetRupiahAttribute()
    {
        if ($this->harga_aset === null) {
            return 'Rp 0';
        }
        return 'Rp ' . number_format($this->harga_aset, 0, ',', '.');
    }

    /**
     * Accessor riwayat diedit relatif (contoh: "3 menit yang lalu", "Baru saja").
     */
    public function getTerakhirDieditRelatifAttribute()
    {
        if (!$this->diperbarui_pada) {
            return 'Baru ditambahkan';
        }
        return $this->diperbarui_pada->locale('id')->diffForHumans();
    }

    /**
     * Accessor tanggal jam riwayat diedit (contoh: "02/09/2026 09:20:00").
     */
    public function getTerakhirDieditWaktuAttribute()
    {
        if (!$this->diperbarui_pada) {
            return '-';
        }
        return $this->diperbarui_pada->format('d/m/Y H:i:s');
    }

    /**
     * Accessor status masa berlaku uji KIR Dishub.
     */
    public function getStatusKirInfoAttribute()
    {
        if (!$this->tanggal_kir) {
            return [
                'status' => 'kosong',
                'label' => 'Belum Diatur',
                'warna' => 'slate',
                'sisa_hari' => null,
            ];
        }

        $hariIni = Carbon::today();
        $tglKir = Carbon::parse($this->tanggal_kir);
        $selisihHari = (int) $hariIni->diffInDays($tglKir, false);

        if ($selisihHari < 0) {
            return [
                'status' => 'kadaluarsa',
                'label' => 'Lewat ' . abs($selisihHari) . ' Hari',
                'warna' => 'rose',
                'sisa_hari' => $selisihHari,
            ];
        } elseif ($selisihHari <= 30) {
            return [
                'status' => 'peringatan',
                'label' => 'Sisa ' . $selisihHari . ' Hari',
                'warna' => 'amber',
                'sisa_hari' => $selisihHari,
            ];
        }

        return [
            'status' => 'aman',
            'label' => 'Aktif (' . $tglKir->format('d/m/Y') . ')',
            'warna' => 'emerald',
            'sisa_hari' => $selisihHari,
        ];
    }

    /**
     * Accessor status masa berlaku Pajak STNK Tahunan.
     */
    public function getStatusPajakInfoAttribute()
    {
        if (!$this->tanggal_pajak) {
            return [
                'status' => 'kosong',
                'label' => 'Belum Diatur',
                'warna' => 'slate',
                'sisa_hari' => null,
            ];
        }

        $hariIni = Carbon::today();
        $tglPajak = Carbon::parse($this->tanggal_pajak);
        $selisihHari = (int) $hariIni->diffInDays($tglPajak, false);

        if ($selisihHari < 0) {
            return [
                'status' => 'kadaluarsa',
                'label' => 'Lewat ' . abs($selisihHari) . ' Hari',
                'warna' => 'rose',
                'sisa_hari' => $selisihHari,
            ];
        } elseif ($selisihHari <= 30) {
            return [
                'status' => 'peringatan',
                'label' => 'Sisa ' . $selisihHari . ' Hari',
                'warna' => 'amber',
                'sisa_hari' => $selisihHari,
            ];
        }

        return [
            'status' => 'aman',
            'label' => 'Aktif (' . $tglPajak->format('d/m/Y') . ')',
            'warna' => 'emerald',
            'sisa_hari' => $selisihHari,
        ];
    }
}
