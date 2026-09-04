<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Customer;

class FakturPenjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';
    protected $primaryKey = 'id_penjualan';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nomor_faktur',
        'tanggal_penjualan',
        'kode_customer',
        'kode_toko',
        'kode_barang',
        'nama_barang',
        'satuan_barang',
        'jumlah_zak',
        'harga_satuan',
        'metode_pembayaran',
        'total_bruto',
        'diskon',
        'total_netto',
        'jumlah_dibayar',
        'sisa_piutang',
        'status_pembayaran',
        'jatuh_tempo',
        'id_rekening',
        'status_persetujuan',
        'dibuat_oleh',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'kode_customer', 'kode_customer');
    }

    public function tokoBangunan()
    {
        return $this->belongsTo(\App\Models\Master\TokoBangunan::class, 'kode_toko', 'kode_toko');
    }

    public function barang()
    {
        return $this->belongsTo(\App\Models\Master\Barang::class, 'kode_barang', 'kode_barang');
    }

    public function piutang()
    {
        return $this->hasOne(Piutang::class, 'id_penjualan', 'id_penjualan');
    }
}
