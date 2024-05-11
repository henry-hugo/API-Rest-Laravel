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
}
