<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class Project extends Model
{
    protected $guarded = ['id'];
    protected function casts(): array { return ['is_published'=>'boolean', 'approved_for_publication'=>'boolean']; }
    public function scopePublished(Builder $query): Builder {
        return $query->where('is_published', true)->where('approved_for_publication', true)->whereIn('relationship', ['client', 'own']);
    }
    protected static function booted(): void {
        static::saving(function (Project $project) {
            if (! $project->approved_for_publication || ! in_array($project->relationship, ['client','own'])) {
                $project->is_published = false;
            }
        });
    }
}
