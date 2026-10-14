<?php

namespace Modules\EmployeePortal\Services;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\EmployeePortal\Repositories\Contracts\PortalEmployeeRepositoryInterface;
use Modules\EmployeePortal\Services\Contracts\PortalDocumentServiceInterface;
use Modules\HR\Enums\HrSettingStatus;
use Modules\HR\Enums\HrSettingType;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeDocument;
use Modules\HR\Models\HrSetting;

class PortalDocumentService implements PortalDocumentServiceInterface
{
    public function __construct(
        protected PortalEmployeeRepositoryInterface $employeeRepo
    ) {}

    /**
     * Resolve active employee for the user.
     */
    public function resolveCurrentEmployee(?User $user): ?Employee
    {
        if ($user) {
            $employee = $this->employeeRepo->findByUserId($user->id);
            if ($employee) {
                return $employee;
            }
        }

        return $this->employeeRepo->getActiveFallback();
    }

    /**
     * Get data required for employee official documents screen.
     */
    public function getDocumentsContext(?User $user): array
    {
        $employee = $this->resolveCurrentEmployee($user);

        if (!$employee) {
            return [
                'employee'             => null,
                'user'                 => $user,
                'documents'            => collect(),
                'approvedCount'        => 0,
                'actionRequiredCount'  => 0,
                'compliancePercentage' => 0,
                'hasWarning'           => false,
                'warningBanner'        => null,
                'documentTypes'        => collect(),
            ];
        }

        // Available document types from HR Settings dynamically
        $documentTypes = HrSetting::with('translations')
            ->where('type', HrSettingType::DOCUMENT_TYPES->value)
            ->where(function ($q) {
                $q->where('status', HrSettingStatus::ACTIVE->value)
                  ->orWhere('status', 'active')
                  ->orWhere('status', 1)
                  ->orWhereNull('status');
            })
            ->orderBy('id', 'asc')
            ->get();

        // Fetch actual employee documents from database
        $dbDocs = EmployeeDocument::with('documentType')
            ->where('employee_id', $employee->id)
            ->orderByRaw('expiry_date IS NULL, expiry_date ASC')
            ->get();

        $processedDocs = collect();
        $approvedCount = 0;
        $actionRequiredCount = 0;
        $expiringDoc = null;
        $now = Carbon::now();

        foreach ($dbDocs as $doc) {
            $typeName = $doc->documentType?->name ?? __('Official Document');
            $docNumber = $doc->document_number ?: '';

            // Subtitle: Document Number or Notes if number is empty
            $subtitle = !empty($docNumber)
                ? __('Document No.') . ': ' . $docNumber
                : ($doc->notes ?: '');

            // Expiry status evaluation
            $expiryDate = $doc->expiry_date;
            $notifyBefore = $doc->notify_before_days ?: 60;
            $statusClass = 'valid';
            $statusDotClass = 'dot-valid';
            $statusLabel = __('Valid');
            $daysLeft = null;

            if ($expiryDate) {
                $daysLeft = (int) $now->diffInDays($expiryDate, false);
                $year = $expiryDate->format('Y');

                if ($daysLeft < 0) {
                    $statusClass = 'expired';
                    $statusDotClass = 'dot-expired';
                    $statusLabel = __('Expired');
                    $actionRequiredCount++;

                    if (!$expiringDoc) {
                        $expiringDoc = [
                            'title'      => $typeName,
                            'days_left'  => 0,
                            'is_expired' => true,
                        ];
                    }
                } elseif ($daysLeft <= $notifyBefore) {
                    $statusClass = 'expiring';
                    $statusDotClass = 'dot-expiring';
                    $statusLabel = __('Expires Soon');
                    $actionRequiredCount++;

                    if (!$expiringDoc || (!isset($expiringDoc['is_expired']) && $daysLeft < $expiringDoc['days_left'])) {
                        $expiringDoc = [
                            'title'      => $typeName,
                            'days_left'  => max(1, $daysLeft),
                            'is_expired' => false,
                        ];
                    }
                } else {
                    $statusClass = 'valid';
                    $statusDotClass = 'dot-valid';
                    $statusLabel = __('Valid until :year', ['year' => $year]);
                    $approvedCount++;
                }
            } else {
                $statusClass = 'valid';
                $statusDotClass = 'dot-valid';
                $statusLabel = __('Valid');
                $approvedCount++;
            }

            // File URL
            $fileUrl = $doc->file_path ? Storage::url($doc->file_path) : null;
            $isPdf = $doc->file_path && str_ends_with(strtolower($doc->file_path), '.pdf');
            $isImage = $doc->file_path && preg_match('/\.(jpg|jpeg|png|webp)$/i', $doc->file_path);

            $processedDocs->push((object) [
                'id'                 => $doc->id,
                'document_type_id'   => $doc->document_type_id,
                'title'              => $typeName,
                'subtitle'           => $subtitle,
                'number'             => $docNumber,
                'issue_date_raw'     => $doc->issue_date ? $doc->issue_date->format('Y-m-d') : '',
                'expiry_date_raw'    => $expiryDate ? $expiryDate->format('Y-m-d') : '',
                'issue_date'         => $doc->issue_date ? $doc->issue_date->translatedFormat('d F Y') : null,
                'expiry_date'        => $expiryDate ? $expiryDate->translatedFormat('d F Y') : null,
                'status_class'       => $statusClass,
                'status_dot'         => $statusDotClass,
                'status_label'       => $statusLabel,
                'days_left'          => $daysLeft,
                'file_url'           => $fileUrl,
                'file_name'          => $doc->file_name,
                'has_file'           => !empty($doc->file_path),
                'is_pdf'             => (bool) $isPdf,
                'is_image'           => (bool) $isImage,
                'notes'              => $doc->notes,
            ]);
        }

        // Compliance percentage calculation
        $totalDocs = $processedDocs->count();
        $compliancePercentage = $totalDocs > 0
            ? min(100, (int) round(($approvedCount / $totalDocs) * 100))
            : 100;

        // Dynamic Warning Banner
        $hasWarning = false;
        $warningBanner = null;

        if ($expiringDoc) {
            $hasWarning = true;
            if (!empty($expiringDoc['is_expired'])) {
                $warningBanner = [
                    'days'        => 0,
                    'days_badge'  => __('Expired'),
                    'title'       => __('Warning: :doc has expired', ['doc' => $expiringDoc['title']]),
                    'description' => __('Please update and upload a renewed copy of the document as soon as possible.'),
                ];
            } else {
                $warningBanner = [
                    'days'        => $expiringDoc['days_left'],
                    'days_badge'  => __('Remaining :days Days', ['days' => $expiringDoc['days_left']]),
                    'title'       => __('Warning: :doc expires soon', ['doc' => $expiringDoc['title']]),
                    'description' => __('Please update the valid copy of this document to ensure the continuity of your professional accreditation.'),
                ];
            }
        }

        return [
            'employee'              => $employee,
            'user'                  => $user,
            'documents'             => $processedDocs,
            'approvedCount'         => $approvedCount,
            'actionRequiredCount'   => $actionRequiredCount,
            'compliancePercentage'  => $compliancePercentage,
            'hasWarning'            => $hasWarning,
            'warningBanner'         => $warningBanner,
            'documentTypes'         => $documentTypes,
        ];
    }

    /**
     * Get data required for create / update document form screen.
     */
    public function getCreateDocumentContext(?User $user, ?int $documentId = null): array
    {
        $employee = $this->resolveCurrentEmployee($user);

        $documentTypes = HrSetting::with('translations')
            ->where('type', HrSettingType::DOCUMENT_TYPES->value)
            ->where(function ($q) {
                $q->where('status', HrSettingStatus::ACTIVE->value)
                  ->orWhere('status', 'active')
                  ->orWhere('status', 1)
                  ->orWhereNull('status');
            })
            ->orderBy('id', 'asc')
            ->get();

        $document = null;
        if ($documentId && $employee) {
            $document = EmployeeDocument::where('id', $documentId)
                ->where('employee_id', $employee->id)
                ->first();
        }

        return [
            'employee'      => $employee,
            'user'          => $user,
            'documentTypes' => $documentTypes,
            'document'      => $document,
            'isEdit'        => (bool) $document,
        ];
    }

    /**
     * Upload or update an employee document.
     */
    public function uploadDocument(array $data, ?User $user): array
    {
        $employee = $this->resolveCurrentEmployee($user);

        if (!$employee) {
            return [
                'success' => false,
                'message' => __('No employee record associated with this account.'),
            ];
        }

        try {
            return DB::transaction(function () use ($data, $employee) {
                $filePath = null;
                $fileName = null;

                if (!empty($data['file']) && $data['file'] instanceof UploadedFile) {
                    $file = $data['file'];
                    $fileName = $file->getClientOriginalName();
                    $filePath = $file->store('hr/documents/' . $employee->id, 'public');
                }

                $documentTypeId = !empty($data['document_type_id']) ? (int) $data['document_type_id'] : null;
                $documentNumber = !empty($data['document_number']) ? trim($data['document_number']) : null;
                $issueDate      = !empty($data['issue_date']) ? $data['issue_date'] : null;
                $isPermanent    = !empty($data['is_permanent']);
                $expiryDate     = $isPermanent ? null : (!empty($data['expiry_date']) ? $data['expiry_date'] : null);
                
                // Notes can hold issuing authority / document name
                $notes = !empty($data['issuing_authority'])
                    ? trim($data['issuing_authority'])
                    : (!empty($data['notes']) ? trim($data['notes']) : null);

                // Check if updating an existing document by document_id or document_type_id
                $existingDoc = null;
                if (!empty($data['document_id'])) {
                    $existingDoc = EmployeeDocument::where('id', $data['document_id'])
                        ->where('employee_id', $employee->id)
                        ->first();
                } elseif ($documentTypeId) {
                    $existingDoc = EmployeeDocument::where('employee_id', $employee->id)
                        ->where('document_type_id', $documentTypeId)
                        ->first();
                }

                if ($existingDoc) {
                    $updatePayload = [
                        'document_type_id' => $documentTypeId ?: $existingDoc->document_type_id,
                        'document_number'  => $documentNumber ?? $existingDoc->document_number,
                        'issue_date'       => $issueDate ?? $existingDoc->issue_date,
                        'expiry_date'      => $expiryDate ?? $existingDoc->expiry_date,
                        'notes'            => $notes ?? $existingDoc->notes,
                    ];

                    if ($filePath) {
                        if ($existingDoc->file_path && Storage::disk('public')->exists($existingDoc->file_path)) {
                            Storage::disk('public')->delete($existingDoc->file_path);
                        }
                        $updatePayload['file_path'] = $filePath;
                        $updatePayload['file_name'] = $fileName;
                    }

                    $existingDoc->update($updatePayload);
                    $doc = $existingDoc;
                } else {
                    $doc = EmployeeDocument::create([
                        'employee_id'        => $employee->id,
                        'document_type_id'   => $documentTypeId,
                        'document_number'    => $documentNumber,
                        'file_path'          => $filePath,
                        'file_name'          => $fileName,
                        'issue_date'         => $issueDate,
                        'expiry_date'        => $expiryDate,
                        'notify_before_days' => 30,
                        'notes'              => $notes,
                    ]);
                }

                return [
                    'success' => true,
                    'message' => __('Document saved successfully.'),
                    'data'    => $doc,
                ];
            });
        } catch (Exception $e) {
            Log::error('PortalDocumentService upload error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
