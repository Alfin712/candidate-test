<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'primary_contact',
        'location',
        'material_certifications',
        'last_audit_date',
        'status',
    ];

    protected $casts = [
        'last_audit_date' => 'date',
    ];

    public function layups(): HasMany
    {
        return $this->hasMany(Layup::class);
    }
}
