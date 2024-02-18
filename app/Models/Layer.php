<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layer extends Model
{
    use HasFactory;
    protected $table = 'layers';
    protected $fillable = ['name', 'image_cids', 'index'];

    protected $casts = [
        'image_cids' => 'array',
    ];

    protected function getImageCidsAttribute($value)
    {
        return array_values(json_decode($value, true) ?? []);
    }

    protected function setImageCidsAttribute($value)
    {

        $valueArray = is_string($value) ? json_decode($value, true) : $value;

        $this->attributes['image_cids'] = json_encode(array_map(function ($item) {
            return ['cid' => $item['cid'], 'name' => $item['name']];
        }, $valueArray));
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
