<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'guest_name',
        'guest_email',
        'content',
        'status',
        'ip_address',
        'verification_token',
        'email_verified_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    // Relationships
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes for filtering
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Helper methods
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isVerified()
    {
        return $this->email_verified_at !== null;
    }

    public function getAuthorNameAttribute()
    {
        return $this->user ? $this->user->name : $this->guest_name;
    }
}