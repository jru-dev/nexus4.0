<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'forum_post_id',
        'user_id',
        'parent_reply_id',
        'content',
        'images',
        'is_approved',
        'likes',
    ];

    protected $casts = [
        'images' => 'array',
        'is_approved' => 'boolean',
    ];

    // Relaciones
    public function forumPost()
    {
        return $this->belongsTo(ForumPost::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parentReply()
    {
        return $this->belongsTo(ForumReply::class, 'parent_reply_id');
    }

    public function childReplies()
    {
        return $this->hasMany(ForumReply::class, 'parent_reply_id');
    }

    // Boot method para actualizar contador de respuestas
    public static function boot()
    {
        parent::boot();
        
        static::created(function ($reply) {
            $reply->forumPost->increment('replies_count');
            $reply->forumPost->updateLastActivity();
        });
        
        static::deleted(function ($reply) {
            $reply->forumPost->decrement('replies_count');
        });
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_reply_id');
    }
}