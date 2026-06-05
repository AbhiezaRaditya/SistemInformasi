<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{

    protected $fillable = ['user_id', 'title', 'tanggal_berlangsung','tanggal_berakhir', 'status','description','attachment','unit_id','catatan_revisi','category_id'];

    protected $casts = [
        'attachment' => 'array',
    ];

    public function pengurus_unit():BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

     public function unit():BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function category():BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
