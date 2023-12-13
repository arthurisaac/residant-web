<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;
    protected $fillable = [
        'user',
        'nom',
        'quartier',
        'ville',
        'pays',
        'latitude',
        'longitude',
        'description',
        'image_principale',
        'type_propriete',
        'superficie',
        'superficie_unite',
        'nombre_chambre',
        'nombre_bain',
        'nombre_parking',
        'meublee',
        'nombre_personne_max',
        'prix',
        'type_prix',
        'reduction_nombre_reservation',
        'minimum_reservation',
        'reduction',
        'vue',
        'partage',
    ];

    public function Pictures() {
        return $this->hasMany(PropertyPicture::class, 'property');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'user');
    }

    public function Favorite() {
        return $this->hasMany(Favorite::class, "property");
    }
}
