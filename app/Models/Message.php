<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'chat_id',
        'sender_id',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    // ─── Relations ──────────────────────────────────────────────────────────────

    /** Message belongs to a chat */
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    /** Message belongs to sender */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
