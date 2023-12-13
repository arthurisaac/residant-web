<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;
    protected $fillable = [
        'user',
        'property',
    ];

    public function Property() {
        return $this->belongsTo(Property::class, 'property')->with("Pictures")->with("User");
    }

    public function User() {
        return $this->belongsTo(User::class, 'property');
    }
}
