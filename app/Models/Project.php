<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    public function images()
    {
        return $this->belongsToMany(Image::class)->withPivot('is_featured_img', 'sort_order');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function Categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
