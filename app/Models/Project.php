<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'projects';
    protected $fillable = ['user_id', 'name', 'layers', 'status'];

    protected $casts = [
        'layers' => 'array',
    ];

    public function layers()
    {
        return $this->hasMany(Layer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
