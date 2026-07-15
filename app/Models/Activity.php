<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'tanggal_berlangsung',
        'tanggal_berakhir',
        'status',
        'description',
        'attachment',
        'unit_id',
        'catatan_revisi',
        'category_id',
        'realization_file',
    ];

    protected $casts = [
        'attachment' => 'array',
        'realization_file' => 'array',
        'pending_at' => 'datetime',
        'revisi_at' => 'datetime',
        'reject_at' => 'datetime',
        'realisasi_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Catat waktu setiap kali status berubah.
     * Bisa berulang-ulang, timestamp selalu ditimpa dengan yang terbaru.
     */
    protected static function booted(): void
    {
        static::saving(function (Activity $activity) {
            if (! $activity->isDirty('status')) {
                return;
            }

            $map = [
                'pending'         => 'pending_at',
                'revisi'          => 'revisi_at',
                'reject'          => 'reject_at',
                'dalam_realisasi' => 'realisasi_at',
                'completed'       => 'completed_at',
            ];

            if (isset($map[$activity->status])) {
                $activity->{$map[$activity->status]} = now();
            }
        });
    }

    /**
     * Relasi ke user pengaju aktivitas
     */
    public function pengurus_unit(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias relasi user untuk notifikasi Filament
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke unit
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Relasi ke kategori
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}