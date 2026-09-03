<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Autentikasi\Jabatan;

class Driver extends Model
{
    use HasFactory;

    protected $table = 'data_karyawan';
    protected $primaryKey = 'kode_karyawan';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_karyawan',
        'nama_karyawan',
        'id_jabatan',
        'kategori_karyawan',
        'no_identitas',
        'alamat',
        'no_hp',
        'foto_ktp',
        'file_kontrak',
        'status_karyawan',
        'tanggal_mulai_kerja',
        'tanggal_berhenti',
    ];

    protected $casts = [
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
        'tanggal_mulai_kerja' => 'date',
        'tanggal_berhenti' => 'date',
    ];

    protected $appends = [
        'no_ktp',
        'no_ktp_format',
        'foto_ktp_url',
        'file_kontrak_url',
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
    ];

    /**
     * Scope global untuk otomatis memfilter hanya karyawan dengan kategori 'driver'.
     */
    protected static function booted()
    {
        static::addGlobalScope('driver_only', function (Builder $builder) {
            $builder->where('kategori_karyawan', 'driver');
        });

        static::creating(function ($driver) {
            $driver->kategori_karyawan = 'driver';
        });
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id_jabatan');
    }

    /**
     * Accessor untuk no_ktp (alias kolom database no_identitas).
     */
    public function getNoKtpAttribute()
    {
        return $this->attributes['no_identitas'] ?? null;
    }

    /**
     * Accessor format khusus No. KTP / NIK 16 digit khas Indonesia (contoh: 3201 0203 0405 0001).
     */
    public function getNoKtpFormatAttribute()
    {
        $nik = preg_replace('/[^0-9]/', '', $this->attributes['no_identitas'] ?? '');
        if (strlen($nik) === 16) {
            return substr($nik, 0, 4) . ' ' . substr($nik, 4, 4) . ' ' . substr($nik, 8, 4) . ' ' . substr($nik, 12, 4);
        }
        return $this->attributes['no_identitas'] ?? '-';
    }

    /**
     * Mutator untuk no_ktp (membersihkan format agar tersimpan 16 digit angka murni di database).
     */
    public function setNoKtpAttribute($value)
    {
        $this->attributes['no_identitas'] = $value ? preg_replace('/[^0-9]/', '', $value) : null;
    }

    /**
     * Mutator untuk no_identitas.
     */
    public function setNoIdentitasAttribute($value)
    {
        $this->attributes['no_identitas'] = $value ? preg_replace('/[^0-9]/', '', $value) : null;
    }

    /**
     * Accessor URL berkas Foto KTP.
     */
    public function getFotoKtpUrlAttribute()
    {
        if (!empty($this->foto_ktp) && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->foto_ktp)) {
            return asset('storage/' . $this->foto_ktp);
        }
        return null;
    }

    /**
     * Accessor URL berkas File Kontrak.
     */
    public function getFileKontrakUrlAttribute()
    {
        if (!empty($this->file_kontrak) && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->file_kontrak)) {
            return asset('storage/' . $this->file_kontrak);
        }
        return null;
    }

    /**
     * Accessor riwayat diedit relatif waktu (contoh: "2 menit yang lalu", "Baru saja").
     */
    public function getTerakhirDieditRelatifAttribute()
    {
        $waktu = $this->diperbarui_pada ?? $this->dibuat_pada;
        if (!$waktu) {
            return 'Baru ditambahkan';
        }
        return $waktu->locale('id')->diffForHumans();
    }

    /**
     * Accessor format tanggal jam riwayat diedit (contoh: "02/09/2026 09:15:00").
     */
    public function getTerakhirDieditWaktuAttribute()
    {
        $waktu = $this->diperbarui_pada ?? $this->dibuat_pada;
        if (!$waktu) {
            return '-';
        }
        return $waktu->format('d/m/Y H:i:s');
    }
}
