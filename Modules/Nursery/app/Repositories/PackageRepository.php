<?php

namespace Modules\Nursery\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Nursery\Models\Package;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Repositories\Contracts\PackageRepositoryInterface;

class PackageRepository implements PackageRepositoryInterface
{
    public function getFilteredQuery(array $filters = []): Builder
    {
        $query = Package::with(['translations', 'nursery.translations', 'features.translations', 'images'])
            ->orderBy('sort_order')
            ->latest('id');

        foreach (['nursery_id', 'status'] as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }

        if (! empty($filters['search'])) {
            $search = is_array($filters['search'])
                ? ($filters['search']['value'] ?? '')
                : $filters['search'];

            if ($search !== '') {
                $query->where(function (Builder $q) use ($search) {
                    $q->whereTranslationLike('name', "%{$search}%")
                        ->orWhereTranslationLike('description', "%{$search}%")
                        ->orWhereHas('nursery', fn (Builder $nursery) =>
                            $nursery->whereTranslationLike('name', "%{$search}%"));
                });
            }
        }

        return $query;
    }

    public function all(): Collection
    {
        return $this->getFilteredQuery()->get();
    }

    public function find(int $id): ?Package
    {
        return Package::with([
            'translations',
            'nursery.translations',
            'features.translations',
            'images',
        ])->find($id);
    }

    public function create(array $data): Package
    {
        return DB::transaction(function () use ($data) {
            $features = $data['features'] ?? [];
            $images = $data['gallery_paths'] ?? [];
            unset($data['features'], $data['gallery_paths']);

            $package = new Package();
            $this->fillPackage($package, $data);
            $package->save();
            $this->syncFeatures($package, $features);
            $this->addImages($package, $images);

            return $this->find($package->id);
        });
    }

    public function update(int $id, array $data): ?Package
    {
        return DB::transaction(function () use ($id, $data) {
            $package = Package::find($id);
            if (! $package) {
                return null;
            }

            $features = $data['features'] ?? null;
            $images = $data['gallery_paths'] ?? null;
            unset($data['features'], $data['gallery_paths'], $data['replace_gallery']);

            $this->fillPackage($package, $data);
            $package->save();

            if ($features !== null) {
                $package->features()->delete();
                $this->syncFeatures($package, $features);
            }

            if (is_array($images)) {
                $package->images()->delete();
                $this->addImages($package, $images);
            }

            return $this->find($package->id);
        });
    }

    public function delete(int $id): bool
    {
        $package = Package::find($id);
        return $package ? (bool) $package->delete() : false;
    }

    public function toggleStatus(int $id): ?Package
    {
        $package = Package::find($id);
        if (! $package) {
            return null;
        }

        $package->status = $package->status === 'active' ? 'inactive' : 'active';
        $package->save();

        return $package;
    }

    public function getNurseries(): Collection
    {
        return Nursery::with('translations')->orderBy('id')->get();
    }

    protected function fillPackage(Package $package, array $data): void
    {
        $package->fill(collect($data)->except(['ar', 'en'])->toArray());

        foreach (['ar', 'en'] as $locale) {
            if (isset($data[$locale]) && is_array($data[$locale])) {
                $translation = $package->translateOrNew($locale);
                $translation->name = $data[$locale]['name'] ?? $translation->name ?? '';
                $translation->description = $data[$locale]['description'] ?? $translation->description;
            }
        }
    }

    protected function syncFeatures(Package $package, array $features): void
    {
        foreach (array_values($features) as $index => $feature) {
            if (empty($feature['name_ar']) && empty($feature['name_en'])) {
                continue;
            }

            $item = $package->features()->create([
                'sort_order' => $feature['sort_order'] ?? $index,
            ]);

            foreach (['ar', 'en'] as $locale) {
                $item->translateOrNew($locale)->name = $feature['name_' . $locale] ?? '';
            }
            $item->save();
        }
    }

    protected function addImages(Package $package, array $images): void
    {
        foreach (array_values($images) as $index => $image) {
            $package->images()->create([
                'image' => $image,
                'sort_order' => $index,
                'is_primary' => $index === 0,
            ]);
        }
    }
}
