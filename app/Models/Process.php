<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Process extends Model
{
    use HasFactory;

    public const TYPE_INSTITUTION_OPENING = 'institution_opening';
    public const TYPE_BRANCH_OPENING = 'branch_opening';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    /** @var array<int, string> */
    protected $fillable = [
        'institution_id',
        'type',
        'title',
        'status',
        'meta',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'meta' => 'array',
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
            self::STATUS_DRAFT => 'Rascunho',
            self::STATUS_IN_PROGRESS => 'Em andamento',
            self::STATUS_COMPLETED => 'Concluido',
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
