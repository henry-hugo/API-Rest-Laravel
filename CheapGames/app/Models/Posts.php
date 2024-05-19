<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    use HasFactory;
    protected $table = 'Posts';
    protected $primaryKey = 'PostID';
    protected $fillable = ['UserID', 'Title', 'Description', 'Active', 'Date', 'CategoryID', 'PlatformID', 'NewPrice', 'OldPrice', 'Link'];


    public $timestamps = false;


    public function images()
    {
        return $this->hasMany(Images::class, 'PostID', 'PostID');
    }
    
    // No modelo Post
    public function category()
    {
        return $this->belongsTo(Category::class, 'CategoryID');
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class, 'PlatformID');
    }
    public function ratings()
    {
        return $this->hasMany(Ratings::class, 'PostID', 'PostID');
    }

    public function ratingsSum()
    {
        $likes = $this->ratings->sum('Liked');
        $dislikes = $this->ratings->count() - $likes;

        return [
            'likes' => $likes,
            'dislikes' => $dislikes,
        ];
    }

}
