<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    public function isFeatured()
    {
        // $this->images()->attach([$image['id'] => ['is_featured_img' => 1]]);
    }

    public function caseStudies()
    {
        return $this->belongsToMany(CaseStudy::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
