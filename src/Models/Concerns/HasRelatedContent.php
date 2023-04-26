<?php

declare(strict_types=1);

namespace BombenProdukt\Relatable\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use BombenProdukt\Relatable\Models\Relatable;

/**
 * @mixin Model
 *
 * @property Collection $related
 * @property Collection $relatables
 */
trait HasRelatedContent
{
    public function relatables(): MorphMany
    {
        return $this->morphMany(Relatable::class, 'source');
    }

    public function getRelatedAttribute(): Collection
    {
        return $this->relatables
            ->groupBy(fn (Relatable $relatable): string => $this->getActualClassNameForMorph($relatable->related_type))
            ->flatMap(fn (Collection $typeGroup, string $type): Collection => $type::whereIn('id', $typeGroup->pluck('related_id'))->get());
    }

    public function hasRelated(): bool
    {
        return $this->relatables()->count() > 0;
    }

    public function relate(int|Model $item, ?string $type = null): Relatable
    {
        return Relatable::firstOrCreate($this->getRelatableValues($item, $type));
    }

    public function unrelate(int|Model $item, ?string $type = null): int
    {
        return Relatable::where($this->getRelatableValues($item, $type))->delete();
    }

    public function syncRelated(array|Collection $items, bool $detaching = true): void
    {
        $items = $this->getSyncRelatedValues($items);

        $current = $this->relatables->map(fn (Relatable $relatable): array => $relatable->getRelatedValues());

        $items->each(fn (array $values): Relatable => $this->relate($values['id'], $values['type']));

        if (!$detaching) {
            return;
        }

        $current
            ->filter(fn (array $values): bool => !$items->contains($values))
            ->each(fn (array $values): int => $this->unrelate($values['id'], $values['type']));
    }

    protected function getSyncRelatedValues(array|Collection $items): Collection
    {
        if (\is_array($items)) {
            return collect($items);
        }

        return $items->map(fn (Model $item): array => [
            'id' => $item->getKey(),
            'type' => $item->getMorphClass(),
        ]);
    }

    protected function getRelatableValues(int|Model $item, ?string $type = null): array
    {
        $itemIsNumber = \is_int($item);

        if ($itemIsNumber && empty($type)) {
            throw new \InvalidArgumentException(
                'If an id is specified as an item, the type isn\'t allowed to be empty.',
            );
        }

        return [
            'source_id' => $this->getKey(),
            'source_type' => $this->getMorphClass(),
            'related_id' => $itemIsNumber ? $item : $item->getKey(),
            'related_type' => $itemIsNumber ? $type : $item->getMorphClass(),
        ];
    }
}
