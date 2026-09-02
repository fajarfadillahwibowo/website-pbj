<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Customer;
use Carbon\Carbon;

class Piutang extends Model
{
    use HasFactory;

    protected $table = 'list_piutang';
    protected $primaryKey = 'id_piutang';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'id_penjualan',
        'kode_customer',
        'jumlah_piutang',
        'sisa_piutang',
        'tanggal_terbit',
        'tanggal_jatuh_tempo',
        'status_piutang',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'jumlah_piutang' => 'decimal:2',
        'sisa_piutang' => 'decimal:2',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    protected $appends = [
        'jumlah_piutang_rupiah',
        'sisa_piutang_rupiah',
        'jumlah_terbayar',
        'jumlah_terbayar_rupiah',
        'persentase_terbayar',
        'status_aging',
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
    ];

    public function getJumlahPiutangRupiahAttribute()
    {
        return 'Rp ' . number_format($this->jumlah_piutang ?? 0, 0, ',', '.');
    }

    public function getSisaPiutangRupiahAttribute()
    {
        return 'Rp ' . number_format($this->sisa_piutang ?? 0, 0, ',', '.');
    }

    public function getJumlahTerbayarAttribute()
    {
        return max(0, ($this->jumlah_piutang ?? 0) - ($this->sisa_piutang ?? 0));
    }

    public function getJumlahTerbayarRupiahAttribute()
    {
        return 'Rp ' . number_format($this->jumlah_terbayar, 0, ',', '.') ;
    }

    public function getPersentaseTerbayarAttribute()
    {
        $total = (float) ($this->jumlah_piutang ?? 0);
        if ($total <= 0) return 100;
        $terbayar = (float) $this->jumlah_terbayar;
        return min(100, max(0, round(($terbayar / $total) * 100, 1)));
    }

    public function getStatusAgingAttribute()
    {
        if ($this->status_piutang === 'lunas' || $this->sisa_piutang <= 0) {
            return [
                'kode' => 'lunas',
                'label' => 'Lunas',
                'badge' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20',
                'hari' => 0
            ];
        }

        if (!$this->tanggal_jatuh_tempo) {
            return [
                'kode' => 'normal',
                'label' => 'Tanpa Tempo',
                'badge' => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300',
                'hari' => 0
            ];
        }

        $sekarang = Carbon::today();
        $jatuhTempo = Carbon::parse($this->tanggal_jatuh_tempo)->startOfDay();

        if ($sekarang->gt($jatuhTempo)) {
            $selisih = (int) $sekarang->diffInDays($jatuhTempo);
            return [
                'kode' => 'terlambat',
                'label' => "Lewat {$selisih} Hari",
                'badge' => 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20',
                'hari' => -$selisih
            ];
        }

        if ($sekarang->equalTo($jatuhTempo)) {
            return [
                'kode' => 'hari_ini',
                'label' => 'Jatuh Tempo Hari Ini',
                'badge' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20',
                'hari' => 0
            ];
        }

        $selisih = (int) $sekarang->diffInDays($jatuhTempo);
        return [
            'kode' => 'berjalan',
            'label' => "{$selisih} Hari Lagi",
            'badge' => 'bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-500/20',
            'hari' => $selisih
        ];
    }

    public function getTerakhirDieditRelatifAttribute()
    {
        $waktu = $this->diperbarui_pada ?? $this->dibuat_pada;
        if (!$waktu) return 'Baru dibuat';
        return $waktu->locale('id')->diffForHumans();
    }

    public function getTerakhirDieditWaktuAttribute()
    {
        $waktu = $this->diperbarui_pada ?? $this->dibuat_pada;
        if (!$waktu) return '-';
        return $waktu->format('d/m/Y H:i:s');
    }

    public function penjualan()
    {
        return $this->belongsTo(FakturPenjualan::class, 'id_penjualan', 'id_penjualan');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'kode_customer', 'kode_customer');
    }
}
