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
        'realization_file' => 'array'
    ];


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