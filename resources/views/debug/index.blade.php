@extends('layouts.app')

@section('title', '🛠️ Debug Panel')

@push('styles')
<style>
    :root {
        --debug-bg: #0d1117;
        --debug-card: #161b22;
        --debug-border: #30363d;
        --debug-accent: #58a6ff;
        --debug-green: #3fb950;
        --debug-red: #f85149;
        --debug-yellow: #d29922;
        --debug-muted: #8b949e;
        --debug-text: #c9d1d9;
    }

    .debug-wrapper {
        background: var(--debug-bg);
        min-height: 100vh;
        color: var(--debug-text);
        font-family: 'JetBrains Mono', 'Fira Code', monospace;
        padding: 1.5rem;
    }

    /* Header */
    .debug-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.75rem;
    }

    .debug-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #fff;
        letter-spacing: 0.02em;
    }

    .debug-header .badge-env {
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
        background: {{ config('app.env') === 'local' ? 'rgba(58,202,107,0.15)' : 'rgba(248,81,73,0.15)' }};
        color: {{ config('app.env') === 'local' ? '#3fb950' : '#f85149' }};
        border: 1px solid {{ config('app.env') === 'local' ? 'rgba(58,202,107,0.3)' : 'rgba(248,81,73,0.3)' }};
        text-transform: uppercase;
    }

    /* Grid */
    .debug-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 1rem;
    }

    .debug-grid-wide {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        margin-top: 1rem;
    }

    /* Card */
    .debug-card {
        background: var(--debug-card);
        border: 1px solid var(--debug-border);
        border-radius: 10px;
        overflow: hidden;
        animation: fadeUp 0.4s ease both;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .debug-card:nth-child(1)  { animation-delay: 0.05s; }
    .debug-card:nth-child(2)  { animation-delay: 0.10s; }
    .debug-card:nth-child(3)  { animation-delay: 0.15s; }
    .debug-card:nth-child(4)  { animation-delay: 0.20s; }
    .debug-card:nth-child(5)  { animation-delay: 0.25s; }
    .debug-card:nth-child(6)  { animation-delay: 0.30s; }

    .card-header {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.85rem 1rem;
        border-bottom: 1px solid var(--debug-border);
        background: rgba(22,27,34,0.8);
    }

    .card-header .icon {
        font-size: 1rem;
    }

    .card-header .title {
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--debug-muted);
    }

    .card-body {
        padding: 0.9rem 1rem;
    }

    /* Key-Value rows */
    .kv-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.4rem 0;
        border-bottom: 1px solid rgba(48,54,61,0.5);
        font-size: 0.78rem;
        gap: 0.5rem;
    }

    .kv-row:last-child { border-bottom: none; }

    .kv-key {
        color: var(--debug-muted);
        flex-shrink: 0;
        min-width: 140px;
    }

    .kv-val {
        color: var(--debug-text);
        text-align: right;
        word-break: break-all;
    }

    /* Badge */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 0.2rem 0.55rem;
        border-radius: 4px;
    }

    .badge-ok {
        background: rgba(63,185,80,0.15);
        color: var(--debug-green);
        border: 1px solid rgba(63,185,80,0.25);
    }

    .badge-err {
        background: rgba(248,81,73,0.15);
        color: var(--debug-red);
        border: 1px solid rgba(248,81,73,0.25);
    }

    .badge-warn {
        background: rgba(210,153,34,0.15);
        color: var(--debug-yellow);
        border: 1px solid rgba(210,153,34,0.25);
    }

    /* Health checks */
    .health-row {
        display: grid;
        grid-template-columns: 1fr auto auto;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(48,54,61,0.5);
        font-size: 0.78rem;
    }

    .health-row:last-child { border-bottom: none; }
    .health-label { color: var(--debug-text); }
    .health-detail { color: var(--debug-muted); font-size: 0.7rem; text-align: right; }

    /* Number highlight */
    .num {
        font-variant-numeric: tabular-nums;
        color: var(--debug-accent);
        font-weight: 600;
    }

    /* Log viewer */
    .log-viewer {
        background: #010409;
        border-radius: 6px;
        padding: 0.75rem 1rem;
        max-height: 380px;
        overflow-y: auto;
        font-size: 0.71rem;
        line-height: 1.65;
        scrollbar-width: thin;
        scrollbar-color: #30363d transparent;
    }

    .log-viewer::-webkit-scrollbar { width: 4px; }
    .log-viewer::-webkit-scrollbar-track { background: transparent; }
    .log-viewer::-webkit-scrollbar-thumb { background: #30363d; border-radius: 4px; }

    .log-line { display: block; padding: 1px 0; border-bottom: 1px solid rgba(48,54,61,0.3); }
    .log-line:last-child { border-bottom: none; }
    .log-error   { color: #f85149; }
    .log-warning { color: #d29922; }
    .log-info    { color: #58a6ff; }
    .log-debug   { color: #8b949e; }
    .log-other   { color: #6e7681; }

    /* Route table */
    .route-table { width: 100%; border-collapse: collapse; font-size: 0.72rem; }
    .route-table th {
        text-align: left;
        color: var(--debug-muted);
        padding: 0.4rem 0.6rem;
        border-bottom: 1px solid var(--debug-border);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-size: 0.68rem;
    }
    .route-table td {
        padding: 0.4rem 0.6rem;
        border-bottom: 1px solid rgba(48,54,61,0.4);
        color: var(--debug-text);
        word-break: break-all;
    }
    .route-table tr:last-child td { border-bottom: none; }
    .route-table tr:hover td { background: rgba(88,166,255,0.04); }

    .method-get    { color: #3fb950; }
    .method-post   { color: #58a6ff; }
    .method-put    { color: #d29922; }
    .method-patch  { color: #bc8cff; }
    .method-delete { color: #f85149; }

    /* Stat big number */
    .stat-big {
        font-size: 1.6rem;
        font-weight: 700;
        color: #fff;
        letter-spacing: -0.02em;
    }

    .stat-label {
        font-size: 0.7rem;
        color: var(--debug-muted);
        text-transform: uppercase;
        letter-spacing: 0.07em;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .stat-item {
        background: rgba(88,166,255,0.05);
        border: 1px solid rgba(88,166,255,0.12);
        border-radius: 8px;
        padding: 0.75rem;
        text-align: center;
    }

    /* Scrollable wrapper for routes */
    .overflow-scroll-y {
        max-height: 360px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #30363d transparent;
    }
    .overflow-scroll-y::-webkit-scrollbar { width: 4px; }
    .overflow-scroll-y::-webkit-scrollbar-thumb { background: #30363d; border-radius: 4px; }

    /* Progress bar */
    .pbar-wrap { background: rgba(48,54,61,0.6); border-radius: 999px; height: 6px; overflow: hidden; }
    .pbar-fill  { height: 100%; border-radius: 999px; transition: width 0.6s ease; }

    /* Timestamp */
    .refresh-time {
        font-size: 0.68rem;
        color: var(--debug-muted);
        text-align: right;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="debug-wrapper">
    {{-- Header --}}
    <div class="debug-header">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#58a6ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
        </svg>
        <h1>Debug Panel — SI-AKIK</h1>
        <span class="badge-env">{{ config('app.env') }}</span>
    </div>

    <p class="refresh-time">
        Dimuat pada: <strong style="color:#c9d1d9">{{ now()->format('d M Y, H:i:s') }}</strong>
        &nbsp;·&nbsp;
        <a href="{{ request()->fullUrl() }}" style="color:#58a6ff">↻ Refresh</a>
    </p>

    {{-- ── Health Checks ───────────────────────────────────────────────── --}}
    <div class="debug-card" style="margin-bottom:1rem;">
        <div class="card-header">
            <span class="icon">🏥</span>
            <span class="title">System Health Checks</span>
            @php
                $failCount = collect($healthChecks)->where('ok', false)->count();
            @endphp
            @if($failCount === 0)
                <span class="badge badge-ok" style="margin-left:auto">✅ All Clear</span>
            @else
                <span class="badge badge-err" style="margin-left:auto">⚠️ {{ $failCount }} Issue(s)</span>
            @endif
        </div>
        <div class="card-body">
            @foreach($healthChecks as $check)
                <div class="health-row">
                    <span class="health-label">{{ $check['label'] }}</span>
                    <span class="health-detail">{{ $check['detail'] }}</span>
                    @if($check['ok'])
                        <span class="badge badge-ok">✓ OK</span>
                    @else
                        <span class="badge badge-err">✗ FAIL</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Grid Row 1 ──────────────────────────────────────────────────── --}}
    <div class="debug-grid">

        {{-- Environment --}}
        <div class="debug-card">
            <div class="card-header"><span class="icon">⚙️</span><span class="title">Environment</span></div>
            <div class="card-body">
                @foreach($envInfo as $key => $value)
                    <div class="kv-row">
                        <span class="kv-key">{{ $key }}</span>
                        <span class="kv-val" style="{{ $key === 'APP_DEBUG' && $value === 'true' ? 'color:#f85149' : '' }}">{{ $value }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Session --}}
        <div class="debug-card">
            <div class="card-header"><span class="icon">🔑</span><span class="title">Session & User</span></div>
            <div class="card-body">
                @foreach($sessionInfo as $key => $value)
                    <div class="kv-row">
                        <span class="kv-key">{{ $key }}</span>
                        <span class="kv-val">{{ $value }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- PHP Extensions --}}
        <div class="debug-card">
            <div class="card-header"><span class="icon">🧩</span><span class="title">PHP Extensions</span></div>
            <div class="card-body">
                @foreach($extensions as $ext => $enabled)
                    <div class="kv-row">
                        <span class="kv-key">{{ $ext }}</span>
                        @if($enabled)
                            <span class="badge badge-ok">✓ enabled</span>
                        @else
                            <span class="badge badge-err">✗ missing</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Database Stats --}}
        <div class="debug-card">
            <div class="card-header">
                <span class="icon">🗃️</span>
                <span class="title">Database Stats</span>
                @if($dbStatus === 'connected')
                    <span class="badge badge-ok" style="margin-left:auto">Connected</span>
                @else
                    <span class="badge badge-err" style="margin-left:auto">Error</span>
                @endif
            </div>
            <div class="card-body">
                @if($dbStatus === 'error')
                    <p style="color:#f85149;font-size:0.78rem;">{{ $dbError }}</p>
                @else
                    {{-- Big stats --}}
                    @if(isset($dbStats['bku_transaksis']))
                        <div class="stat-grid" style="grid-template-columns: repeat(2,1fr)">
                            <div class="stat-item">
                                <div class="stat-big">{{ $dbStats['bku_transaksis'] }}</div>
                                <div class="stat-label">Total BKU</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-big" style="color:#3fb950">{{ $dbStats['bku_tervalidasi'] ?? 0 }}</div>
                                <div class="stat-label">Tervalidasi</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-big" style="color:#f85149">{{ $dbStats['bku_belum_validasi'] ?? 0 }}</div>
                                <div class="stat-label">Belum Validasi</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-big" style="font-size:1rem">Rp {{ number_format($dbStats['total_nominal_bku'] ?? 0, 0, ',', '.') }}</div>
                                <div class="stat-label">Total Nominal</div>
                            </div>
                        </div>
                    @endif

                    @php $skipKeys = ['bku_tervalidasi','bku_belum_validasi','total_nominal_bku']; @endphp
                    @foreach($dbStats as $tbl => $count)
                        @if(!in_array($tbl, $skipKeys))
                            <div class="kv-row">
                                <span class="kv-key">{{ $tbl }}</span>
                                <span class="kv-val num">
                                    @if(is_numeric($count))
                                        {{ number_format($count) }} rows
                                    @else
                                        {{ $count }}
                                    @endif
                                </span>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>

    </div>{{-- end grid --}}

    {{-- ── Row 2: Logs + Routes ─────────────────────────────────────────── --}}
    <div class="debug-grid" style="margin-top:1rem; grid-template-columns: 1fr 1fr;">

        {{-- Recent Logs --}}
        <div class="debug-card">
            <div class="card-header">
                <span class="icon">📋</span>
                <span class="title">Recent Laravel Logs</span>
                <span style="margin-left:auto;font-size:0.7rem;color:var(--debug-muted)">80 baris terakhir</span>
            </div>
            <div class="card-body" style="padding: 0.5rem 0.75rem">
                @if(empty($logLines))
                    <p style="color:var(--debug-muted);font-size:0.78rem;padding:0.5rem">Log kosong atau tidak ditemukan.</p>
                @else
                    <div class="log-viewer">
                        @foreach($logLines as $line)
                            @php
                                $trimmed = htmlspecialchars(mb_strimwidth($line, 0, 240, '...'));
                                if (str_contains($line, '.ERROR') || str_contains($line, 'CRITICAL')) {
                                    $cls = 'log-error';
                                } elseif (str_contains($line, '.WARNING')) {
                                    $cls = 'log-warning';
                                } elseif (str_contains($line, '.INFO')) {
                                    $cls = 'log-info';
                                } elseif (str_contains($line, '.DEBUG')) {
                                    $cls = 'log-debug';
                                } else {
                                    $cls = 'log-other';
                                }
                            @endphp
                            <span class="log-line {{ $cls }}">{!! $trimmed !!}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Routes --}}
        <div class="debug-card">
            <div class="card-header">
                <span class="icon">🗺️</span>
                <span class="title">Registered Routes</span>
                <span style="margin-left:auto;font-size:0.7rem;color:var(--debug-muted)">{{ count($routes) }} routes</span>
            </div>
            <div class="card-body" style="padding:0">
                <div class="overflow-scroll-y">
                    <table class="route-table">
                        <thead>
                            <tr>
                                <th>Method</th>
                                <th>URI</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($routes as $route)
                                @php
                                    $m = strtolower(explode('|', $route['method'])[0]);
                                    $cls = match($m) {
                                        'get'    => 'method-get',
                                        'post'   => 'method-post',
                                        'put'    => 'method-put',
                                        'patch'  => 'method-patch',
                                        'delete' => 'method-delete',
                                        default  => ''
                                    };
                                @endphp
                                <tr>
                                    <td class="{{ $cls }}">{{ $route['method'] }}</td>
                                    <td>{{ $route['uri'] }}</td>
                                    <td style="color:var(--debug-muted)">{{ $route['name'] }}</td>
                                    <td style="color:var(--debug-muted)">{{ $route['action'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- Footer --}}
    <p style="text-align:center;font-size:0.68rem;color:var(--debug-muted);margin-top:1.5rem;padding-top:1rem;border-top:1px solid var(--debug-border)">
        SI-AKIK Debug Panel &nbsp;·&nbsp; PHP {{ PHP_VERSION }} &nbsp;·&nbsp; Laravel {{ app()->version() }}
        &nbsp;·&nbsp; <span style="color:#f85149">Halaman ini hanya untuk development. Nonaktifkan di production!</span>
    </p>
</div>
@endsection
