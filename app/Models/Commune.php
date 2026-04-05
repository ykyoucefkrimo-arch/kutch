<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Commune extends Model {
    public $timestamps = true;
    protected $fillable = ['wilaya_id','name','name_ar'];
    public function wilaya() { return $this->belongsTo(Wilaya::class); }
    public function orders() { return $this->hasMany(Order::class); }
}
