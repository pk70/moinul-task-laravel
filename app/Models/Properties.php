<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Properties extends Model
{
    use HasFactory;
     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_customer',
        'listingId',
        'title',
        'address',
        'platform',
    ];

    public $timestamps=true;

    public function getAddressAttribute($value)
    {
        return unserialize($value);
    }

    public function setAddressAttribute($value)
    {
        $this->attributes['address'] = serialize($value);
    }
}
