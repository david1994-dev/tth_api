<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function thongBao()
    {
        return $this->belongsToMany(User::class, 'thong_bao_users', 'user_id', 'thong_bao_id');
    }

    public function nhanVien()
    {
        return $this->hasOne(NhanVien::class, 'user_id', 'id');
    }

    public function getNhomNhanSuAttribute()
    {
        return NhomNhanSu::query()->whereJsonContains('user_ids', $this->id)->pluck('id')->toArray();
    }
}
