<?php

namespace Modules\Settings\Repositories;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;
use Modules\Settings\Models\NotificationBatch;
use Modules\Settings\Models\NotificationTranslation;
use Modules\Settings\Repositories\Contracts\NotificationRepositoryInterface;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function getForUser(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        return User::findOrFail($userId)->notifications()->latest()->paginate($perPage);
    }

    public function countBatches(): int
    {
        return NotificationBatch::query()->count();
    }

    public function searchBatches(?string $search, int $start, int $length): array
    {
        $query = NotificationBatch::query()->latest('id');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('audience_type', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereJsonContains('payload->title_ar', $search);
            });
        }

        $filtered = (clone $query)->count();
        $rows = $query->skip($start)->take($length)->get();

        return ['filtered' => $filtered, 'rows' => $rows];
    }

    public function getUsersByAudience(string $audience): Collection
    {
        $query = User::query()->where('is_active', true);

        return match ($audience) {
            'employees' => $query->whereIn('user_type', [UserType::EMPLOYEE->value, UserType::SUPERVISOR->value, UserType::NURSE->value])->get(['id']),
            'teachers' => $query->where('user_type', UserType::TEACHER->value)->get(['id']),
            'guardians' => $query->where('user_type', UserType::GUARDIAN->value)->get(['id']),
            default => $query->get(['id']),
        };
    }

    public function createBatch(array $data): NotificationBatch
    {
        return NotificationBatch::create($data);
    }

    public function createNotification(array $data): DatabaseNotification
    {
        return DatabaseNotification::create($data);
    }

    public function createTranslations(array $translations): bool
    {
        return NotificationTranslation::insert($translations);
    }
}
