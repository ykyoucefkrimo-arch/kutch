<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model {
    protected $fillable = ['order_id','product_id','new_arrival_id','product_name','unit_price','quantity','subtotal','product_options'];
    protected $casts = ['product_options'=>'array'];
    public function order() { return $this->belongsTo(Order::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function newArrival() { return $this->belongsTo(NewArrival::class); }
}
