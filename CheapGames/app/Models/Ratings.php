<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ratings extends Model
{
    use HasFactory;
    protected $table = 'Ratings';
    protected $primaryKey = 'RatingID';
    protected $fillable = ['PostID', 'UserID', 'Liked', 'Date'];

    public $timestamps = false;
}
