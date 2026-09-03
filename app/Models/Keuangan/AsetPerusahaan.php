<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\JenisAset;
use App\Models\Operasional\DataKendaraan;

class AsetPerusahaan extends Model
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
        'harga_perolehan',
        'nilai_residu',
        'umur_manfaat',
        'metode_penyusutan',
        'tarif_penyusutan',
        'kode_akun_aset',
        'kode_akun_akumulasi',
        'kode_akun_beban',
        'akumulasi_penyusutan',
        'nilai_buku',
        'status_aset',
        'nama_pemilik',
        'no_polisi',
        'no_mesin',
        'no_rangka',
        'merek_aset',
        'jenis_kendaraan',
        'muatan',
    ];

    protected $casts = [
        'tanggal_pembelian' => 'date',
        'harga_aset' => 'decimal:2',
        'harga_perolehan' => 'decimal:2',
        'nilai_residu' => 'decimal:2',
        'tarif_penyusutan' => 'decimal:2',
        'akumulasi_penyusutan' => 'decimal:2',
        'nilai_buku' => 'decimal:2',
        'umur_manfaat' => 'integer',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    /**
     * Relasi ke Master Jenis Aset.
     */
    public function jenisAset()
    {
        return $this->belongsTo(JenisAset::class, 'kode_jenis_aset', 'kode_jenis_aset');
    }

    /**
     * Relasi ke data fisik armada operasional (jika bertipe kendaraan).
     */
    public function dataKendaraan()
    {
        return $this->hasOne(DataKendaraan::class, 'kode_aset', 'kode_aset');
    }

    /**
     * Relasi ke histori riwayat penyusutan bulanan.
     */
    public function riwayatPenyusutan()
    {
        return $this->hasMany(RiwayatPenyusutan::class, 'kode_aset', 'kode_aset')->orderBy('tanggal_penyusutan', 'desc');
    }

    /**
     * Menghitung taksiran beban penyusutan per bulan.
     */
    public function hitungPenyusutanBulanan(): float
    {
        if ($this->metode_penyusutan === 'Tidak Disusutkan' || $this->umur_manfaat <= 0) {
            return 0.00;
        }

        $nilaiBuku = (float) ($this->nilai_buku ?? $this->harga_perolehan);
        $nilaiResidu = (float) ($this->nilai_residu ?? 0);

        if ($nilaiBuku <= $nilaiResidu) {
            return 0.00;
        }

        $hargaPokok = (float) ($this->harga_perolehan ?? $this->harga_aset);

        if ($this->metode_penyusutan === 'Garis Lurus') {
            $penyusutanTahunan = ($hargaPokok - $nilaiResidu) / $this->umur_manfaat;
            $penyusutanBulanan = $penyusutanTahunan / 12;
            $sisaBuku = $nilaiBuku - $nilaiResidu;
            return min($penyusutanBulanan, $sisaBuku);
        }

        if ($this->metode_penyusutan === 'Saldo Menurun') {
            $tarif = (float) $this->tarif_penyusutan;
            if ($tarif <= 0) {
                $tarif = (100 / $this->umur_manfaat) * 2; // Double declining
            }
            $penyusutanTahunan = $nilaiBuku * ($tarif / 100);
            $penyusutanBulanan = $penyusutanTahunan / 12;
            $sisaBuku = $nilaiBuku - $nilaiResidu;
            return min($penyusutanBulanan, $sisaBuku);
        }

        return 0.00;
    }
}
