<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    protected $fillable = [
        "user",
        "property",
        "date_debut",
        "date_fin",
        "method",
        "amount",
        "phone",
        "otp",
        "status",
    ];

    public function Property() {
        return $this->belongsTo(Property::class, "property")->with("Pictures")->with('User');
    }

    public function User() {
        return $this->belongsTo(User::class, "user");
    }
}
