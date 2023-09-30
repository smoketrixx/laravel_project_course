<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Post extends Model
{
    use HasFactory;
    use Searchable;
    

    protected $fillable = ['title', 'content','user_id' ];

    public function toSearchableArray(){
        return [
          'title' => $this->title,
          'content' => $this->content
        ];
    }
    
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
