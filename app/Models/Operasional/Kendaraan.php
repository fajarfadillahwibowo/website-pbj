<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Keuangan\AsetPerusahaan;
use App\Models\Master\JenisAset;
use Carbon\Carbon;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'data_kendaraan';
    protected $primaryKey = 'kode_kendaraan';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_kendaraan',
        'kode_aset',
        'no_polisi',
        'no_mesin',
        'no_rangka',
        'merek_kendaraan',
        'jenis_kendaraan',
        'tipe_armada',
        'muatan',
        'tahun_pembuatan',
        'tanggal_kir',
        'tanggal_pajak',
        'status_kendaraan',
        'nama_pemilik',
    ];

    protected $casts = [
        'tanggal_kir' => 'date',
        'tanggal_pajak' => 'date',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
        'tahun_pembuatan' => 'integer',
    ];

    protected $appends = [
        'nama_aset',
        'kode_aset_display',
        'harga_aset_rupiah',
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
        'status_kir_info',
        'status_pajak_info',
    ];

    /**
     * Relasi ke data aset tetap finansial.
     */
    public function asetPerusahaan()
    {
        return $this->belongsTo(AsetPerusahaan::class, 'kode_aset', 'kode_aset');
    }

    /**
     * Relasi ke kategori / jenis aset kendaraan melalui aset finansial.
     */
    public function jenisAset()
    {
        return $this->hasOneThrough(
            JenisAset::class,
            AsetPerusahaan::class,
            'kode_aset',       // Foreign key pada data_aset
            'kode_jenis_aset', // Foreign key pada data_jenis_aset
            'kode_aset',       // Local key pada data_kendaraan
            'kode_jenis_aset'  // Local key pada data_aset
        );
    }

    /**
     * Accessor alias nama_aset dari relasi aset finansial atau merek.
     */
    public function getNamaAsetAttribute()
    {
        return $this->asetPerusahaan->nama_aset ?? ($this->merek_kendaraan . ' ' . $this->jenis_kendaraan);
    }

    /**
     * Accessor kode_aset untuk tampilan backward-compatible.
     */
    public function getKodeAsetDisplayAttribute()
    {
        return $this->kode_aset ?? $this->kode_kendaraan;
    }

    /**
     * Accessor format Rupiah untuk harga perolehan aset.
     */
    public function getHargaAsetRupiahAttribute()
    {
        $harga = $this->asetPerusahaan->harga_perolehan ?? $this->asetPerusahaan->harga_aset ?? null;
        if ($harga === null) {
            return 'Rp 0';
        }
        return 'Rp ' . number_format($harga, 0, ',', '.');
    }

    /**
     * Accessor riwayat diedit relatif.
     */
    public function getTerakhirDieditRelatifAttribute()
    {
        if (!$this->diperbarui_pada) {
            return 'Baru ditambahkan';
        }
        return $this->diperbarui_pada->locale('id')->diffForHumans();
    }

    /**
     * Accessor waktu diedit format tanggal jam.
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
