<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    use HasFactory;
    protected $table = 'Images';
    protected $primaryKey = 'ImageID';
    protected $fillable = ['PostID', 'ImageURL', 'Active', 'Date'];

    public $timestamps = false;



    public function post()
{
    return $this->belongsTo(Posts::class, 'PostID', 'PostID');
}

}
