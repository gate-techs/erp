<?php

namespace Modules\Nursery\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Modules\Nursery\Models\Package;
use Modules\Nursery\Repositories\Contracts\PackageRepositoryInterface;

class PackageService
{
    public function __construct(
        protected PackageRepositoryInterface $repository
    ) {}

    public function getFilteredQuery(array $filters = [])
    {
        return $this->repository->getFilteredQuery($filters);
    }

    public function find(int $id): ?Package
    {
        return $this->repository->find($id);
    }

    public function getNurseries(): Collection
    {
        return $this->repository->getNurseries();
    }

    public function create(array $data, ?UploadedFile $cover, array $gallery, ?int $userId = null): Package
    {
        $data = $this->prepareData($data, $userId);
        if ($cover) {
            $data['cover_image'] = upload_image($cover, 'packages');
        }
        $data['gallery_paths'] = $this->uploadGallery($gallery);

        return $this->repository->create($data);
    }

    public function update(int $id, array $data, ?UploadedFile $cover, array $gallery): ?Package
    {
        $package = $this->find($id);
        if (! $package) {
            return null;
        }

        $data = $this->prepareData($data);
        if ($cover) {
            if ($package->cover_image) {
                delete_image($package->cover_image);
            }
            $data['cover_image'] = upload_image($cover, 'packages');
        }

        if ($gallery) {
            foreach ($package->images as $image) {
                delete_image($image->image);
            }
            $data['gallery_paths'] = $this->uploadGallery($gallery);
        }

        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        $package = $this->find($id);
        if (! $package) {
            return false;
        }

        if ($package->cover_image) {
            delete_image($package->cover_image);
        }
        foreach ($package->images as $image) {
            delete_image($image->image);
        }

        return $this->repository->delete($id);
    }

    public function toggleStatus(int $id): ?Package
    {
        return $this->repository->toggleStatus($id);
    }

    protected function prepareData(array $data, ?int $userId = null): array
    {
        if ($userId && ! isset($data['created_by'])) {
            $data['created_by'] = $userId;
        }

        // Files are handled separately by the service and must not be mass-assigned.
        unset($data['cover_image'], $data['gallery']);

        $data['ar'] = [
            'name' => $data['name_ar'],
            'description' => $data['description_ar'] ?? null,
        ];
        $data['en'] = [
            'name' => $data['name_en'],
            'description' => $data['description_en'] ?? null,
        ];

        unset($data['name_ar'], $data['name_en'], $data['description_ar'], $data['description_en']);

        return $data;
    }

    protected function uploadGallery(array $gallery): array
    {
        return collect($gallery)
            ->filter(fn ($image) => $image instanceof UploadedFile)
            ->map(fn (UploadedFile $image) => upload_image($image, 'packages/gallery'))
            ->values()
            ->all();
    }
}
