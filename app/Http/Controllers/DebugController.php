<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DebugController extends Controller
{
    public function index(Request $request)
    {
        // ── 1. Environment Info ─────────────────────────────────────────────
        $envInfo = [
            'APP_NAME'      => config('app.name'),
            'APP_ENV'       => config('app.env'),
            'APP_DEBUG'     => config('app.debug') ? 'true' : 'false',
            'APP_URL'       => config('app.url'),
            'PHP Version'   => PHP_VERSION,
            'Laravel'       => app()->version(),
            'Timezone'      => config('app.timezone'),
            'Locale'        => config('app.locale'),
        ];

        // ── 2. Database Connectivity & Stats ────────────────────────────────
        $dbStatus  = 'connected';
        $dbError   = null;
        $dbStats   = [];

        try {
            DB::connection()->getPdo();

            $tables = [
                'users'          => 'users',
                'bku_transaksis' => 'bku_transaksis',
                'anggarans'      => 'anggarans',
                'pejabats'       => 'pejabats',
                'sessions'       => 'sessions',
            ];

            foreach ($tables as $label => $table) {
                if (Schema::hasTable($table)) {
                    $dbStats[$label] = DB::table($table)->count();
                } else {
                    $dbStats[$label] = 'tabel tidak ada';
                }
            }

            // Extra: Validated vs Unvalidated
            if (Schema::hasTable('bku_transaksis')) {
                $dbStats['bku_tervalidasi']   = DB::table('bku_transaksis')->where('status_validasi', true)->count();
                $dbStats['bku_belum_validasi'] = DB::table('bku_transaksis')->where('status_validasi', false)->count();
                $dbStats['total_nominal_bku'] = DB::table('bku_transaksis')->sum('nominal');
            }

        } catch (\Exception $e) {
            $dbStatus = 'error';
            $dbError  = $e->getMessage();
        }

        // ── 3. PHP Extensions ───────────────────────────────────────────────
        $extensions = [
            'ZipArchive'  => class_exists('ZipArchive'),
            'PDO'         => extension_loaded('pdo'),
            'pdo_mysql'   => extension_loaded('pdo_mysql'),
            'mbstring'    => extension_loaded('mbstring'),
            'openssl'     => extension_loaded('openssl'),
            'tokenizer'   => extension_loaded('tokenizer'),
            'xml'         => extension_loaded('xml'),
            'ctype'       => extension_loaded('ctype'),
            'json'        => extension_loaded('json'),
            'bcmath'      => extension_loaded('bcmath'),
            'fileinfo'    => extension_loaded('fileinfo'),
            'gd'          => extension_loaded('gd'),
            'curl'        => extension_loaded('curl'),
            'intl'        => extension_loaded('intl'),
        ];

        // ── 4. Recent Laravel Logs ──────────────────────────────────────────
        $logLines = [];
        $logPath  = storage_path('logs/laravel.log');
        if (file_exists($logPath)) {
            $lines     = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $logLines  = array_slice(array_reverse($lines), 0, 80);
        }

        // ── 5. Session Info ─────────────────────────────────────────────────
        $sessionInfo = [
            'tahun_anggaran' => session('tahun_anggaran', '(tidak diset)'),
            'user_id'        => auth()->id(),
            'user_role'      => auth()->user()?->role ?? '-',
            'user_email'     => auth()->user()?->email ?? '-',
            'session_driver' => config('session.driver'),
        ];

        // ── 6. Route List (subset) ──────────────────────────────────────────
        $routes = collect(\Illuminate\Support\Facades\Route::getRoutes())
            ->filter(fn($r) => !str_starts_with($r->uri(), '_') && !str_starts_with($r->uri(), 'ignition'))
            ->map(fn($r) => [
                'method' => implode('|', $r->methods()),
                'uri'    => $r->uri(),
                'name'   => $r->getName() ?? '-',
                'action' => class_basename($r->getActionName()),
            ])
            ->values()
            ->toArray();

        // ── 7. System Health Checks ─────────────────────────────────────────
        $healthChecks = [
            ['label' => 'Database Connection',    'ok' => $dbStatus === 'connected', 'detail' => $dbStatus === 'connected' ? 'OK' : $dbError],
            ['label' => 'ZipArchive Extension',   'ok' => class_exists('ZipArchive'),   'detail' => class_exists('ZipArchive') ? 'Enabled' : 'MISSING'],
            ['label' => 'Storage Directory',      'ok' => is_writable(storage_path()),  'detail' => is_writable(storage_path()) ? 'Writable' : 'NOT Writable'],
            ['label' => 'Bootstrap Cache',        'ok' => is_writable(base_path('bootstrap/cache')), 'detail' => is_writable(base_path('bootstrap/cache')) ? 'Writable' : 'NOT Writable'],
            ['label' => 'Log File',               'ok' => file_exists($logPath),        'detail' => file_exists($logPath) ? 'Exists (' . round(filesize($logPath) / 1024, 1) . ' KB)' : 'Not found'],
            ['label' => 'APP_KEY Set',            'ok' => !empty(config('app.key')),    'detail' => !empty(config('app.key')) ? 'Set' : 'MISSING'],
            ['label' => 'Debug Mode (should be off in prod)', 'ok' => !config('app.debug'), 'detail' => config('app.debug') ? 'ON (unsafe in production)' : 'OFF'],
            ['label' => 'PDO MySQL Extension',    'ok' => extension_loaded('pdo_mysql'), 'detail' => extension_loaded('pdo_mysql') ? 'Enabled' : 'MISSING'],
        ];

        return view('debug.index', compact(
            'envInfo', 'dbStatus', 'dbError', 'dbStats',
            'extensions', 'logLines', 'sessionInfo', 'routes', 'healthChecks'
        ));
    }
}
