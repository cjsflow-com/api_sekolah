<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeComponent extends Model
{
    protected $fillable = [
        'name',
    ];

    public function teachingAssignments(): BelongsToMany
    {
        return $this->belongsToMany(
            TeachingAssignment::class,
            'teaching_grade_components',
            'grade_component_id',
            'teaching_assignment_id'
        )
            ->withPivot('weight')
            ->withTimestamps();
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }
}
