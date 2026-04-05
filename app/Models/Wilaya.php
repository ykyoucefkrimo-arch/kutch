<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Wilaya extends Model {
    public $timestamps = true;
    protected $fillable = ['code','name','name_ar'];
    public function communes() { return $this->hasMany(Commune::class); }
    public function orders() { return $this->hasMany(Order::class); }
}
