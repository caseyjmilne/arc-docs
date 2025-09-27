<?php

namespace ARC\Docs\Models;

use \Illuminate\Database\Eloquent\Model;

class Doc extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'docs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'status',
        'author_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from title if not provided
        static::creating(function ($doc) {
            if (empty($doc->slug) && !empty($doc->title)) {
                $doc->slug = sanitize_title($doc->title);
            }
        });

        // Auto-set author_id if not provided
        static::creating(function ($doc) {
            if (empty($doc->author_id)) {
                $doc->author_id = get_current_user_id();
            }
        });
    }

    /**
     * Scope a query to only include published docs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to only include draft docs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to filter by author.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $authorId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByAuthor($query, $authorId)
    {
        return $query->where('author_id', $authorId);
    }

    /**
     * Get the author (WordPress user).
     *
     * @return \WP_User|false
     */
    public function getAuthor()
    {
        return get_userdata($this->author_id);
    }

    /**
     * Get the author name.
     *
     * @return string
     */
    public function getAuthorNameAttribute()
    {
        $author = $this->getAuthor();
        return $author ? $author->display_name : 'Unknown';
    }

    /**
     * Get the permalink for the doc.
     *
     * @return string
     */
    public function getPermalinkAttribute()
    {
        return home_url('/doc/' . $this->slug);
    }

    /**
     * Check if doc is published.
     *
     * @return bool
     */
    public function isPublished()
    {
        return $this->status === 'published';
    }

    /**
     * Check if doc is draft.
     *
     * @return bool
     */
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    /**
     * Publish the doc.
     *
     * @return bool
     */
    public function publish()
    {
        $this->status = 'published';
        return $this->save();
    }

    /**
     * Unpublish the doc (set to draft).
     *
     * @return bool
     */
    public function unpublish()
    {
        $this->status = 'draft';
        return $this->save();
    }
}