<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'post_id',
        'user_id',
        'content',
        'image'
    ];
    // Explicit table name for this model. Laravel can infer "comments",
    // but declaring it makes the intent explicit and prevents breakage if
    // the class name or default conventions change.
    protected $table = 'comments';

    /**
     * The user who wrote the comment.
     * Relationship: many comments belong to one user (author/owner).
     * Returns: BelongsTo relation to the User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The post that this comment is attached to.
     * Relationship: a comment belongs to a single Post.
     * Use this to load the parent post or constrain queries by post.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Polymorphic "karma" entries (upvotes/downvotes) related to this comment.
     * Using morphMany lets both posts and comments share the same Karma model.
     */
    public function karma()
    {
        return $this->morphMany(Karma::class, 'karmaable');
    }

    /**
     * Compute the net karma score: (upvotes - downvotes).
     * Note: this issues two count queries; for hot paths consider eager
     * loading or maintaining an aggregate column for performance.
     */
    public function karmaScore()
    {
        return $this->karma()->where('type', Karma::UPVOTE)->count()
             - $this->karma()->where('type', Karma::DOWNVOTE)->count();
    }

    
}