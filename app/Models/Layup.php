<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layup extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'clt_layups';

    protected $fillable = [
        'supplier_id',
        'name',
        'description',
        'specification_code',
        'ply_count',
        'grade',
        'status',
    ];

    protected $casts = [
        'ply_count' => 'integer',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function layers(): HasMany
    {
        return $this->hasMany(Layer::class)->orderBy('layer_order');
    }
}

