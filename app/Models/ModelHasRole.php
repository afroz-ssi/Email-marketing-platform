<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModelHasRole extends Model
{
    use HasFactory;
    protected $table = "model_has_roles";

    protected $fillable = ['model_id', 'role_id'];

    public function roles(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'model_id', 'id');
    }
}
