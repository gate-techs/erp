<?php

namespace App\Services;

use App\Helpers\MenuHelper;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Throwable;

class GlobalSearchService
{
    /**
     * Cache for parsed sidebar screens.
     */
    protected static array $cachedScreens = [];

    /**
     * Normalize Arabic string variations (Hamza, Taa Marbuta, Yaa, Definite article, Plurals).
     *
     * @param string $term
     * @return array
     */
    protected function getSearchVariants(string $term): array
    {
        $term = trim($term);
        $variants = [$term];

        // Replace Hamzas: أ, إ, آ -> ا
        $normalizedAlef = preg_replace('/[أإآ]/u', 'ا', $term);
        $variants[] = $normalizedAlef;

        // Replace Taa Marbuta: ة -> ه and vice versa
        $variants[] = preg_replace('/ة/u', 'ه', $term);
        $variants[] = preg_replace('/ه/u', 'ة', $term);
        $variants[] = preg_replace('/ة/u', 'ه', $normalizedAlef);

        // Replace Yaa: ى -> ي and vice versa
        $variants[] = preg_replace('/ى/u', 'ي', $term);
        $variants[] = preg_replace('/ي/u', 'ى', $term);

        // Compound name handling: عبد الله <-> عبدالله
        $variants[] = preg_replace('/عبد\s+الله/u', 'عبدالله', $term);
        $variants[] = preg_replace('/عبدالله/u', 'عبد الله', $term);
        $variants[] = preg_replace('/عبد\s+الله/u', 'عبدالله', $normalizedAlef);
        $variants[] = preg_replace('/عبدالله/u', 'عبد الله', $normalizedAlef);

        // Arabic Definite article 'ال' (Add and Remove)
        $withoutAl = null;
        if (str_starts_with($term, 'ال') && mb_strlen($term) > 3) {
            $withoutAl = mb_substr($term, 2);
            $variants[] = $withoutAl;
            $variants[] = preg_replace('/[أإآ]/u', 'ا', $withoutAl);
        } else {
            $variants[] = 'ال' . $term;
            $variants[] = 'ال' . $normalizedAlef;
        }

        // Common Arabic suffixes: ون / ين (Plural masculine) & ات (Plural feminine)
        $wordsToStem = array_filter([$term, $withoutAl, $normalizedAlef]);
        foreach ($wordsToStem as $w) {
            if (str_ends_with($w, 'ين') && mb_strlen($w) > 4) {
                $base = mb_substr($w, 0, -2);
                $variants[] = $base;
                $variants[] = $base . 'ون';
                $variants[] = 'ال' . $base;
                $variants[] = 'ال' . $base . 'ون';
            } elseif (str_ends_with($w, 'ون') && mb_strlen($w) > 4) {
                $base = mb_substr($w, 0, -2);
                $variants[] = $base;
                $variants[] = $base . 'ين';
                $variants[] = 'ال' . $base;
                $variants[] = 'ال' . $base . 'ين';
            } elseif (str_ends_with($w, 'ات') && mb_strlen($w) > 4) {
                $base = mb_substr($w, 0, -2);
                $variants[] = $base;
                $variants[] = $base . 'ة';
                $variants[] = 'ال' . $base;
                $variants[] = 'ال' . $base . 'ة';
            }
        }

        // Individual words if multiple words provided
        $words = preg_split('/\s+/u', $term, -1, PREG_SPLIT_NO_EMPTY);
        if (count($words) > 1) {
            foreach ($words as $w) {
                if (mb_strlen($w) >= 3) {
                    $variants[] = $w;
                }
            }
        }

        return array_values(array_filter(array_unique($variants)));
    }

    /**
     * Search across the entire ERP system.
     *
     * @param string $term
     * @param int $limitPerCategory
     * @return array
     */
    public function search(string $term, int $limitPerCategory = 5): array
    {
        $term = trim($term);
        if (mb_strlen($term) < 2) {
            return [
                'query' => $term,
                'total' => 0,
                'categories' => [],
            ];
        }

        $categories = [];

        // 1. Navigation & All Sidebar Screens (الشاشات والصفحات من السايد بار)
        $pages = $this->searchNavigation($term, 8);
        if (!empty($pages)) {
            $categories[] = [
                'key' => 'navigation',
                'title' => __('Screens & Navigation'),
                'icon' => 'bx bx-layout',
                'color' => 'primary',
                'count' => count($pages),
                'items' => $pages,
            ];
        }

        // 2. Academic Students
        $students = $this->searchStudents($term, $limitPerCategory);
        if (!empty($students)) {
            $categories[] = [
                'key' => 'students',
                'title' => __('Students'),
                'icon' => 'bx bx-user',
                'color' => 'info',
                'count' => count($students),
                'items' => $students,
            ];
        }

        // 3. Guardians
        $guardians = $this->searchGuardians($term, $limitPerCategory);
        if (!empty($guardians)) {
            $categories[] = [
                'key' => 'guardians',
                'title' => __('Guardians'),
                'icon' => 'bx bx-group',
                'color' => 'success',
                'count' => count($guardians),
                'items' => $guardians,
            ];
        }

        // 4. Employees & Teachers
        $employees = $this->searchEmployees($term, $limitPerCategory);
        if (!empty($employees)) {
            $categories[] = [
                'key' => 'employees',
                'title' => __('Employees & Staff'),
                'icon' => 'bx bx-id-card',
                'color' => 'warning',
                'count' => count($employees),
                'items' => $employees,
            ];
        }

        // 5. Classes & Stages
        $classes = $this->searchClasses($term, $limitPerCategory);
        if (!empty($classes)) {
            $categories[] = [
                'key' => 'classes',
                'title' => __('Classes & Stages'),
                'icon' => 'bx bx-book-open',
                'color' => 'secondary',
                'count' => count($classes),
                'items' => $classes,
            ];
        }

        // 6. Tasks
        $tasks = $this->searchTasks($term, $limitPerCategory);
        if (!empty($tasks)) {
            $categories[] = [
                'key' => 'tasks',
                'title' => __('Tasks'),
                'icon' => 'bx bx-check-square',
                'color' => 'danger',
                'count' => count($tasks),
                'items' => $tasks,
            ];
        }

        // 7. CRM Leads
        $leads = $this->searchLeads($term, $limitPerCategory);
        if (!empty($leads)) {
            $categories[] = [
                'key' => 'leads',
                'title' => __('CRM Leads'),
                'icon' => 'bx bx-user-voice',
                'color' => 'primary',
                'count' => count($leads),
                'items' => $leads,
            ];
        }

        // 8. Incident Reports
        $incidents = $this->searchIncidents($term, $limitPerCategory);
        if (!empty($incidents)) {
            $categories[] = [
                'key' => 'incidents',
                'title' => __('Incident Reports'),
                'icon' => 'bx bx-error-circle',
                'color' => 'danger',
                'count' => count($incidents),
                'items' => $incidents,
            ];
        }

        $total = array_sum(array_column($categories, 'count'));

        return [
            'query' => $term,
            'total' => $total,
            'categories' => $categories,
        ];
    }

    /**
     * Get all visible screens parsed from verticalMenu.json.
     *
     * @return array
     */
    /**
     * Get all visible screens parsed from verticalMenu.json.
     *
     * @return array
     */
    public function getAllSidebarScreens(): array
    {
        $locale = app()->getLocale();
        if (isset(self::$cachedScreens[$locale])) {
            return self::$cachedScreens[$locale];
        }

        $menuPath = base_path('resources/menu/verticalMenu.json');
        if (!file_exists($menuPath)) {
            return [];
        }

        try {
            $menuJson = json_decode(file_get_contents($menuPath));
            if (!$menuJson || !isset($menuJson->menu)) {
                return [];
            }

            $screens = [];
            $this->extractMenuScreens($menuJson->menu, '', $screens);
            self::$cachedScreens[$locale] = $screens;
            return $screens;
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Recursively extract all screens from menu items.
     */
    protected function extractMenuScreens(array $items, string $parentHeader, array &$screens): void
    {
        $user = Auth::user();

        foreach ($items as $item) {
            $currentHeader = $parentHeader;
            if (isset($item->menuHeader)) {
                $currentHeader = __($item->menuHeader);
                continue;
            }

            // Check visibility based on user permissions
            if ($user && !MenuHelper::isItemVisible($item, $user)) {
                continue;
            }

            $nameAr = __($item->name ?? '');
            $nameEn = $item->name ?? '';
            $url = $item->url ?? '';

            // Clean icon class
            $icon = $item->icon ?? 'bx bx-link';
            $icon = trim(str_replace(['menu-icon', 'icon-base'], '', $icon));
            if (empty($icon)) {
                $icon = 'bx bx-link';
            }

            if (!empty($url) && $url !== 'javascript:void(0);' && $url !== '#') {
                $screens[] = [
                    'id' => 'screen-' . md5($url . $nameEn),
                    'title' => $nameAr,
                    'title_en' => $nameEn,
                    'subtitle' => !empty($currentHeader) ? $currentHeader : __('Dashboard'),
                    'url' => url($url),
                    'icon' => $icon,
                    'badge' => __('Screen'),
                    'badge_class' => 'bg-label-primary',
                ];
            }

            if (isset($item->submenu) && is_array($item->submenu)) {
                $subHeader = !empty($currentHeader) ? $currentHeader . ' > ' . $nameAr : $nameAr;
                $this->extractMenuScreens($item->submenu, $subHeader, $screens);
            }
        }
    }

    /**
     * Search system navigation & all sidebar screens dynamically.
     */
    protected function searchNavigation(string $term, int $limit): array
    {
        $variants = $this->getSearchVariants($term);
        $allScreens = $this->getAllSidebarScreens();
        $matched = [];
        $addedUrls = [];

        foreach ($allScreens as $screen) {
            if (isset($addedUrls[$screen['url']])) {
                continue;
            }

            $isMatch = false;
            foreach ($variants as $v) {
                $vLower = mb_strtolower($v);
                if (
                    mb_stripos(mb_strtolower($screen['title']), $vLower) !== false ||
                    mb_stripos(mb_strtolower($screen['title_en']), $vLower) !== false ||
                    mb_stripos(mb_strtolower($screen['subtitle']), $vLower) !== false
                ) {
                    $isMatch = true;
                    break;
                }
            }

            if ($isMatch) {
                $matched[] = $screen;
                $addedUrls[$screen['url']] = true;
                if (count($matched) >= $limit) {
                    break;
                }
            }
        }

        return $matched;
    }

    /**
     * Search Academic Students.
     */
    protected function searchStudents(string $term, int $limit): array
    {
        if (!class_exists(\Modules\Academic\Models\AcademicStudent::class)) {
            return [];
        }

        try {
            $variants = $this->getSearchVariants($term);

            $results = \Modules\Academic\Models\AcademicStudent::query()
                ->with(['stage', 'class'])
                ->where(function ($q) use ($variants) {
                    foreach ($variants as $v) {
                        $q->orWhereTranslationLike('name', "%{$v}%")
                          ->orWhere('student_code', 'like', "%{$v}%")
                          ->orWhere('civil_id', 'like', "%{$v}%");
                    }
                })
                ->limit($limit)
                ->get();

            return $results->map(function ($student) {
                $stageName = $student->stage?->name ?? '';
                $className = $student->class?->name ?? '';
                $codePart = $student->student_code ? __('Code: :code', ['code' => $student->student_code]) : null;
                $subtitleParts = array_filter([$stageName, $className, $codePart]);

                $statusLabel = match($student->status) {
                    'active' => __('Active'),
                    'pending' => __('Pending'),
                    'graduated' => __('Graduated'),
                    'withdrawn' => __('Withdrawn'),
                    default => $student->status,
                };
                $badgeClass = match($student->status) {
                    'active' => 'bg-label-success',
                    'pending' => 'bg-label-warning',
                    'withdrawn' => 'bg-label-danger',
                    default => 'bg-label-secondary',
                };

                return [
                    'id' => $student->id,
                    'title' => $student->name ?? "Student #{$student->id}",
                    'subtitle' => !empty($subtitleParts) ? implode(' • ', $subtitleParts) : __('Registered Student'),
                    'url' => Route::has('academic.students.show') ? route('academic.students.show', $student->id) : '#',
                    'icon' => 'bx bx-user',
                    'badge' => $statusLabel,
                    'badge_class' => $badgeClass,
                ];
            })->toArray();
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Search Academic Guardians.
     */
    protected function searchGuardians(string $term, int $limit): array
    {
        if (!class_exists(\Modules\Academic\Models\AcademicGuardian::class)) {
            return [];
        }

        try {
            $variants = $this->getSearchVariants($term);

            $results = \Modules\Academic\Models\AcademicGuardian::query()
                ->where(function ($q) use ($variants) {
                    foreach ($variants as $v) {
                        $q->orWhereTranslationLike('name', "%{$v}%")
                          ->orWhere('phone', 'like', "%{$v}%")
                          ->orWhere('civil_id', 'like', "%{$v}%")
                          ->orWhere('guardian_code', 'like', "%{$v}%");
                    }
                })
                ->limit($limit)
                ->get();

            return $results->map(function ($guardian) {
                $subtitle = array_filter([
                    $guardian->phone ? __('Phone: :phone', ['phone' => $guardian->phone]) : null,
                    $guardian->civil_id ? __('Civil ID: :id', ['id' => $guardian->civil_id]) : null,
                ]);

                return [
                    'id' => $guardian->id,
                    'title' => $guardian->name ?? "Guardian #{$guardian->id}",
                    'subtitle' => !empty($subtitle) ? implode(' • ', $subtitle) : __('Guardian'),
                    'url' => Route::has('academic.guardians.show') ? route('academic.guardians.show', $guardian->id) : '#',
                    'icon' => 'bx bx-group',
                    'badge' => __('Guardian'),
                    'badge_class' => 'bg-label-success',
                ];
            })->toArray();
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Search HR Employees & Teachers.
     */
    protected function searchEmployees(string $term, int $limit): array
    {
        if (!class_exists(\Modules\HR\Models\Employee::class)) {
            return [];
        }

        try {
            $variants = $this->getSearchVariants($term);

            $results = \Modules\HR\Models\Employee::query()
                ->where(function ($q) use ($variants) {
                    foreach ($variants as $v) {
                        $q->orWhereTranslationLike('name', "%{$v}%")
                          ->orWhere('employee_code', 'like', "%{$v}%")
                          ->orWhere('mobile', 'like', "%{$v}%")
                          ->orWhere('national_id', 'like', "%{$v}%");
                    }
                })
                ->limit($limit)
                ->get();

            return $results->map(function ($emp) {
                $codePart = $emp->employee_code ? __('Code: :code', ['code' => $emp->employee_code]) : null;
                $subtitle = array_filter([
                    $codePart,
                    $emp->mobile ?? null,
                ]);

                return [
                    'id' => $emp->id,
                    'title' => $emp->name ?? "Employee #{$emp->id}",
                    'subtitle' => !empty($subtitle) ? implode(' • ', $subtitle) : __('Staff'),
                    'url' => Route::has('hr.employees.show') ? route('hr.employees.show', $emp->id) : '#',
                    'icon' => 'bx bx-id-card',
                    'badge' => __('Employee'),
                    'badge_class' => 'bg-label-warning',
                ];
            })->toArray();
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Search Classes & Stages.
     */
    protected function searchClasses(string $term, int $limit): array
    {
        if (!class_exists(\Modules\Academic\Models\AcademicClass::class)) {
            return [];
        }

        try {
            $variants = $this->getSearchVariants($term);

            $results = \Modules\Academic\Models\AcademicClass::query()
                ->with('stage')
                ->where(function ($q) use ($variants) {
                    foreach ($variants as $v) {
                        $q->orWhereTranslationLike('name', "%{$v}%");
                    }
                })
                ->limit($limit)
                ->get();

            return $results->map(function ($cls) {
                $stageName = $cls->stage?->name;
                $subtitle = $stageName ? __('Stage: :name', ['name' => $stageName]) : '';
                return [
                    'id' => $cls->id,
                    'title' => $cls->name ?? "Class #{$cls->id}",
                    'subtitle' => $subtitle,
                    'url' => Route::has('academic.classes.show') ? route('academic.classes.show', $cls->id) : '#',
                    'icon' => 'bx bx-book-open',
                    'badge' => __('Class'),
                    'badge_class' => 'bg-label-secondary',
                ];
            })->toArray();
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Search Tasks.
     */
    protected function searchTasks(string $term, int $limit): array
    {
        if (!class_exists(\Modules\Tasks\Models\Task::class)) {
            return [];
        }

        try {
            $variants = $this->getSearchVariants($term);

            $results = \Modules\Tasks\Models\Task::query()
                ->where(function ($q) use ($variants) {
                    foreach ($variants as $v) {
                        $q->orWhereTranslationLike('title', "%{$v}%");
                    }
                })
                ->limit($limit)
                ->get();

            return $results->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title ?? "Task #{$task->id}",
                    'subtitle' => $task->due_date ? __('Due: :date', ['date' => $task->due_date]) : '',
                    'url' => Route::has('tasks.tasks.show') ? route('tasks.tasks.show', $task->id) : '#',
                    'icon' => 'bx bx-check-square',
                    'badge' => __('Task'),
                    'badge_class' => 'bg-label-danger',
                ];
            })->toArray();
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Search CRM Leads.
     */
    protected function searchLeads(string $term, int $limit): array
    {
        if (!class_exists(\Modules\CRM\Models\Lead::class)) {
            return [];
        }

        try {
            $variants = $this->getSearchVariants($term);

            $results = \Modules\CRM\Models\Lead::query()
                ->where(function ($q) use ($variants) {
                    foreach ($variants as $v) {
                        $q->orWhere('name', 'like', "%{$v}%")
                          ->orWhere('phone', 'like', "%{$v}%")
                          ->orWhere('email', 'like', "%{$v}%");
                    }
                })
                ->limit($limit)
                ->get();

            return $results->map(function ($lead) {
                return [
                    'id' => $lead->id,
                    'title' => $lead->name ?? "Lead #{$lead->id}",
                    'subtitle' => $lead->phone ? __('Phone: :phone', ['phone' => $lead->phone]) : __('Lead Request'),
                    'url' => Route::has('crm.leads.index') ? route('crm.leads.index') : '#',
                    'icon' => 'bx bx-user-voice',
                    'badge' => __('CRM Leads'),
                    'badge_class' => 'bg-label-primary',
                ];
            })->toArray();
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Search Incident Reports.
     */
    protected function searchIncidents(string $term, int $limit): array
    {
        if (!class_exists(\Modules\Academic\Models\IncidentReport::class)) {
            return [];
        }

        try {
            $variants = $this->getSearchVariants($term);

            $results = \Modules\Academic\Models\IncidentReport::query()
                ->with('student')
                ->where(function ($q) use ($variants) {
                    foreach ($variants as $v) {
                        $q->orWhere('incident_type', 'like', "%{$v}%")
                          ->orWhere('description', 'like', "%{$v}%");
                    }
                })
                ->limit($limit)
                ->get();

            return $results->map(function ($inc) {
                $studentName = $inc->student?->name ?? '';
                return [
                    'id' => $inc->id,
                    'title' => ($inc->incident_type ?? __('Incident')),
                    'subtitle' => $studentName ? __('Student: :name', ['name' => $studentName]) : '',
                    'url' => Route::has('academic.incident-reports.show') ? route('academic.incident-reports.show', $inc->id) : '#',
                    'icon' => 'bx bx-error-circle',
                    'badge' => __('Incident Reports'),
                    'badge_class' => 'bg-label-danger',
                ];
            })->toArray();
        } catch (Throwable $e) {
            return [];
        }
    }
}
