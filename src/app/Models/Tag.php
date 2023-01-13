<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tags';

    protected $primaryKey = 'id';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'user_id',
    ];


    /**
     * Get the user that owns tag.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user that owns task.
     */
    public function tasks()
    {
        return $this->belongsToMany(Tag::class, 'task_tag');
    }


}
