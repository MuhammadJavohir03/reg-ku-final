<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'section_id',
        'body',
        'status',
        'rozilik',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    const STATUS_UNREAD = 0;
    const STATUS_READ = 1;

    const ROZILIK_PENDING = 0;
    const ROZILIK_ACCEPTED = 1;

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    
    
    

    
    public function scopeBetweenUsers(Builder $query, int $userA, int $userB): Builder
    {
        return $query->whereNull('section_id')->where(function ($q) use ($userA, $userB) {
            $q->where(['sender_id' => $userA, 'receiver_id' => $userB])
              ->orWhere(['sender_id' => $userB, 'receiver_id' => $userA]);
        });
    }

    
    public function scopeForSectionConversation(Builder $query, int $sectionId, int $studentId): Builder
    {
        return $query->where('section_id', $sectionId)
            ->where(function ($q) use ($studentId) {
                $q->where('sender_id', $studentId)
                  ->orWhere('receiver_id', $studentId);
            });
    }

    
    
    

    
    public static function isApproved(int $userA, int $userB): bool
    {
        return static::betweenUsers($userA, $userB)
            ->where('rozilik', self::ROZILIK_ACCEPTED)
            ->exists();
    }

    
    public static function pendingRequestSender(int $userA, int $userB): ?int
    {
        $first = static::betweenUsers($userA, $userB)->oldest()->first();

        if (!$first) {
            return null;
        }

        if ($first->rozilik === self::ROZILIK_ACCEPTED) {
            return null;
        }

        return $first->sender_id;
    }

    
    public static function acceptRequest(int $requesterId, int $receiverId): void
    {
        static::betweenUsers($requesterId, $receiverId)
            ->update(['rozilik' => self::ROZILIK_ACCEPTED]);
    }

    
    
    

    public function markRead(): void
    {
        if ($this->status !== self::STATUS_READ) {
            $this->update(['status' => self::STATUS_READ, 'read_at' => now()]);
        }
    }
}
