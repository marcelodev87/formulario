<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\ProcessStatusTimeline;

class Process extends Model
{
    use HasFactory;

    public const TYPE_INSTITUTION_OPENING = 'institution_opening';
    public const TYPE_BRANCH_OPENING = 'branch_opening';
    public const TYPE_BOARD_ELECTION_MINUTES_REGISTRATION = 'board_election_minutes_registration';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_PENDING_UPDATES = 'pending_updates';

    /** @var array<int, string> */
    protected $fillable = [
        'institution_id',
        'type',
        'title',
        'status',
        'meta',
        'answers',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'meta' => 'array',
        'answers' => 'array',
    ];

    protected static ?array $typeDefinitionsCache = null;

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function location(): HasOne
    {
        return $this->hasOne(Location::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function statusTimeline(): HasMany
    {
        return $this->hasMany(ProcessStatusTimeline::class)->orderByDesc('created_at');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ProcessType::class, 'type', 'slug');
    }

    /**
     * @return array<string, array<string, string|null>>
     */
    public static function typeDefinitions(): array
    {
        if (self::$typeDefinitionsCache !== null) {
            return self::$typeDefinitionsCache;
        }

        $definitions = ProcessType::activeOrdered()->mapWithKeys(function (ProcessType $type) {
            return [
                $type->slug => [
                    'label' => $type->name,
                    'description' => $type->description,
                    'default_title' => $type->default_title,
                    'cta_label' => $type->cta_label,
                ],
            ];
        });

        self::$typeDefinitionsCache = $definitions->toArray();

        return self::$typeDefinitionsCache;
    }

    /**
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT => 'Novo',
            self::STATUS_IN_PROGRESS => 'Em revisao',
            self::STATUS_COMPLETED => 'Aprovado',
            self::STATUS_PENDING_UPDATES => 'Aguardando novas informacoes',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        $definitions = self::typeDefinitions();

        if (isset($definitions[$this->type]['label'])) {
            return $definitions[$this->type]['label'];
        }

        return Str::title(str_replace('_', ' ', $this->type));
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = self::statusLabels();

        return $labels[$this->status] ?? Str::title(str_replace('_', ' ', $this->status));
    }

    /**
     * @return array<string, string|null>|null
     */
    public function getTypeDefinitionAttribute(): ?array
    {
        $definitions = self::typeDefinitions();

        return $definitions[$this->type] ?? null;
    }

    public static function defaultTitleForType(string $type): string
    {
        $definition = self::typeDefinitions()[$type] ?? null;

        return $definition['default_title'] ?? Str::title(str_replace('_', ' ', $type));
    }

    public static function forInstitutionAndType(?Institution $institution, string $type): ?self
    {
        if (!$institution) {
            return null;
        }

        return $institution->processes()->where('type', $type)->orderByDesc('created_at')->first();
    }

    public static function clearTypeDefinitionsCache(): void
    {
        self::$typeDefinitionsCache = null;
    }
}


