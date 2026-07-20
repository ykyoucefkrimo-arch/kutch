<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model {
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'category_id','name','slug','description','short_description',
        'price','price_promo','sku','stock','in_stock','is_custom','is_active',
        'is_featured','images','main_image','dimensions','material','color',
        'delivery_days','sort_order'
    ];
    protected $casts = [
        'in_stock'=>'boolean','is_custom'=>'boolean','is_active'=>'boolean',
        'is_featured'=>'boolean','images'=>'array','dimensions'=>'array',
    ];
    public function category() { return $this->belongsTo(Category::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }
    public function getPriceFormattedAttribute(): string {
        return number_format($this->price, 0, ',', ' ') . ' DA';
    }
    public function getCurrentPriceAttribute(): int {
        return $this->price_promo ?? $this->price;
    }
}
