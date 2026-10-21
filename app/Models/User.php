<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'email', 'password', 'full_name', 'phone', 'address', 'role', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /** Lấy (hoặc tạo) giỏ hàng của user */
    public function getOrCreateCart()
    {
        return $this->cart ?: $this->cart()->create();
    }

    /** "KH001" như trang quản trị người dùng */
    public function getCustomerCodeAttribute()
    {
        return 'KH' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }
}
