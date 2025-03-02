<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'author',
        'total_pages',
        'current_page',
        'status',
        'rating',
        'time_spent',
        // New fields
        'isbn',
        'cover_url',
        'description',
        'publication_year',
        'genres'
    ];

    // Cast the genres JSON field to array
    protected $casts = [
        'genres' => 'array',
        'rating' => 'integer',
        'total_pages' => 'integer',
        'current_page' => 'integer',
        'time_spent' => 'integer',
        'publication_year' => 'integer'
    ];

    // Define the valid status values
    const STATUS_WANT_TO_READ = 'want_to_read';
    const STATUS_READING = 'reading';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DROPPED = 'dropped';

    // Define valid statuses
    const VALID_STATUSES = [
        self::STATUS_WANT_TO_READ,
        self::STATUS_READING,
        self::STATUS_COMPLETED,
        self::STATUS_DROPPED
    ];

    // Relationship with User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helper method to get reading progress percentage
    public function getProgressPercentage(): float
    {
        if ($this->total_pages === 0) return 0;
        return round(($this->current_page / $this->total_pages) * 100, 2);
    }

    // Helper method to format time spent
    public function getFormattedTimeSpent(): string
    {
        $hours = floor($this->time_spent / 60);
        $minutes = $this->time_spent % 60;
        
        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        
        return "{$minutes}m";
    }

    // Helper method to get cover URL or default image
    public function getCoverUrl(): string
    {
        return $this->cover_url ?? 'https://via.placeholder.com/200x300?text=No+Cover';
    }

    // Helper method to get truncated description
    public function getTruncatedDescription(int $length = 150): string
    {
        if (!$this->description) {
            return 'No description available.';
        }
        
        return strlen($this->description) > $length 
            ? substr($this->description, 0, $length) . '...'
            : $this->description;
    }
}