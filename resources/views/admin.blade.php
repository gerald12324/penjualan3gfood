<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin | 3GFood</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{background:#f0f2f5;color:#1a1a2e;font:14px 'DM Sans',sans-serif;min-height:100vh}
        a{text-decoration:none;color:inherit}
        input,select,textarea,button{font:inherit}

        /* === ADMIN SHELL === */
        .adm-shell{display:flex;min-height:100vh}

        /* === SIDEBAR === */
        .adm-sidebar{width:240px;background:#1a1a2e;color:#fff;display:flex;flex-direction:column;flex-shrink:0;position:sticky;top:0;height:100vh;overflow-y:auto}
        .adm-logo{padding:26px 24px 20px;border-bottom:1px solid rgba(255,255,255,.08)}
        .adm-logo-name{font:800 20px 'Playfair Display',serif;color:#fff}
        .adm-logo-name em{color:#f97316;font-style:normal}
        .adm-logo-sub{font-size:11px;color:rgba(255,255,255,.4);margin-top:3px}
        .adm-nav{padding:18px 14px;flex:1}
        .adm-nav-label{font-size:10px;font-weight:700;letter-spacing:1.5px;color:rgba(255,255,255,.35);margin:16px 10px 8px;text-transform:uppercase}
        .adm-nav-link{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;color:rgba(255,255,255,.65);font-weight:500;margin-bottom:2px;cursor:pointer;transition:all .2s;font-size:13px}
        .adm-nav-link svg{width:17px;height:17px;stroke-width:1.8;flex-shrink:0}
        .adm-nav-link:hover,.adm-nav-link.active{background:rgba(249,115,22,.18);color:#f97316}
        .adm-nav-link.active{font-weight:700}
        .adm-nav-badge{margin-left:auto;background:#f97316;color:#fff;border-radius:99px;font-size:10px;font-weight:700;padding:2px 7px;min-width:20px;text-align:center}
        .adm-sidebar-footer{padding:16px 14px;border-top:1px solid rgba(255,255,255,.08)}
        .adm-sidebar-footer form button{display:flex;align-items:center;gap:8px;width:100%;padding:10px 12px;border-radius:10px;background:rgba(239,68,68,.12);color:#f87171;border:0;cursor:pointer;font-weight:600;font-size:13px;transition:background .2s}
        .adm-sidebar-footer form button:hover{background:rgba(239,68,68,.25)}

        /* === MAIN CONTENT === */
        .adm-main{flex:1;display:flex;flex-direction:column;min-width:0}
        .adm-topbar{background:#fff;border-bottom:1px solid #e5e7eb;padding:0 28px;height:60px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;box-shadow:0 1px 4px rgba(0,0,0,.06)}
        .adm-topbar-title{font:700 17px 'Playfair Display',serif;color:#1a1a2e}
        .adm-topbar-right{display:flex;align-items:center;gap:12px}
        .adm-store-link{display:flex;align-items:center;gap:6px;padding:7px 14px;background:#fff4ed;color:#f97316;border-radius:8px;font-size:12px;font-weight:700;border:1px solid #fed7aa}
        .adm-store-link svg{width:14px;height:14px}
        .adm-content{padding:24px 28px;flex:1}

        /* === SUCCESS FLASH === */
        .adm-flash{background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;border-radius:10px;padding:13px 18px;margin-bottom:20px;font-weight:600;font-size:13px}

        /* === METRIC CARDS === */
        .adm-metrics{display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:24px}
        .adm-metric{background:#fff;border-radius:14px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.06);border:1px solid #f1f5f9;transition:box-shadow .2s}
        .adm-metric:hover{box-shadow:0 4px 16px rgba(0,0,0,.1)}
        .adm-metric-icon{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:14px}
        .adm-metric-icon svg{width:20px;height:20px;stroke-width:1.8}
        .adm-metric-val{font:700 26px 'Playfair Display',serif;color:#1a1a2e;line-height:1}
        .adm-metric-label{font-size:12px;color:#64748b;margin-top:6px;font-weight:500}
        .ic-orange{background:#fff4ed}.ic-orange svg{stroke:#f97316}
        .ic-blue{background:#eff6ff}.ic-blue svg{stroke:#3b82f6}
        .ic-green{background:#f0fdf4}.ic-green svg{stroke:#22c55e}
        .ic-purple{background:#faf5ff}.ic-purple svg{stroke:#a855f7}
        .ic-red{background:#fef2f2}.ic-red svg{stroke:#ef4444}

        /* === SECTION CARDS === */
        .adm-card{background:#fff;border-radius:14px;padding:24px;box-shadow:0 1px 4px rgba(0,0,0,.06);border:1px solid #f1f5f9;margin-bottom:22px}
        .adm-card-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}
        .adm-card-title{font:700 17px 'Playfair Display',serif;color:#1a1a2e}
        .adm-card-sub{font-size:12px;color:#94a3b8;margin-top:2px}

        /* === STATUS TABS === */
        .adm-tabs{display:flex;gap:6px;margin-bottom:18px;flex-wrap:wrap}
        .adm-tab{padding:8px 16px;border-radius:8px;font-size:12px;font-weight:700;background:#f1f5f9;color:#64748b;border:0;cursor:pointer;transition:all .2s}
        .adm-tab.active,.adm-tab:hover{background:#f97316;color:#fff}
        a.adm-tab{display:inline-block}

        /* === ORDERS TABLE === */
        .adm-table{width:100%;border-collapse:collapse}
        .adm-table th{text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.8px;border-bottom:2px solid #f1f5f9}
        .adm-table td{padding:13px 14px;border-bottom:1px solid #f8fafc;vertical-align:top;font-size:13px}
        .adm-table tr:last-child td{border-bottom:0}
        .adm-table tr:hover td{background:#fafafa}
        .adm-badge{display:inline-block;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:700}
        .badge-wait{background:#fef3c7;color:#92400e}
        .badge-ok{background:#d1fae5;color:#065f46}
        .badge-cash{background:#eff6ff;color:#1d4ed8}
        .badge-bank{background:#fdf4ff;color:#7e22ce}
        .badge-digital{background:#f0fdfa;color:#134e4a}

        /* === ACTION BUTTONS === */
        .btn-primary{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#f97316;color:#fff;border:0;border-radius:8px;font-weight:700;font-size:12px;cursor:pointer;transition:background .2s}
        .btn-primary:hover{background:#ea6c0c}
        .btn-danger{background:#ef4444;color:#fff;border:0;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer}
        .btn-danger:hover{background:#dc2626}
        .btn-ghost{display:inline-flex;align-items:center;gap:4px;padding:6px 10px;background:#f1f5f9;color:#475569;border:0;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;transition:background .2s}
        .btn-ghost:hover{background:#e2e8f0}
        .btn-wa{display:inline-flex;align-items:center;gap:5px;padding:6px 10px;background:#22c55e;color:#fff;border-radius:7px;font-size:11px;font-weight:700;margin-top:4px}
        .btn-receipt{display:inline-flex;align-items:center;gap:5px;padding:6px 10px;background:#fff4ed;color:#f97316;border-radius:7px;font-size:11px;font-weight:700;margin-top:4px;margin-left:4px;border:1px solid #fed7aa}
        .btn-confirm{padding:7px 12px;background:#f97316;color:#fff;border:0;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;margin-top:6px;width:100%}
        .btn-confirm:hover{background:#ea6c0c}
        .btn-confirm-sm{padding:5px 10px;background:#f97316;color:#fff;border:0;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer}
        .btn-paid{padding:5px 10px;background:#22c55e;color:#fff;border:0;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;margin-top:4px}

        /* === FORMS === */
        .adm-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px}
        .adm-form-group{display:flex;flex-direction:column;gap:6px}
        .adm-form-group label{font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px}
        .adm-form-group input,.adm-form-group select,.adm-form-group textarea{padding:9px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;background:#fff;transition:border .2s}
        .adm-form-group input:focus,.adm-form-group select:focus,.adm-form-group textarea:focus{outline:none;border-color:#f97316;box-shadow:0 0 0 3px rgba(249,115,22,.1)}
        .adm-form-group textarea{min-height:80px;resize:vertical}
        .adm-form-actions{display:flex;gap:8px;margin-top:14px}

        /* === ACCORDION CATEGORIES === */
        .adm-cat-details{border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;margin-bottom:12px}
        .adm-cat-summary{padding:14px 18px;background:#fffbf7;display:flex;align-items:center;gap:10px;cursor:pointer;list-style:none;font-weight:700;color:#1a1a2e;font-size:14px;user-select:none}
        .adm-cat-summary::-webkit-details-marker{display:none}
        .adm-cat-summary::marker{display:none}
        .adm-cat-icon{width:32px;height:32px;background:#fff4ed;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px}
        .adm-cat-count{margin-left:auto;background:#f97316;color:#fff;border-radius:99px;font-size:11px;padding:2px 8px;font-weight:700}
        .adm-cat-details[open] .adm-cat-summary{border-bottom:1px solid #e2e8f0;border-radius:0}
        .adm-cat-arrow{transition:transform .2s;margin-left:4px;color:#94a3b8}
        .adm-cat-details[open] .adm-cat-arrow{transform:rotate(90deg)}

        /* === COURIER GRID === */
        .adm-courier-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-top:14px}
        .adm-courier-card{background:#fafafa;border:1px solid #e2e8f0;border-radius:12px;padding:18px}
        .adm-courier-name{font:700 16px 'Playfair Display',serif;margin-bottom:4px}
        .adm-courier-phone{font-size:12px;color:#64748b;margin-bottom:10px}
        .adm-courier-stat{display:inline-flex;align-items:center;gap:5px;background:#fff4ed;color:#f97316;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:700;margin-bottom:12px}
        .adm-courier-actions{display:flex;gap:6px;flex-wrap:wrap}
        .adm-courier-edit-form{display:none;margin-top:12px;padding:14px;background:#fff;border:1px solid #e2e8f0;border-radius:10px}

        /* === PAYOUT TABLE === */
        .adm-payout-table{width:100%;border-collapse:collapse;font-size:13px}
        .adm-payout-table th{text-align:left;padding:8px 12px;font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;border-bottom:2px solid #f1f5f9}
        .adm-payout-table td{padding:10px 12px;border-bottom:1px solid #f8fafc;vertical-align:middle}

        /* === REPORTS === */
        .adm-report-header{display:flex;align-items:flex-end;justify-content:space-between;gap:24px;margin-bottom:22px}
        .adm-report-title{font:700 28px/1.15 'Playfair Display',serif;color:#1a1a2e}
        .adm-report-description{font-size:13px;color:#64748b;margin-top:6px}
        .adm-report-filter{display:flex;align-items:end;gap:12px;flex-wrap:wrap;padding:16px;background:#f8fafc;border:1px solid #e5e7eb;border-radius:10px}
        .adm-report-filter .adm-form-group{min-width:150px}
        .adm-report-filter label{font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px}
        .adm-report-filter input{padding:9px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;background:#fff}
        .adm-report-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-bottom:22px}
        .adm-report-stat{background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:18px;min-height:104px;box-shadow:0 1px 4px rgba(0,0,0,.04)}
        .adm-report-stat-label{font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px}
        .adm-report-stat-value{font:700 23px 'Playfair Display',serif;color:#1a1a2e;margin-top:8px}
        .adm-report-layout{display:grid;grid-template-columns:minmax(0,1.35fr) minmax(280px,1fr);gap:18px;align-items:start}
        .adm-report-layout .adm-card{margin-bottom:0}

        /* === TABS (section switching) === */
        .adm-section{display:none}
        .adm-section.active{display:block}

        @media(max-width:900px){
            .adm-sidebar{width:64px}
            .adm-logo-sub,.adm-nav-label,.adm-nav-link span,.adm-nav-badge,.adm-sidebar-footer form button span{display:none}
            .adm-logo-name{font-size:0}
            .adm-logo{padding:18px 14px}
            .adm-nav-link{justify-content:center;padding:12px 8px}
            .adm-sidebar-footer form button{justify-content:center;padding:10px 8px}
            .adm-metrics{grid-template-columns:repeat(3,1fr)}
            .adm-courier-grid{grid-template-columns:1fr}
        }
        @media(max-width:600px){
            .adm-content{padding:16px 14px}
            .adm-topbar{padding:0 14px}
            .adm-metrics{grid-template-columns:1fr 1fr}
            .adm-form-grid{grid-template-columns:1fr}
            .adm-table{display:block;overflow-x:auto;white-space:nowrap}
            .adm-report-grid{grid-template-columns:1fr 1fr}
            .adm-report-layout{grid-template-columns:1fr}
            .adm-report-filter{align-items:stretch;flex-direction:column}
            .adm-report-filter .btn-primary{justify-content:center}
            .adm-report-header{display:block;margin-bottom:16px}
            .adm-report-title{font-size:24px}
        }
    </style>
</head>
<body>
@php
    $waiting = $orders->whereIn('status', ['waiting','waiting_owner','waiting_payment'])->count();
    $revenue = $orders->where('status','completed')->sum('total');
    $activeTab = $status ?? 'summary';
    $menuByCategory = $managedMenu->groupBy('category');
    $navSection = request()->query('section', 'orders');
@endphp
<div class="adm-shell">
    <!-- SIDEBAR -->
    <aside class="adm-sidebar">
        <div class="adm-logo">
            <div class="adm-logo-name">3G<em>Food</em></div>
            <div class="adm-logo-sub">Panel Owner</div>
        </div>
        <nav class="adm-nav">
            <div class="adm-nav-label">Menu Utama</div>
            <a class="adm-nav-link {{ $navSection === 'orders' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}?section=orders">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                <span>Pesanan</span>
                @if($waiting > 0)<span class="adm-nav-badge">{{ $waiting }}</span>@endif
            </a>
            <a class="adm-nav-link {{ $navSection === 'menu' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}?section=menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                <span>Kelola Menu</span>
            </a>
            <a class="adm-nav-link {{ $navSection === 'couriers' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}?section=couriers">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                <span>Kurir</span>
                @if($couriers->count() > 0)<span class="adm-nav-badge">{{ $couriers->count() }}</span>@endif
            </a>
            <a class="adm-nav-link {{ $navSection === 'payouts' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}?section=payouts">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                <span>Pencairan</span>
            </a>
            <a class="adm-nav-link {{ $navSection === 'reports' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}?section=reports">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 19V5M4 19h16"/><path d="M8 16v-5M12 16V7M16 16v-3"/></svg>
                <span>Laporan</span>
            </a>
            <div class="adm-nav-label" style="margin-top:24px">Lainnya</div>
            <a class="adm-nav-link" href="{{ url('/') }}" target="_blank">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span>Lihat Toko</span>
            </a>
        </nav>
        <div class="adm-sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN -->
    <div class="adm-main">
        <!-- TOP BAR -->
        <header class="adm-topbar">
            <div class="adm-topbar-title">
                @if($navSection === 'orders') Manajemen Pesanan
                @elseif($navSection === 'menu') Kelola Menu
                @elseif($navSection === 'couriers') Data Kurir
                @elseif($navSection === 'payouts') Pencairan Kurir
                @elseif($navSection === 'reports') Laporan Penjualan
                @else Dashboard Admin @endif
            </div>
            <div class="adm-topbar-right">
                <a class="adm-store-link" href="{{ url('/') }}" target="_blank">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                    Lihat Toko
                </a>
            </div>
        </header>

        <div class="adm-content">
            @if(session('success'))
            <div class="adm-flash">{!! session('success') !!}</div>
            @endif

            <!-- DASHBOARD METRIC CARDS -->
            @if($navSection !== 'reports')
            <div class="adm-metrics">
                <div class="adm-metric">
                    <div class="adm-metric-icon ic-orange"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg></div>
                    <div class="adm-metric-val">{{ $orders->count() }}</div>
                    <div class="adm-metric-label">Total Pesanan</div>
                </div>
                <div class="adm-metric">
                    <div class="adm-metric-icon ic-red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
                    <div class="adm-metric-val">{{ $waiting }}</div>
                    <div class="adm-metric-label">Perlu Diproses</div>
                </div>
                <div class="adm-metric">
                    <div class="adm-metric-icon ic-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
                    <div class="adm-metric-val">{{ $orders->where('status','completed')->count() }}</div>
                    <div class="adm-metric-label">Pesanan Selesai</div>
                </div>
                <div class="adm-metric">
                    <div class="adm-metric-icon ic-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg></div>
                    <div class="adm-metric-val">{{ $couriers->count() }}</div>
                    <div class="adm-metric-label">Kurir Aktif</div>
                </div>
                <div class="adm-metric">
                    <div class="adm-metric-icon ic-purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
                    <div class="adm-metric-val" style="font-size:16px">Rp {{ number_format($revenue,0,',','.') }}</div>
                    <div class="adm-metric-label">Pendapatan</div>
                </div>
            </div>
            @endif

            {{-- ===================== SECTION: ORDERS ===================== --}}
            @if($navSection === 'orders')
            <div class="adm-card">
                <div class="adm-card-head">
                    <div>
                        <div class="adm-card-title">Daftar Pesanan</div>
                        <div class="adm-card-sub">{{ $orders->count() }} pesanan ditemukan</div>
                    </div>
                    @if($orders->count() > 0 && !$status)
                    <form method="POST" action="{{ route('admin.orders.clear') }}" onsubmit="return confirm('Hapus semua pesanan? Data pesanan dan log WhatsApp tidak dapat dikembalikan.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-danger">Hapus Semua Pesanan</button>
                    </form>
                    @endif
                </div>
                <div class="adm-tabs">
                    <a class="adm-tab {{ !$status ? 'active' : '' }}" href="{{ route('admin.dashboard') }}?section=orders">Semua</a>
                    <a class="adm-tab {{ in_array($status, ['waiting','waiting_owner','waiting_payment'], true) ? 'active' : '' }}" href="{{ route('admin.dashboard') }}?section=orders&status=waiting">Menunggu @if($waiting > 0)({{ $waiting }})@endif</a>
                    <a class="adm-tab {{ $status === 'accepted' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}?section=orders&status=accepted">Diterima</a>
                    <a class="adm-tab {{ $status === 'cooking' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}?section=orders&status=cooking">Sedang Dimasak</a>
                    <a class="adm-tab {{ $status === 'ready_to_deliver' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}?section=orders&status=ready_to_deliver">Siap Diantar</a>
                    <a class="adm-tab {{ $status === 'delivering' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}?section=orders&status=delivering">Diantar</a>
                    <a class="adm-tab {{ $status === 'completed' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}?section=orders&status=completed">Selesai</a>
                </div>
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Pembeli</th>
                            <th>Pembayaran</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($orders as $order)
                    @php($msg = in_array($order->status, ['completed', 'delivering']) ? 'Halo '.$order->customer_name.', pesanan '.$order->order_code.' sudah siap dan diantar. Untuk pembayaran di tempat, mohon siapkan uang pas sebesar Rp '.number_format($order->total,0,',','.') : 'Halo '.$order->customer_name.', pesanan '.$order->order_code.' sedang diproses.')
                    <tr>
                        <td>
                            <b style="color:#1a1a2e">#{{ $order->order_code }}</b>
                            <div style="font-size:11px;color:#94a3b8;margin-top:2px">Dibuat: {{ $order->created_at->format('d/m/Y H:i') }}</div>
                            @if($order->accepted_at)<div style="font-size:11px;color:#64748b;margin-top:1px">Diterima: {{ $order->accepted_at->format('d/m/Y H:i') }}</div>@endif
                            @if($order->cooking_at)<div style="font-size:11px;color:#64748b;margin-top:1px">Masak: {{ $order->cooking_at->format('d/m/Y H:i') }}</div>@endif
                            @if($order->cooked_at)<div style="font-size:11px;color:#64748b;margin-top:1px">Siap: {{ $order->cooked_at->format('d/m/Y H:i') }}</div>@endif
                            @if($order->delivering_at)<div style="font-size:11px;color:#64748b;margin-top:1px">Diantar: {{ $order->delivering_at->format('d/m/Y H:i') }}</div>@endif
                            @if($order->completed_at)<div style="font-size:11px;color:#64748b;margin-top:1px">Selesai: {{ $order->completed_at->format('d/m/Y H:i') }}</div>@endif
                        </td>
                        <td>
                            <b>{{ $order->customer_name }}</b>
                            <div style="font-size:11px;color:#64748b;margin-top:2px">{{ $order->phone }}</div>
                            <div style="font-size:11px;color:#94a3b8">{{ Str::limit($order->address, 40) }}</div>
                            <a class="btn-wa" target="_blank" href="https://wa.me/{{ preg_replace('/\D+/', '', $order->phone) }}?text={{ urlencode($msg) }}">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                {{ in_array($order->status, ['completed', 'delivering']) ? 'Kabari Antar' : 'WhatsApp' }}
                            </a>
                        </td>
                        <td>
                            <span class="adm-badge {{ $order->payment_method === 'cash' ? 'badge-cash' : ($order->payment_method === 'bank' ? 'badge-bank' : 'badge-digital') }}">
                                {{ ['cash'=>'COD','bank'=>'Bank','digital'=>'Digital'][$order->payment_method] }}
                            </span>
                        </td>
                        <td>
                            <b>Rp {{ number_format($order->total,0,',','.') }}</b>
                            @if($order->payment_method === 'cash')<div style="font-size:10px;color:#f97316;margin-top:2px">Siapkan uang pas</div>@endif
                        </td>
                        <td>
                            @if($order->status === 'waiting' || $order->status === 'waiting_owner' || $order->status === 'waiting_payment')
                                <span class="adm-badge badge-wait">Menunggu</span>
                            @elseif($order->status === 'accepted')
                                <span class="adm-badge" style="background:#e0f2fe;color:#0369a1">Diterima</span>
                            @elseif($order->status === 'cooking')
                                <span class="adm-badge" style="background:#ffedd5;color:#c2410c">Sedang Dimasak</span>
                            @elseif($order->status === 'ready_to_deliver')
                                <span class="adm-badge" style="background:#dbeafe;color:#1d4ed8">Siap Diantar</span>
                            @elseif($order->status === 'delivering')
                                <span class="adm-badge" style="background:#f3e8ff;color:#7e22ce">Diantar</span>
                                @if($order->courier)
                                <div style="font-size:11px;color:#64748b;margin-top:4px">Oleh: <b>{{ $order->courier->name }}</b></div>
                                @endif
                            @elseif($order->status === 'completed' || $order->status === 'confirmed')
                                <span class="adm-badge badge-ok">Selesai</span>
                            @endif
                        </td>
                        <td>
                            @if($order->status === 'waiting' || $order->status === 'waiting_owner' || $order->status === 'waiting_payment')
                                <form method="POST" action="{{ route('admin.orders.accept', $order) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn-confirm" type="submit">Terima Pesanan</button>
                                </form>
                            @elseif($order->status === 'accepted')
                                <form method="POST" action="{{ route('admin.orders.cook', $order) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn-confirm" type="submit">Mulai Masak</button>
                                </form>
                            @elseif($order->status === 'cooking')
                                <form method="POST" action="{{ route('admin.orders.cooked', $order) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn-confirm" type="submit">Selesai Masak</button>
                                </form>
                            @elseif($order->status === 'ready_to_deliver')
                                <form method="POST" action="{{ route('admin.orders.courier', $order) }}" style="display:flex;gap:6px;align-items:center;flex-wrap:wrap">
                                    @csrf @method('PATCH')
                                    <select name="courier_id" required style="padding:6px 8px;border:1px solid #e2e8f0;border-radius:7px;font-size:12px;flex:1;min-width:100px">
                                        <option value="">Pilih kurir</option>
                                        @foreach($couriers as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                                    </select>
                                    <button type="submit" class="btn-primary" style="padding:6px 10px;font-size:11px">Atur</button>
                                </form>
                            @elseif($order->status === 'delivering')
                                <form method="POST" action="{{ route('admin.orders.complete', $order) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn-confirm" type="submit" style="background:#22c55e">Pesanan Diterima</button>
                                </form>
                            @endif

                            @if(in_array($order->status, ['ready_to_deliver', 'delivering', 'completed']))
    <div style="margin-top:8px;font-size:11px;padding:4px;border-radius:4px;text-align:center;background:#f1f5f9;color:#64748b">
        Status WhatsApp diproses otomatis
    </div>
@endif

                            <a class="btn-receipt" target="_blank" href="{{ route('admin.orders.receipt', $order) }}" style="display:block;text-align:center;margin-top:6px;margin-left:0;width:100%;box-sizing:border-box">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px;display:inline-block"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>Struk
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;padding:40px;color:#94a3b8">Belum ada pesanan.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ===================== SECTION: MENU ===================== --}}
            @elseif($navSection === 'menu')
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:22px">
                <div class="adm-card" style="margin-bottom:0">
                    <div class="adm-card-head"><div class="adm-card-title">Tambah Menu Baru</div></div>
                    <form method="POST" action="{{ route('admin.menu.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="adm-form-grid">
                            <div class="adm-form-group"><label>Nama Menu</label><input name="name" placeholder="Nama menu" required></div>
                            <div class="adm-form-group"><label>Harga (Rp)</label><input name="price" type="number" min="0" placeholder="0" required></div>
                            <div class="adm-form-group"><label>Kategori</label><select name="category" required><option value="">Pilih...</option><option>Paket Nasi Liwet</option><option>Lauk Utama</option><option>Menu Tambahan</option></select></div>
                            <div class="adm-form-group"><label>Foto Menu</label><input name="image" type="file" accept="image/*"></div>
                        </div>
                        <div class="adm-form-group"><label>Deskripsi</label><input name="description" placeholder="Deskripsi singkat"></div>
                        <div class="adm-form-actions"><button type="submit" class="btn-primary">Simpan Menu</button></div>
                    </form>
                </div>
                <div class="adm-card" style="margin-bottom:0">
                    <div class="adm-card-head"><div class="adm-card-title">Tambah Kurir</div></div>
                    <form method="POST" action="{{ route('admin.couriers.store') }}">
                        @csrf
                        <div class="adm-form-group" style="margin-bottom:10px"><label>Nama Kurir</label><input name="name" placeholder="Nama kurir" required></div>
                        <div class="adm-form-group"><label>Nomor WhatsApp</label><input name="phone" placeholder="08xx-xxxx-xxxx" required></div>
                        <div class="adm-form-actions"><button type="submit" class="btn-primary">Simpan Kurir</button></div>
                    </form>
                </div>
            </div>
            <div class="adm-card">
                <div class="adm-card-head">
                    <div>
                        <div class="adm-card-title">Kelola Menu</div>
                        <div class="adm-card-sub">{{ $managedMenu->count() }} menu tersedia &bull; dikelompokkan per kategori</div>
                    </div>
                </div>
                @if($managedMenu->count())
                    @foreach($menuByCategory as $cat => $items)
                    <details class="adm-cat-details">
                        <summary class="adm-cat-summary">
                            <div class="adm-cat-icon">{{ $cat === 'Paket Nasi Liwet' ? '🍛' : ($cat === 'Lauk Utama' ? '🥘' : '🍟') }}</div>
                            {{ $cat }}
                            <span class="adm-cat-count">{{ $items->count() }}</span>
                            <span class="adm-cat-arrow">&#9654;</span>
                        </summary>
                        <table class="adm-table">
                            <thead><tr><th>Nama</th><th>Harga</th><th style="text-align:center">Aksi</th></tr></thead>
                            <tbody>
                            @foreach($items as $item)
                            <tr>
                                <td><b>{{ $item->name }}</b>@if($item->description)<div style="font-size:11px;color:#94a3b8">{{ Str::limit($item->description, 50) }}</div>@endif</td>
                                <td style="color:#f97316;font-weight:700">Rp {{ number_format($item->price,0,',','.') }}</td>
                                <td style="text-align:center">
                                    <button class="btn-ghost" onclick="document.getElementById('edit-menu-{{ $item->id }}').style.display='block'">Edit</button>
                                    <form method="POST" action="{{ route('admin.menu.delete', $item) }}" style="display:inline" onsubmit="return confirm('Hapus menu ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn-danger" type="submit">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            <tr id="edit-menu-{{ $item->id }}" style="display:none">
                                <td colspan="3" style="padding:14px;background:#fafafa">
                                    <form method="POST" action="{{ route('admin.menu.update', $item) }}" enctype="multipart/form-data">
                                        @csrf @method('PATCH')
                                        <div class="adm-form-grid">
                                            <div class="adm-form-group"><label>Nama</label><input name="name" value="{{ $item->name }}" required></div>
                                            <div class="adm-form-group"><label>Harga</label><input name="price" type="number" value="{{ $item->price }}" required></div>
                                            <div class="adm-form-group"><label>Kategori</label><select name="category" required><option value="Paket Nasi Liwet" {{ $item->category==='Paket Nasi Liwet'?'selected':'' }}>Paket Nasi Liwet</option><option value="Lauk Utama" {{ $item->category==='Lauk Utama'?'selected':'' }}>Lauk Utama</option><option value="Menu Tambahan" {{ $item->category==='Menu Tambahan'?'selected':'' }}>Menu Tambahan</option></select></div>
                                            <div class="adm-form-group"><label>Foto Baru</label><input name="image" type="file" accept="image/*"></div>
                                        </div>
                                        <div class="adm-form-group"><label>Deskripsi</label><textarea name="description">{{ $item->description }}</textarea></div>
                                        <div class="adm-form-actions">
                                            <button type="submit" class="btn-primary">Simpan Perubahan</button>
                                            <button type="button" class="btn-ghost" onclick="document.getElementById('edit-menu-{{ $item->id }}').style.display='none'">Batal</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </details>
                    @endforeach
                @else
                    <p style="color:#94a3b8;text-align:center;padding:30px">Belum ada menu yang ditambahkan.</p>
                @endif
            </div>

            {{-- ===================== SECTION: COURIERS ===================== --}}
            @elseif($navSection === 'couriers')
            <div class="adm-card">
                <div class="adm-card-head"><div class="adm-card-title">Data Kurir</div></div>
                @if($couriers->count())
                <div class="adm-courier-grid">
                    @foreach($couriers as $courier)
                    <div class="adm-courier-card">
                        <div class="adm-courier-name">{{ $courier->name }}</div>
                        <div class="adm-courier-phone">{{ $courier->phone }}</div>
                        <div class="adm-courier-stat">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            {{ $courier->orders_count }} pengantaran
                        </div>
                        <div class="adm-courier-actions">
                            <form method="POST" action="{{ route('admin.couriers.reset-orders', $courier) }}">
                                @csrf @method('PATCH')
                                <button class="btn-ghost" type="submit">Reset</button>
                            </form>
                            <button class="btn-ghost" onclick="document.getElementById('edit-c-{{ $courier->id }}').style.display='block'">Edit</button>
                            <form method="POST" action="{{ route('admin.couriers.delete', $courier) }}" onsubmit="return confirm('Hapus kurir?')">
                                @csrf @method('DELETE')
                                <button class="btn-danger" type="submit">Hapus</button>
                            </form>
                        </div>
                        <div class="adm-courier-edit-form" id="edit-c-{{ $courier->id }}">
                            <form method="POST" action="{{ route('admin.couriers.update', $courier) }}">
                                @csrf @method('PATCH')
                                <div class="adm-form-group" style="margin-bottom:8px"><label>Nama</label><input name="name" value="{{ $courier->name }}" required></div>
                                <div class="adm-form-group" style="margin-bottom:10px"><label>WhatsApp</label><input name="phone" value="{{ $courier->phone }}" required></div>
                                <div class="adm-form-actions">
                                    <button type="submit" class="btn-primary" style="font-size:11px;padding:6px 12px">Simpan</button>
                                    <button type="button" class="btn-ghost" onclick="document.getElementById('edit-c-{{ $courier->id }}').style.display='none'">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p style="color:#94a3b8;text-align:center;padding:30px">Belum ada kurir. Tambahkan dari menu Kelola Menu.</p>
                @endif
            </div>

            {{-- ===================== SECTION: REPORTS ===================== --}}
            @elseif($navSection === 'reports')
            <div class="adm-report-header">
                <div>
                    <div class="adm-report-title">Laporan Penjualan</div>
                    <div class="adm-report-description">Ringkasan penjualan dan operasional berdasarkan periode yang dipilih.</div>
                </div>
            </div>
            <div class="adm-card" style="margin-bottom:18px">
                <form class="adm-report-filter" method="GET" action="{{ route('admin.dashboard') }}">
                    <input type="hidden" name="section" value="reports">
                    <div class="adm-form-group"><label for="report-from">Dari tanggal</label><input id="report-from" type="date" name="from" value="{{ $reportFrom }}"></div>
                    <div class="adm-form-group"><label for="report-to">Sampai tanggal</label><input id="report-to" type="date" name="to" value="{{ $reportTo }}"></div>
                    <button type="submit" class="btn-primary">Tampilkan Laporan</button>
                </form>
            </div>
            <div class="adm-report-grid">
                <div class="adm-report-stat"><div class="adm-report-stat-label">Omzet</div><div class="adm-report-stat-value">Rp {{ number_format($reportRevenue, 0, ',', '.') }}</div></div>
                <div class="adm-report-stat"><div class="adm-report-stat-label">Pesanan selesai</div><div class="adm-report-stat-value">{{ $reportOrders->count() }}</div></div>
                <div class="adm-report-stat"><div class="adm-report-stat-label">Total ongkir</div><div class="adm-report-stat-value">Rp {{ number_format($reportDelivery, 0, ',', '.') }}</div></div>
                <div class="adm-report-stat"><div class="adm-report-stat-label">Pencairan kurir</div><div class="adm-report-stat-value">Rp {{ number_format($reportPayouts, 0, ',', '.') }}</div></div>
                <div class="adm-report-stat"><div class="adm-report-stat-label">Estimasi bersih</div><div class="adm-report-stat-value">Rp {{ number_format($reportProfit, 0, ',', '.') }}</div></div>
                <div class="adm-report-stat"><div class="adm-report-stat-label">Periode aktif</div><div class="adm-report-stat-value" style="font-size:16px;line-height:1.35">{{ \Carbon\Carbon::parse($reportFrom)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($reportTo)->format('d/m/Y') }}</div></div>
            </div>
            <div class="adm-report-layout">
                <div class="adm-card">
                    <div class="adm-card-head"><div><div class="adm-card-title">Menu Paling Laku</div><div class="adm-card-sub">Berdasarkan jumlah item pada pesanan selesai.</div></div></div>
                    @if($bestSellingMenus->count())
                    <table class="adm-payout-table"><thead><tr><th>Menu</th><th>Terjual</th><th>Penjualan</th></tr></thead><tbody>
                    @foreach($bestSellingMenus as $menuReport)
                    <tr><td><b>{{ $menuReport->menu_name }}</b></td><td>{{ $menuReport->total_quantity }} item</td><td style="color:#f97316;font-weight:700">Rp {{ number_format($menuReport->total_sales, 0, ',', '.') }}</td></tr>
                    @endforeach
                    </tbody></table>
                    @else
                    <p style="color:#94a3b8;text-align:center;padding:30px">Belum ada data menu pada periode ini.</p>
                    @endif
                </div>
                <div class="adm-card">
                    <div class="adm-card-head"><div><div class="adm-card-title">Catatan Keuangan</div><div class="adm-card-sub">Ringkasan untuk evaluasi usaha.</div></div></div>
                    <p style="color:#64748b;line-height:1.7;font-size:13px;margin-bottom:12px">Estimasi bersih dihitung dari omzet dikurangi seluruh pencairan kurir dalam periode ini. Biaya bahan baku dan operasional lain belum termasuk.</p>
                    <p style="color:#64748b;line-height:1.7;font-size:13px;margin:0">Total ongkir memakai tarif saat ini, yaitu Rp1.000 per pesanan selesai.</p>
                </div>
            </div>

            {{-- ===================== SECTION: PAYOUTS ===================== --}}
            @elseif($navSection === 'payouts')
            <div style="display:grid;grid-template-columns:1fr 2fr;gap:18px">
                <div class="adm-card" style="margin-bottom:0">
                    <div class="adm-card-head"><div class="adm-card-title">Buat Pencairan</div></div>
                    <form method="POST" action="{{ route('admin.payouts.store') }}">
                        @csrf
                        <div class="adm-form-group" style="margin-bottom:10px"><label>Kurir</label><select name="courier_id" required><option value="">Pilih kurir</option>@foreach($couriers as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
                        <div class="adm-form-group" style="margin-bottom:10px"><label>Nominal</label><input name="amount" type="number" min="1" placeholder="Nominal pencairan" required></div>
                        <div class="adm-form-group" style="margin-bottom:10px"><label>Periode</label><input name="period" placeholder="Agustus 2026" required></div>
                        <div class="adm-form-group" style="margin-bottom:12px"><label>Catatan</label><input name="notes" placeholder="Opsional"></div>
                        <button type="submit" class="btn-primary">Buat Pencairan</button>
                    </form>
                </div>
                <div class="adm-card" style="margin-bottom:0">
                    <div class="adm-card-head">
                        <div class="adm-card-title">Riwayat Pencairan</div>
                        @if($payouts->count())
                        <form method="POST" action="{{ route('admin.payouts.clear') }}" onsubmit="return confirm('Hapus semua riwayat pencairan? Tindakan ini tidak dapat dibatalkan.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger">Hapus Semua Riwayat</button>
                        </form>
                        @endif
                    </div>
                    @if($payouts->count())
                    <table class="adm-payout-table">
                        <thead><tr><th>Kurir</th><th>Periode</th><th>Nominal</th><th>Status</th></tr></thead>
                        <tbody>
                        @foreach($payouts as $payout)
                        <tr>
                            <td><b>{{ $payout->courier->name }}</b></td>
                            <td>{{ $payout->period }}</td>
                            <td style="color:#f97316;font-weight:700">Rp {{ number_format($payout->amount,0,',','.') }}</td>
                            <td>
                                @if($payout->status === 'paid')
                                    <span class="adm-badge badge-ok">Sudah dibayar</span>
                                @else
                                    <span class="adm-badge badge-wait">Belum</span>
                                    <form method="POST" action="{{ route('admin.payouts.paid', $payout) }}" style="margin-top:6px">
                                        @csrf @method('PATCH')
                                        <button class="btn-paid" type="submit">Tandai Dibayar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @else
                    <p style="color:#94a3b8;text-align:center;padding:30px">Belum ada data pencairan.</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
</body>
