<?php

namespace Webkul\Project\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Project\Database\Factories\TaskStageFactory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class TaskStage extends Model implements Sortable
{
    use BelongsToCompany;
    use HasCustomFields, HasFactory, SoftDeletes, SortableTrait;

    protected $table = 'projects_task_stages';

    protected $fillable = [
        'name',
        'is_active',
        'is_collapsed',
        'sort',
        'project_id',
        'company_id',
        'user_id',
        'creator_id',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'is_collapsed' => 'boolean',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public static function autoAssignsCompany(): bool
    {
        return false;
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'stage_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($taskStage) {
            $taskStage->creator_id ??= Auth::id();

            $taskStage->company_id ??= static::parentProjectCompanyId($taskStage->project_id);
        });
    }

    /**
     * The owning project's company, read without global scopes.
     *
     * `$taskStage->project` cannot be used here. `Project` carries both
     * `CompanyScope` and a global `OwnershipScope`, so the relation resolves to
     * null for anyone who does not own the project - which for an `individual`
     * user is most projects - and `??=` then leaves `company_id` **null**.
     * `CompanyScope` reads a null company as *shared*, so the stage would be
     * visible to every company on the installation. The failure is silent, which
     * is what makes it dangerous: the stage saves and looks correct.
     *
     * The project's own row decides which company this stage belongs to, not
     * whether the actor is allowed to see that project. Authorization for
     * creating the stage belongs to the policy, before this point.
     */
    protected static function parentProjectCompanyId(?int $projectId): ?int
    {
        if ($projectId === null) {
            return null;
        }

        return Project::withoutGlobalScopes()
            ->whereKey($projectId)
            ->value('company_id');
    }

    protected static function newFactory(): TaskStageFactory
    {
        return TaskStageFactory::new();
    }
}
