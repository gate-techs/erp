<?php

namespace Modules\AuditLog\Services;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Modules\AuditLog\Models\AuditLogSetting;
use PDO;

class AuditConnectionService
{
    const CONNECTION_NAME = 'audit_external';

    /**
     * Boot and apply dynamic connection configuration into Laravel runtime.
     */
    public static function applyDynamicConnection(): void
    {
        try {
            if (!Schema::hasTable('audit_log_settings')) {
                return;
            }

            $settings = AuditLogSetting::first();

            if ($settings && $settings->is_external_db_enabled && !empty($settings->host) && !empty($settings->database)) {
                $password = $settings->getDecryptedPassword() ?? '';

                Config::set('database.connections.' . self::CONNECTION_NAME, [
                    'driver'         => $settings->driver ?: 'mysql',
                    'host'           => $settings->host,
                    'port'           => $settings->port ?: '3306',
                    'database'       => $settings->database,
                    'username'       => $settings->username ?: '',
                    'password'       => $password,
                    'charset'        => 'utf8mb4',
                    'collation'      => 'utf8mb4_unicode_ci',
                    'prefix'         => '',
                    'strict'         => true,
                    'engine'         => null,
                    'options'        => [
                        PDO::ATTR_TIMEOUT => 3, // 3 seconds timeout to prevent hanging
                    ],
                ]);

                // Ensure the activity_log table exists on the external database
                if (!Schema::connection(self::CONNECTION_NAME)->hasTable('activity_log')) {
                    Schema::connection(self::CONNECTION_NAME)->create('activity_log', function (Blueprint $table) {
                        $table->bigIncrements('id');
                        $table->string('log_name')->nullable()->index();
                        $table->text('description');
                        $table->nullableMorphs('subject', 'subject');
                        $table->string('event')->nullable()->index();
                        $table->nullableMorphs('causer', 'causer');
                        $table->json('properties')->nullable();
                        $table->uuid('batch_uuid')->nullable()->index();
                        $table->timestamps();
                        $table->index('created_at');
                    });
                }

                Config::set('activitylog.database_connection', self::CONNECTION_NAME);
            }
        } catch (Exception $e) {
            // In case of DB connectivity boot failure, smoothly fallback to default if enabled
            Config::set('activitylog.database_connection', null);
        }
    }

    /**
     * Test database connectivity with given parameters.
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function testConnection(array $params): array
    {
        $driver   = $params['driver'] ?? 'mysql';
        $host     = $params['host'] ?? '127.0.0.1';
        $port     = $params['port'] ?? '3306';
        $database = $params['database'] ?? '';
        $username = $params['username'] ?? '';
        $password = $params['password'] ?? '';

        if (empty($database)) {
            return [
                'success' => false,
                'message' => __('اسم قاعدة البيانات مطلوب لاختبار الاتصال.'),
            ];
        }

        try {
            $dsn = "{$driver}:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT            => 4,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            $pdo = new PDO($dsn, $username, $password, $options);

            // Check if activity_log table exists
            $stmt = $pdo->query("SHOW TABLES LIKE 'activity_log'");
            $tableExists = (bool) $stmt->fetch();

            return [
                'success'      => true,
                'table_exists' => $tableExists,
                'message'      => $tableExists 
                    ? __('تم الاتصال بنجاح بقاعدة البيانات الخارجية، وجدول السجلات (activity_log) موجود وجاهز.')
                    : __('تم الاتصال بنجاح بقاعدة البيانات الخارجية، ولكن جدول (activity_log) غير موجود بعد. يمكنك الضغط على زر "مزامنة وإنشاء الجدول".'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => __('فشل الاتصال بقاعدة البيانات الخارجية: ') . $e->getMessage(),
            ];
        }
    }

    /**
     * Initialize / create activity_log table on external database.
     *
     * @param AuditLogSetting $settings
     * @return array<string, mixed>
     */
    public function syncExternalSchema(AuditLogSetting $settings): array
    {
        try {
            // Configure temporary connection
            $password = $settings->getDecryptedPassword() ?? '';

            Config::set('database.connections.' . self::CONNECTION_NAME, [
                'driver'    => $settings->driver ?: 'mysql',
                'host'      => $settings->host,
                'port'      => $settings->port ?: '3306',
                'database'  => $settings->database,
                'username'  => $settings->username ?: '',
                'password'  => $password,
                'charset'   => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix'    => '',
                'strict'    => true,
            ]);

            DB::purge(self::CONNECTION_NAME);

            if (!Schema::connection(self::CONNECTION_NAME)->hasTable('activity_log')) {
                Schema::connection(self::CONNECTION_NAME)->create('activity_log', function (Blueprint $table) {
                    $table->bigIncrements('id');
                    $table->string('log_name')->nullable()->index();
                    $table->text('description');
                    $table->nullableMorphs('subject', 'subject');
                    $table->string('event')->nullable()->index();
                    $table->nullableMorphs('causer', 'causer');
                    $table->json('properties')->nullable();
                    $table->uuid('batch_uuid')->nullable()->index();
                    $table->timestamps();
                    $table->index('created_at');
                });

                return [
                    'success' => true,
                    'message' => __('تم إنشاء جدول (activity_log) بنجاح على قاعدة البيانات الخارجية!'),
                ];
            }

            return [
                'success' => true,
                'message' => __('جدول (activity_log) موجود بالفعل على قاعدة البيانات الخارجية.'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => __('فشل إنشاء الجدول على السيرفر الخارجي: ') . $e->getMessage(),
            ];
        }
    }
}
