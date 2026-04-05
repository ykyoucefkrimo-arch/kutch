<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model {
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'order_number','customer_name','customer_phone','customer_phone2',
        'customer_email','wilaya_id','commune_id','address','status',
        'payment_method','subtotal','shipping_cost','total','notes',
        'admin_notes','ip_address','user_agent','fraud_score','is_flagged',
        'honeypot_triggered'
    ];
    protected $casts = ['is_flagged'=>'boolean','honeypot_triggered'=>'boolean'];
    public function wilaya() { return $this->belongsTo(Wilaya::class); }
    public function commune() { return $this->belongsTo(Commune::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function logs() { return $this->hasMany(OrderLog::class)->orderBy('created_at','desc'); }
    public static function generateOrderNumber(): string {
        $year = date('Y');
        $last = static::whereYear('created_at', $year)->max('id') ?? 0;
        return 'EB-' . $year . '-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    }
    public function getStatusLabelAttribute(): string {
        return match($this->status) {
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'processing' => 'En préparation',
            'shipped' => 'Expédiée',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
            default => $this->status,
        };
    }
    public function getStatusColorAttribute(): string {
        return match($this->status) {
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'processing' => 'indigo',
            'shipped' => 'purple',
            'delivered' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }
}
