<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Admin | 3GFood</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">
    @vite(['resources/css/app.css'])
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{background:#ede5d8;color:#1c1917;font:14px 'Inter',sans-serif;min-height:100vh}
        a{text-decoration:none;color:inherit}
        input,select,textarea,button{font:inherit}

        /* === ADMIN SHELL === */
        .adm-shell{display:flex;min-height:100vh}

        /* === SIDEBAR === */
        .adm-sidebar{width:250px;background:#1a0307;color:#fff;display:flex;flex-direction:column;flex-shrink:0;position:sticky;top:0;height:100vh;overflow-y:auto;border-right:1px solid #3d0a13}
        .adm-logo{padding:22px 20px 18px;border-bottom:1px solid rgba(255,255,255,.07)}
        .adm-logo-inner{display:flex;align-items:center;gap:12px;text-decoration:none}
        .adm-logo-img{width:38px;height:38px;border-radius:11px;object-fit:contain;background:#f4ece1;border:1px solid rgba(255,255,255,0.18);box-shadow:0 3px 10px rgba(0,0,0,0.3);flex-shrink:0}
        .adm-logo-name{font:800 20px 'Outfit',sans-serif;color:#fff;letter-spacing:-0.5px;line-height:1.1}
        .adm-logo-name em{color:#e50027;font-style:normal}
        .adm-logo-sub{font-size:10px;color:#fecdd3;margin-top:3px;letter-spacing:0.5px;text-transform:uppercase;font-weight:600}
        .adm-nav{padding:20px 14px;flex:1}
        .adm-nav-label{font-size:10px;font-weight:700;letter-spacing:1px;color:#fda4af;margin:16px 10px 10px;text-transform:uppercase;opacity:0.7}
        .adm-nav-link{display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:10px;color:#cbd5e1;font-weight:500;margin-bottom:4px;cursor:pointer;transition:all .2s;font-size:13px}
        .adm-nav-link svg{width:18px;height:18px;stroke-width:2;flex-shrink:0;opacity:0.75}
        .adm-nav-link:hover,.adm-nav-link.active{background:rgba(229,0,39,.18);color:#ff4d6d}
        .adm-nav-link:hover svg,.adm-nav-link.active svg{opacity:1;stroke:#ff4d6d}
        .adm-nav-link.active{font-weight:600;background:linear-gradient(to right, rgba(229,0,39,.22), transparent); border-left: 3px solid #e50027;}
        .adm-nav-badge{margin-left:auto;background:#e50027;color:#fff;border-radius:99px;font-size:10px;font-weight:700;padding:2px 8px;min-width:20px;text-align:center;box-shadow: 0 0 10px rgba(229,0,39,.45)}
        .adm-sidebar-footer{padding:20px 14px;border-top:1px solid rgba(255,255,255,.07)}
        .adm-sidebar-footer form button{display:flex;align-items:center;gap:10px;width:100%;padding:12px 14px;border-radius:10px;background:rgba(239,68,68,.12);color:#f87171;border:0;cursor:pointer;font-weight:600;font-size:13px;transition:all .2s}
        .adm-sidebar-footer form button:hover{background:rgba(239,68,68,.22);color:#fca5a5}

        /* === MAIN CONTENT === */
        .adm-main{flex:1;display:flex;flex-direction:column;min-width:0}
        .adm-topbar{background:#f4ece1;border-bottom:1px solid #d5c7b2;padding:0 32px;height:70px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;box-shadow:0 1px 3px rgba(40,20,10,.03)}
        .adm-topbar-title{font:800 20px 'Outfit',sans-serif;color:#1c1917;letter-spacing:-0.5px}
        .adm-topbar-right{display:flex;align-items:center;gap:12px}
        .adm-store-link{display:flex;align-items:center;gap:8px;padding:8px 16px;background:#fff1f2;color:#e50027;border-radius:99px;font-size:13px;font-weight:600;border:1px solid #fecdd3;transition:all .2s}
        .adm-store-link:hover{background:#ffe4e6;}
        .adm-store-link svg{width:16px;height:16px;stroke-width:2}
        .adm-content{padding:32px;flex:1}

        /* === SUCCESS FLASH === */
        .adm-flash{background:#fff1f2;border:1px solid #fecdd3;color:#9f1239;border-radius:12px;padding:16px 20px;margin-bottom:24px;font-weight:500;font-size:13px;box-shadow:0 4px 6px -1px rgba(229,0,39,.1)}

        /* === METRIC CARDS === */
        .adm-metrics{display:grid;grid-template-columns:repeat(5,1fr);gap:20px;margin-bottom:32px}
        .adm-metric{background:#f4ece1;border-radius:16px;padding:24px;box-shadow:0 1px 3px rgba(40,20,10,.04);border:1px solid #d5c7b2;transition:all .2s;position:relative;overflow:hidden}
        .adm-metric::after{content:'';position:absolute;top:0;right:0;width:100px;height:100px;background:radial-gradient(circle, rgba(0,0,0,.03) 0%, transparent 70%);border-radius:50%;transform:translate(30%,-30%)}
        .adm-metric:hover{box-shadow:0 10px 25px -5px rgba(40,20,10,.07),0 8px 10px -6px rgba(40,20,10,.02);transform:translateY(-2px)}
        .adm-metric-icon{width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:16px}
        .adm-metric-icon svg{width:24px;height:24px;stroke-width:2}
        .adm-metric-val{font:800 28px 'Outfit',sans-serif;color:#1c1917;line-height:1}
        .adm-metric-label{font-size:13px;color:#786d5e;margin-top:8px;font-weight:500}
        .ic-orange{background:#fff1f2}.ic-orange svg{stroke:#e50027}
        .ic-blue{background:#eff6ff}.ic-blue svg{stroke:#3b82f6}
        .ic-green{background:#f0fdf4}.ic-green svg{stroke:#22c55e}
        .ic-purple{background:#f5f3ff}.ic-purple svg{stroke:#8b5cf6}
        .ic-red{background:#fef2f2}.ic-red svg{stroke:#ef4444}

        .adm-card{background:#f4ece1;border-radius:16px;padding:24px;box-shadow:0 1px 3px rgba(40,20,10,.04);border:1px solid #d5c7b2;margin-bottom:24px;box-sizing:border-box;min-width:0;overflow:hidden}
        .adm-card-head{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px}
        .adm-card-title{font:800 18px 'Outfit',sans-serif;color:#1c1917}
        .adm-card-sub{font-size:13px;color:#786d5e;margin-top:4px}

        /* === STATUS TABS === */
        .adm-tabs{display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;background:#ede3d6;padding:6px;border-radius:12px;width:max-content}
        .adm-tab{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;color:#786d5e;border:0;cursor:pointer;transition:all .2s;background:transparent}
        .adm-tab.active{background:#f4ece1;color:#e50027;box-shadow:0 1px 3px rgba(229,0,39,.15);border-bottom:2px solid #e50027}
        .adm-tab:not(.active):hover{color:#1c1917;background:rgba(250,246,240,.7)}
        a.adm-tab{display:inline-block}

        /* === ORDERS TABLE === */
        .adm-table{width:100%;border-collapse:collapse}
        .adm-table th{text-align:left;padding:12px 16px;font-size:12px;font-weight:600;color:#786d5e;text-transform:uppercase;letter-spacing:0.5px;border-bottom:1px solid #d5c7b2;background:#ded2bf}
        .adm-table th:first-child{border-top-left-radius:10px}
        .adm-table th:last-child{border-top-right-radius:10px}
        .adm-table td{padding:16px;border-bottom:1px solid #d5c7b2;vertical-align:top;font-size:14px;color:#1c1917}
        .adm-table tr:last-child td{border-bottom:0}
        .adm-table tr:hover td{background:#e8decb}
        .adm-badge{display:inline-flex;align-items:center;padding:4px 10px;border-radius:99px;font-size:12px;font-weight:600}
        .badge-wait{background:#fef3c7;color:#92400e}
        .badge-ok{background:#d1fae5;color:#065f46}
        .badge-cash{background:#ede3d6;color:#574c3e}
        .badge-bank{background:#eff6ff;color:#1e40af}
        .badge-digital{background:#f3e8ff;color:#6b21a8}

        /* === ACTION BUTTONS === */
        .btn-primary{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:10px 16px;background:linear-gradient(135deg, #e50027, #b3001a);color:#fff;border:0;border-radius:10px;font-weight:600;font-size:13px;cursor:pointer;transition:all .2s;box-shadow:0 4px 6px -1px rgba(229,0,39,.25)}
        .btn-primary:hover{transform:translateY(-1px);box-shadow:0 6px 12px -1px rgba(229,0,39,.35);background:linear-gradient(135deg, #f0052d, #c20020)}
        .btn-danger{background:#fef2f2;color:#ef4444;border:1px solid #fecaca;border-radius:8px;padding:8px 12px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s}
        .btn-danger:hover{background:#fee2e2;color:#dc2626}
        .btn-ghost{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:8px 12px;background:#ede3d6;color:#574c3e;border:0;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;transition:background .2s}
        .btn-ghost:hover{background:#e2d6c6;color:#1c1917}
        
        .btn-wa{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:#22c55e;color:#fff;border-radius:8px;font-size:12px;font-weight:600;margin-top:6px;box-shadow:0 2px 4px rgba(34,197,94,.2)}
        .btn-receipt{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:#f4ece1;color:#1c1917;border-radius:8px;font-size:12px;font-weight:600;margin-top:6px;border:1px solid #d5c7b2;transition:all .2s}
        .btn-receipt:hover{background:#e8decb;border-color:#d5c7b2}
        
        .btn-confirm{padding:8px 14px;background:#e50027;color:#fff;border:0;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;margin-top:8px;width:100%;transition:all .2s;box-shadow:0 2px 4px rgba(229,0,39,.25)}
        .btn-confirm:hover{background:#b3001a}
        .btn-confirm-sm{padding:6px 12px;background:#e50027;color:#fff;border:0;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer}
        .btn-confirm-sm:hover{background:#b3001a}
        .btn-paid{padding:6px 12px;background:#22c55e;color:#fff;border:0;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;margin-top:4px}

        /* === FORMS === */
        .adm-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin-bottom:14px;width:100%;box-sizing:border-box}
        .adm-form-group{display:flex;flex-direction:column;gap:6px;min-width:0;width:100%;box-sizing:border-box}
        .adm-form-group label{font-size:11px;font-weight:700;color:#6e6153;text-transform:uppercase;letter-spacing:0.5px}
        .adm-form-group input,.adm-form-group select,.adm-form-group textarea{width:100%;max-width:100%;box-sizing:border-box;padding:10px 12px;border:1px solid #d5c7b2;border-radius:10px;font-size:13px;background:#e5dbcb;transition:all .2s;color:#1c1917}
        .adm-form-group input:focus,.adm-form-group select:focus,.adm-form-group textarea:focus{outline:none;border-color:#e50027;box-shadow:0 0 0 3px rgba(229,0,39,.18);background:#f4ece1}
        .adm-form-group textarea{min-height:85px;resize:vertical}
        .adm-form-group input[type="file"]{padding:7px 9px;font-size:11px;cursor:pointer;background:#ede3d6;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .adm-form-group input[type="file"]::-webkit-file-upload-button{background:#1c1917;color:#fff;border:0;padding:5px 10px;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;margin-right:6px;transition:background .2s}
        .adm-form-group input[type="file"]::-webkit-file-upload-button:hover{background:#e50027}
        .adm-form-actions{display:flex;gap:10px;margin-top:16px}

        /* === ACCORDION CATEGORIES === */
        .adm-cat-details{border:1px solid #d5c7b2;border-radius:14px;overflow:hidden;margin-bottom:16px;box-shadow:0 1px 2px rgba(40,20,10,.02)}
        .adm-cat-summary{padding:16px 20px;background:#e8decb;display:flex;align-items:center;gap:12px;cursor:pointer;list-style:none;font-weight:600;color:#1c1917;font-size:15px;user-select:none;transition:background .2s}
        .adm-cat-summary:hover{background:#ede3d6}
        .adm-cat-summary::-webkit-details-marker{display:none}
        .adm-cat-summary::marker{display:none}
        .adm-cat-icon{width:36px;height:36px;background:#fff1f2;color:#e50027;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px}
        .adm-cat-count{margin-left:auto;background:#d5c7b2;color:#6e6153;border-radius:99px;font-size:12px;padding:4px 10px;font-weight:600}
        .adm-cat-details[open] .adm-cat-summary{border-bottom:1px solid #d5c7b2;background:#f4ece1}
        .adm-cat-arrow{transition:transform .2s;margin-left:8px;color:#94a3b8}
        .adm-cat-details[open] .adm-cat-arrow{transform:rotate(90deg)}

        /* === COURIER GRID === */
        .adm-courier-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;margin-top:16px}
        .adm-courier-card{background:#f4ece1;border:1px solid #d5c7b2;border-radius:14px;padding:24px;box-shadow:0 1px 3px rgba(40,20,10,.02);transition:transform .2s}
        .adm-courier-card:hover{transform:translateY(-2px);box-shadow:0 4px 6px -1px rgba(40,20,10,.05)}
        .adm-courier-name{font:800 18px 'Outfit',sans-serif;margin-bottom:6px;color:#1c1917}
        .adm-courier-phone{font-size:14px;color:#786d5e;margin-bottom:16px}
        .adm-courier-stat{display:inline-flex;align-items:center;gap:6px;background:#fff1f2;color:#e50027;padding:6px 12px;border-radius:99px;font-size:12px;font-weight:600;margin-bottom:20px}
        .adm-courier-actions{display:flex;gap:8px;flex-wrap:wrap}
        .adm-courier-edit-form{display:none;margin-top:16px;padding:16px;background:#e8decb;border:1px solid #d5c7b2;border-radius:12px}

        /* === PAYOUT TABLE === */
        .adm-payout-table{width:100%;border-collapse:collapse;font-size:14px}
        .adm-payout-table th{text-align:left;padding:12px 16px;font-size:12px;color:#786d5e;font-weight:600;text-transform:uppercase;border-bottom:1px solid #d5c7b2;background:#ded2bf}
        .adm-payout-table td{padding:16px;border-bottom:1px solid #d5c7b2;vertical-align:middle;color:#1c1917}

        /* === REPORTS === */
        .adm-report-header{display:flex;align-items:flex-end;justify-content:space-between;gap:24px;margin-bottom:32px}
        .adm-report-title{font:800 32px/1.2 'Outfit',sans-serif;color:#1c1917;letter-spacing:-0.5px}
        .adm-report-description{font-size:14px;color:#786d5e;margin-top:8px}
        .adm-report-filter{display:flex;align-items:end;gap:16px;flex-wrap:wrap;padding:20px;background:#f4ece1;border:1px solid #d5c7b2;border-radius:16px;box-shadow:0 1px 3px rgba(40,20,10,.02)}
        .adm-report-filter .adm-form-group{min-width:160px}
        .adm-report-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px;margin-bottom:32px}
        .adm-report-stat{background:#f4ece1;border:1px solid #d5c7b2;border-radius:16px;padding:24px;box-shadow:0 1px 3px rgba(40,20,10,.02);position:relative;overflow:hidden}
        .adm-report-stat::before{content:'';position:absolute;left:0;top:0;width:4px;height:100%;background:#e50027}
        .adm-report-stat-label{font-size:12px;color:#786d5e;font-weight:600;text-transform:uppercase;letter-spacing:0.5px}
        .adm-report-stat-value{font:800 28px 'Outfit',sans-serif;color:#1c1917;margin-top:10px}
        .adm-report-layout{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(320px,1fr);gap:24px;align-items:start}
        .adm-report-layout .adm-card{margin-bottom:0}

        /* === TABS (section switching) === */
        .adm-section{display:none}
        .adm-section.active{display:block}

        .adm-table-wrap{width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch;border-radius:12px;margin-bottom:8px}
        .adm-table{width:100%;min-width:680px;border-collapse:collapse}

        /* === MOBILE DRAWER & REALTIME PILL === */
        .adm-hamburger-btn {
            display: none;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #ede3d6;
            border: 1px solid #d5c7b2;
            color: #1c1917;
            cursor: pointer;
            position: relative;
            font-size: 15px;
            transition: all .2s;
            flex-shrink: 0;
        }
        .adm-hamburger-btn:hover { background: #e2d6c6; }
        .adm-badge-dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #e50027;
            box-shadow: 0 0 6px rgba(229,0,39,.6);
        }
        .adm-sidebar-close {
            display: none;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: rgba(255,255,255,0.12);
            border: 0;
            color: #fff;
            cursor: pointer;
            margin-left: auto;
        }
        .adm-sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(10,2,4,0.65);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 998;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .adm-sidebar-backdrop.active {
            display: block;
            opacity: 1;
        }

        .adm-realtime-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 6px 12px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }
        .adm-pulse-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            animation: admPulse 1.8s infinite;
        }
        @keyframes admPulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }

        .adm-metric-pulse {
            animation: admValPulse 0.5s ease;
        }
        @keyframes admValPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.15); color: #e50027; }
            100% { transform: scale(1); }
        }

        @keyframes admToastSlide {
            from { opacity: 0; transform: translateY(20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* === CHAT GRID RESPONSIVE === */
        .adm-chat-grid {
            display: grid;
            grid-template-columns: 340px 1fr;
            height: 650px;
        }

        .adm-vouchers-layout {
            display: grid;
            grid-template-columns: 1.1fr 1.2fr;
            gap: 18px;
            align-items: start;
        }

        .adm-payout-layout {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 18px;
        }

        .adm-settings-layout {
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 18px;
            align-items: start;
        }

        .adm-menu-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr);
            gap: 20px;
            margin-bottom: 22px;
            align-items: start;
        }

        @media(max-width: 991px) {
            .adm-hamburger-btn { display: inline-flex; }
            .adm-sidebar-close { display: inline-flex; }
            .adm-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: 270px;
                height: 100vh;
                z-index: 999;
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 10px 30px rgba(0,0,0,0.45);
            }
            .adm-sidebar.open {
                transform: translateX(0);
            }
            .adm-topbar {
                padding: 0 16px;
                height: 64px;
            }
            .adm-topbar-title {
                font-size: 16px;
            }
            .adm-metrics {
                grid-template-columns: repeat(3, 1fr);
                gap: 14px;
                margin-bottom: 24px;
            }
            .adm-report-layout {
                grid-template-columns: 1fr;
            }
            .adm-report-grid {
                grid-template-columns: 1fr 1fr;
            }
            .adm-chat-grid {
                grid-template-columns: 1fr;
                height: 75vh;
            }
            .adm-vouchers-layout, .adm-payout-layout, .adm-settings-layout {
                grid-template-columns: 1fr;
            }
            .adm-menu-grid {
                grid-template-columns: 1fr;
            }
        }
        @media(max-width: 640px) {
            .adm-form-grid {
                grid-template-columns: 1fr;
            }
            .adm-content {
                padding: 16px 12px;
            }
            .adm-topbar {
                padding: 0 12px;
            }
            .adm-metrics {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
                margin-bottom: 20px;
            }
            .adm-metric {
                padding: 16px 14px;
                border-radius: 12px;
            }
            .adm-metric-icon {
                width: 36px;
                height: 36px;
                border-radius: 10px;
                margin-bottom: 10px;
            }
            .adm-metric-icon svg {
                width: 18px;
                height: 18px;
            }
            .adm-metric-val {
                font-size: 20px;
            }
            .adm-metric-label {
                font-size: 11px;
            }
            .adm-card {
                padding: 18px 14px;
                border-radius: 14px;
            }
            .adm-card-head {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .adm-tabs {
                width: 100%;
                overflow-x: auto;
                white-space: nowrap;
                -webkit-overflow-scrolling: touch;
            }
            .adm-form-grid {
                grid-template-columns: 1fr;
            }
            .adm-report-grid {
                grid-template-columns: 1fr;
            }
            .adm-report-filter {
                flex-direction: column;
                align-items: stretch;
            }
            .adm-report-filter .adm-form-group {
                min-width: 100%;
            }
            .adm-report-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            .adm-realtime-text {
                display: none;
            }
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
<div id="admSidebarBackdrop" class="adm-sidebar-backdrop" onclick="toggleAdminSidebar(false)"></div>
<div class="adm-shell">
    <!-- SIDEBAR -->
    <aside class="adm-sidebar">
        <div class="adm-logo">
            <a href="{{ route('admin.dashboard') }}" class="adm-logo-inner">
                <img src="{{ asset('images/logo.jpg') }}" alt="3GFood" class="adm-logo-img">
                <div>
                    <div class="adm-logo-name">3G<em>Food</em></div>
                    <div class="adm-logo-sub">Saveur & Tradition</div>
                </div>
            </a>
            <button type="button" class="adm-sidebar-close" onclick="toggleAdminSidebar(false)" aria-label="Tutup menu">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <nav class="adm-nav">
            <div class="adm-nav-label">Menu Utama</div>
            <a class="adm-nav-link {{ $navSection === 'orders' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}?section=orders">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                <span>Pesanan</span>
                <span class="adm-nav-badge" id="sidebar-waiting-badge" style="{{ $waiting > 0 ? '' : 'display:none' }}">{{ $waiting }}</span>
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
            <a class="adm-nav-link {{ $navSection === 'vouchers' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}?section=vouchers">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 7h18v10H3z"/><path d="M7 7V5h10v2M9 12h6"/></svg>
                <span>Voucher</span>
            </a>
            <a class="adm-nav-link {{ $navSection === 'settings' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}?section=settings">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 00.34 1.87l.06.06a2 2 0 01-2.83 2.83l-.06-.06A1.7 1.7 0 0015 19.4a1.7 1.7 0 00-1 .6 1.7 1.7 0 00-.21 1.14V21a2 2 0 01-4 0v-.09A1.7 1.7 0 009 19.4a1.7 1.7 0 00-1-.6 1.7 1.7 0 00-1.14.21l-.09.06a2 2 0 01-2.83-2.83l.06-.06A1.7 1.7 0 004.6 15a1.7 1.7 0 00-.6-1 1.7 1.7 0 00-1.14-.21H2.77a2 2 0 010-4h.09A1.7 1.7 0 004.6 9a1.7 1.7 0 00.6-1 1.7 1.7 0 00-.21-1.14l-.06-.09a2 2 0 012.83-2.83l.06.06A1.7 1.7 0 009 4.6a1.7 1.7 0 001-.6 1.7 1.7 0 00.21-1.14V2.77a2 2 0 014 0v.09A1.7 1.7 0 0015 4.6a1.7 1.7 0 001 .6 1.7 1.7 0 001.14-.21l.09-.06a2 2 0 012.83 2.83l-.06.06A1.7 1.7 0 0019.4 9c.25.25.6.41 1 .4h.09a2 2 0 010 4h-.09a1.7 1.7 0 00-1 .4 1.7 1.7 0 00-.4 1z"/></svg>
                <span>Toko</span>
            </a>
            <a class="adm-nav-link {{ $navSection === 'payouts' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}?section=payouts">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                <span>Pencairan</span>
            </a>
            <a class="adm-nav-link {{ $navSection === 'reports' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}?section=reports">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 19V5M4 19h16"/><path d="M8 16v-5M12 16V7M16 16v-3"/></svg>
                <span>Laporan</span>
            </a>
            <a class="adm-nav-link {{ $navSection === 'chat' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}?section=chat">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                <span>Live Chat</span>
                <span id="admin-chat-unread-badge" class="adm-nav-badge" style="display:none;background:#e50027;color:#fff;font-weight:700">0</span>
            </a>
            <div class="adm-nav-label" style="margin-top:24px">Lainnya</div>
            <a class="adm-nav-link" href="{{ route('register') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                <span>Tambah Admin</span>
            </a>
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
            <div style="display:flex;align-items:center;gap:12px">
                <button type="button" class="adm-hamburger-btn" onclick="toggleAdminSidebar(true)" aria-label="Buka menu navigasi">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                    <span id="admHamburgerDot" class="adm-badge-dot" style="{{ $waiting > 0 ? '' : 'display:none;' }}"></span>
                </button>
                <div class="adm-topbar-title">
                    @if($navSection === 'orders') Manajemen Pesanan
                    @elseif($navSection === 'menu') Kelola Menu
                    @elseif($navSection === 'couriers') Data Kurir
                    @elseif($navSection === 'vouchers') Kelola Voucher
                    @elseif($navSection === 'settings') Pengaturan Toko
                    @elseif($navSection === 'payouts') Pencairan Kurir
                    @elseif($navSection === 'reports') Laporan Penjualan
                    @elseif($navSection === 'chat') Live Chat Pelanggan
                    @else Dashboard Admin @endif
                </div>
            </div>
            <div class="adm-topbar-right">
                <div class="adm-realtime-pill" title="Sistem terhubung realtime ke server">
                    <span class="adm-pulse-dot"></span>
                    <span class="adm-realtime-text">LIVE REALTIME</span>
                </div>
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
            @if($navSection !== 'reports' && $navSection !== 'chat')
            <div class="adm-metrics">
                <div class="adm-metric">
                    <div class="adm-metric-icon ic-orange"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg></div>
                    <div class="adm-metric-val" id="metric-total-orders">{{ $orders->count() }}</div>
                    <div class="adm-metric-label">Total Pesanan</div>
                </div>
                <div class="adm-metric">
                    <div class="adm-metric-icon ic-red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
                    <div class="adm-metric-val" id="metric-waiting-orders">{{ $waiting }}</div>
                    <div class="adm-metric-label">Perlu Diproses</div>
                </div>
                <div class="adm-metric">
                    <div class="adm-metric-icon ic-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
                    <div class="adm-metric-val" id="metric-completed-orders">{{ $orders->where('status','completed')->count() }}</div>
                    <div class="adm-metric-label">Pesanan Selesai</div>
                </div>
                <div class="adm-metric">
                    <div class="adm-metric-icon ic-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg></div>
                    <div class="adm-metric-val" id="metric-courier-count">{{ $couriers->count() }}</div>
                    <div class="adm-metric-label">Kurir Aktif</div>
                </div>
                <div class="adm-metric">
                    <div class="adm-metric-icon ic-purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
                    <div class="adm-metric-val" id="metric-revenue" style="font-size:16px">Rp {{ number_format($revenue,0,',','.') }}</div>
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
                <div class="adm-table-wrap">
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
                    <tbody id="adminOrdersTableBody">
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
                            @if($order->customer)
                                <span style="display:inline-block; font-size:10px; background:#dcfce7; color:#166534; padding:1px 6px; border-radius:99px; margin-left:4px; font-weight:600;">Member: {{ $order->customer->email }}</span>
                            @endif
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
                            @if($order->payment_proof)
                            <a href="{{ $order->payment_proof }}" target="_blank" style="display:block; margin-top:6px; font-size:11px; color:#3b82f6; text-decoration:underline;">Lihat Bukti</a>
                            @endif
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
            </div>

            {{-- ===================== SECTION: MENU ===================== --}}
            @elseif($navSection === 'menu')
            <div class="adm-menu-grid">
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
                        <div class="adm-form-group" style="margin-bottom:12px">
                            <label style="display:inline-flex;align-items:center;gap:8px;cursor:pointer;font-weight:600;font-size:13px;color:#1c1917">
                                <input type="checkbox" name="is_visible" value="1" checked style="width:16px;height:16px;accent-color:#e50027">
                                Tampilkan langsung di etalase toko (Visible)
                            </label>
                        </div>
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
                        <div class="adm-card-sub">{{ $managedMenu->count() }} menu terdaftar &bull; dikelompokkan per kategori</div>
                    </div>
                </div>
                @if($managedMenu->count())
                    @foreach($menuByCategory as $cat => $items)
                    <details class="adm-cat-details" open>
                        <summary class="adm-cat-summary">
                            <div class="adm-cat-icon">{{ $cat === 'Paket Nasi Liwet' ? '🍛' : ($cat === 'Lauk Utama' ? '🥘' : '🍟') }}</div>
                            {{ $cat }}
                            <span class="adm-cat-count">{{ $items->count() }}</span>
                            <span class="adm-cat-arrow">&#9654;</span>
                        </summary>
                        <div class="adm-table-wrap">
                        <table class="adm-table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Harga</th>
                                    <th style="text-align:center">Status Etalase</th>
                                    <th style="text-align:center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($items as $item)
                            <tr id="menu-row-{{ $item->id }}" style="{{ !$item->is_visible ? 'opacity: 0.62; background: #fafafa;' : '' }}">
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px">
                                        @if($item->image_url)
                                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" style="width:36px;height:36px;border-radius:8px;object-fit:cover;border:1px solid #e2e8f0;flex-shrink:0">
                                        @endif
                                        <div>
                                            <b id="menu-name-text-{{ $item->id }}" style="{{ !$item->is_visible ? 'color:#64748b' : '' }}">{{ $item->name }}</b>
                                            @if($item->description)
                                                <div style="font-size:11px;color:#94a3b8">{{ Str::limit($item->description, 50) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td style="color:#f97316;font-weight:700">Rp {{ number_format($item->price,0,',','.') }}</td>
                                <td style="text-align:center">
                                    @if($item->is_visible)
                                        <span id="menu-status-badge-{{ $item->id }}" style="display:inline-flex;align-items:center;gap:5px;background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;padding:3px 9px;border-radius:99px;font-size:11px;font-weight:700;transition:all .25s ease">
                                            <span style="width:6px;height:6px;border-radius:50%;background:#10b981;display:inline-block"></span> Tampil
                                        </span>
                                    @else
                                        <span id="menu-status-badge-{{ $item->id }}" style="display:inline-flex;align-items:center;gap:5px;background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;padding:3px 9px;border-radius:99px;font-size:11px;font-weight:700;transition:all .25s ease">
                                            <span style="width:6px;height:6px;border-radius:50%;background:#ef4444;display:inline-block"></span> Tersembunyi
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align:center">
                                    <div style="display:inline-flex;align-items:center;gap:6px;flex-wrap:nowrap">
                                        <!-- Tombol Hide / Show Visibility Toggle (AJAX Realtime: Tanpa Reload & Tanpa Scroll) -->
                                        <form method="POST" action="{{ route('admin.menu.toggle-visibility', $item) }}" style="display:inline" onsubmit="toggleMenuVisibilityRealtime(event, this, {{ $item->id }})">
                                            @csrf @method('PATCH')
                                            @if($item->is_visible)
                                                <button id="menu-toggle-btn-{{ $item->id }}" class="btn-ghost" type="submit" style="color:#d97706;border-color:#fcd34d;background:#fffbeb;padding:5px 9px;font-size:11px;display:inline-flex;align-items:center;gap:4px;cursor:pointer;border-radius:8px;font-weight:600;border:1px solid #fcd34d;transition:all .2s" title="Sembunyikan menu dari etalase pembeli">
                                                    <i class="fas fa-eye-slash"></i> Sembunyikan
                                                </button>
                                            @else
                                                <button id="menu-toggle-btn-{{ $item->id }}" class="btn-ghost" type="submit" style="color:#047857;border-color:#a7f3d0;background:#ecfdf5;padding:5px 9px;font-size:11px;display:inline-flex;align-items:center;gap:4px;cursor:pointer;border-radius:8px;font-weight:600;border:1px solid #a7f3d0;transition:all .2s" title="Tampilkan menu di etalase pembeli">
                                                    <i class="fas fa-eye"></i> Tampilkan
                                                </button>
                                            @endif
                                        </form>

                                        <!-- Tombol Edit -->
                                        <button class="btn-ghost" type="button" onclick="document.getElementById('edit-menu-{{ $item->id }}').style.display='table-row'" style="padding:5px 9px;font-size:11px;display:inline-flex;align-items:center;gap:4px">
                                            <i class="fas fa-pen-to-square"></i> Edit
                                        </button>

                                        <!-- Tombol Hapus -->
                                        <form method="POST" action="{{ route('admin.menu.delete', $item) }}" style="display:inline" onsubmit="return confirm('Hapus menu ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn-danger" type="submit" style="padding:5px 9px;font-size:11px;display:inline-flex;align-items:center;gap:4px">
                                                <i class="fas fa-trash-can"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <tr id="edit-menu-{{ $item->id }}" style="display:none">
                                <td colspan="4" style="padding:14px;background:#fafafa">
                                    <form method="POST" action="{{ route('admin.menu.update', $item) }}" enctype="multipart/form-data">
                                        @csrf @method('PATCH')
                                        <div class="adm-form-grid">
                                            <div class="adm-form-group"><label>Nama</label><input name="name" value="{{ $item->name }}" required></div>
                                            <div class="adm-form-group"><label>Harga</label><input name="price" type="number" value="{{ $item->price }}" required></div>
                                            <div class="adm-form-group"><label>Kategori</label><select name="category" required><option value="Paket Nasi Liwet" {{ $item->category==='Paket Nasi Liwet'?'selected':'' }}>Paket Nasi Liwet</option><option value="Lauk Utama" {{ $item->category==='Lauk Utama'?'selected':'' }}>Lauk Utama</option><option value="Menu Tambahan" {{ $item->category==='Menu Tambahan'?'selected':'' }}>Menu Tambahan</option></select></div>
                                            <div class="adm-form-group"><label>Foto Baru</label><input name="image" type="file" accept="image/*"></div>
                                        </div>
                                        <div class="adm-form-group"><label>Deskripsi</label><textarea name="description">{{ $item->description }}</textarea></div>
                                        <div class="adm-form-group" style="margin-top:6px;margin-bottom:12px">
                                            <label style="display:inline-flex;align-items:center;gap:8px;cursor:pointer;font-weight:600;font-size:13px;color:#1c1917">
                                                <input type="checkbox" name="is_visible" value="1" {{ $item->is_visible ? 'checked' : '' }} style="width:16px;height:16px;accent-color:#e50027">
                                                Tampilkan di etalase toko (Visible untuk pembeli)
                                            </label>
                                        </div>
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
                        </div>
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

            {{-- ===================== SECTION: VOUCHERS ===================== --}}
            @elseif($navSection === 'vouchers')
            <div class="adm-vouchers-layout">
                <div class="adm-card" style="margin-bottom:0">
                    <div class="adm-card-head"><div><div class="adm-card-title">Buat Voucher Baru</div><div class="adm-card-sub">Atur promo untuk pelanggan.</div></div></div>
                    <form method="POST" action="{{ route('admin.vouchers.store') }}">
                        @csrf
                        <div class="adm-form-grid">
                            <div class="adm-form-group"><label>Kode Voucher</label><input name="kode_voucher" placeholder="SAVE50" required></div>
                            <div class="adm-form-group"><label>Jenis Potongan</label>
                                <select name="jenis_potongan" required>
                                    <option value="nominal">Nominal</option>
                                    <option value="persen">Persen</option>
                                </select>
                            </div>
                            <div class="adm-form-group"><label>Nilai Potongan</label><input name="nilai_potongan" type="number" min="1" placeholder="5000" required></div>
                            <div class="adm-form-group"><label>Minimal Belanja</label><input name="min_belanja" type="number" min="0" placeholder="50000" required></div>
                            <div class="adm-form-group"><label>Kuota</label><input name="kuota" type="number" min="1" placeholder="10" required></div>
                            <div class="adm-form-group"><label>Berakhir</label><input name="tanggal_berakhir" type="date" required></div>
                        </div>
                        <div class="adm-form-group" style="margin-bottom:12px">
                            <label>Status</label>
                            <select name="status" required>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary">Simpan Voucher</button>
                    </form>
                </div>
                <div class="adm-card" style="margin-bottom:0">
                    <div class="adm-card-head"><div><div class="adm-card-title">Daftar Voucher</div><div class="adm-card-sub">Promo aktif dan yang sudah dibuat.</div></div></div>
                    @if($vouchers->count())
                    <div class="adm-table-wrap">
                    <table class="adm-payout-table">
                        <thead><tr><th>Kode</th><th>Diskon</th><th>Min</th><th>Status</th><th>Aksi</th></tr></thead>
                        <tbody>
                        @foreach($vouchers as $voucher)
                        <tr>
                            <td><b>{{ $voucher->kode_voucher }}</b><div style="font-size:11px;color:#94a3b8">{{ $voucher->tanggal_berakhir ? \Carbon\Carbon::parse($voucher->tanggal_berakhir)->format('d/m/Y') : '-' }}</div></td>
                            <td>{{ $voucher->jenis_potongan === 'persen' ? $voucher->nilai_potongan.'%' : 'Rp '.number_format($voucher->nilai_potongan,0,',','.') }}</td>
                            <td>Rp {{ number_format($voucher->min_belanja,0,',','.') }}</td>
                            <td>@if($voucher->status === 'aktif')<span class="adm-badge badge-ok">Aktif</span>@else<span class="adm-badge badge-wait">Nonaktif</span>@endif</td>
                            <td>
                                <form method="POST" action="{{ route('admin.vouchers.delete', $voucher) }}" onsubmit="return confirm('Hapus voucher ini?')">
                                     @csrf @method('DELETE')
                                     <button class="btn-danger" type="submit" style="padding:4px 8px;font-size:11px">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                    @else
                    <p style="color:#94a3b8;text-align:center;padding:30px">Belum ada voucher yang dibuat.</p>
                    @endif
                </div>
            </div>

            {{-- ===================== SECTION: SETTINGS ===================== --}}
            @elseif($navSection === 'settings')
            <div class="adm-settings-layout">
                <div class="adm-card" style="margin-bottom:0">
                    <div class="adm-card-head"><div><div class="adm-card-title">Pengaturan Toko</div><div class="adm-card-sub">Atur jam buka dan status operasional.</div></div></div>
                    <form method="POST" action="{{ route('admin.settings.store') }}">
                        @csrf
                        <div class="adm-form-grid">
                            <div class="adm-form-group">
                                <label>Status Toko</label>
                                <select name="status_toko" required>
                                    <option value="buka" {{ ($shopSettings->status_toko ?? 'buka') === 'buka' ? 'selected' : '' }}>Buka</option>
                                    <option value="tutup" {{ ($shopSettings->status_toko ?? 'buka') === 'tutup' ? 'selected' : '' }}>Tutup</option>
                                </select>
                            </div>
                            <div class="adm-form-group"><label>Jam Buka</label><input type="time" name="jam_buka" value="{{ $shopSettings->jam_buka ?? '08:00' }}" required></div>
                            <div class="adm-form-group"><label>Jam Tutup</label><input type="time" name="jam_tutup" value="{{ $shopSettings->jam_tutup ?? '21:00' }}" required></div>
                        </div>
                        <button type="submit" class="btn-primary">Simpan Pengaturan</button>
                    </form>
                </div>
                <div class="adm-card" style="margin-bottom:0">
                    <div class="adm-card-head"><div><div class="adm-card-title">Identitas Brand & Status Toko</div><div class="adm-card-sub">Banner resmi dan status operasional yang tampil ke pembeli.</div></div></div>
                    <div style="display:flex;flex-direction:column;gap:12px">
                        <div style="border-radius:12px;overflow:hidden;border:1px solid #d5c7b2;background:#f4ece1;box-shadow:0 4px 12px rgba(40,20,10,0.04)">
                            <img src="{{ asset('images/banner.jpg') }}" alt="3GFood Banner" style="width:100%;height:auto;display:block;">
                        </div>
                        <div style="padding:14px 16px;border-radius:10px;background:{{ ($shopSettings->status_toko ?? 'buka') === 'buka' ? '#ecfdf5' : '#fef2f2' }};border:1px solid {{ ($shopSettings->status_toko ?? 'buka') === 'buka' ? '#a7f3d0' : '#fecaca' }};color:{{ ($shopSettings->status_toko ?? 'buka') === 'buka' ? '#065f46' : '#991b1b' }};font-weight:700">
                            {{ ($shopSettings->status_toko ?? 'buka') === 'buka' ? 'Toko sedang buka' : 'Toko sedang tutup' }}
                        </div>
                        <div style="padding:14px 16px;background:#e5dbcb;border:1px solid #d5c7b2;border-radius:10px;color:#475569;line-height:1.7">
                            <div><strong>Jam Operasional:</strong> {{ $shopSettings->jam_buka ?? '08:00' }} - {{ $shopSettings->jam_tutup ?? '21:00' }}</div>
                            <div style="margin-top:6px"><strong>Update Terakhir:</strong> {{ $shopSettings->updated_at ? $shopSettings->updated_at->format('d/m/Y H:i') : '-' }}</div>
                        </div>
                    </div>
                </div>
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
                    <div class="adm-table-wrap">
                    <table class="adm-payout-table"><thead><tr><th>Menu</th><th>Terjual</th><th>Penjualan</th></tr></thead><tbody>
                    @foreach($bestSellingMenus as $menuReport)
                    <tr><td><b>{{ $menuReport->menu_name }}</b></td><td>{{ $menuReport->total_quantity }} item</td><td style="color:#f97316;font-weight:700">Rp {{ number_format($menuReport->total_sales, 0, ',', '.') }}</td></tr>
                    @endforeach
                    </tbody></table>
                    </div>
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
            <div class="adm-payout-layout">
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
                    <div class="adm-table-wrap">
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
                    </div>
                    @else
                    <p style="color:#94a3b8;text-align:center;padding:30px">Belum ada data pencairan.</p>
                    @endif
                </div>
            </div>
            @endif

            {{-- ===================== SECTION: LIVE CHAT ===================== --}}
            @if($navSection === 'chat')
            <div class="adm-card" style="padding:0;overflow:hidden;border-radius:18px;margin-bottom:0">
                <div class="adm-chat-grid" id="adminChatGrid">
                    <!-- LEFT: CONVERSATION LIST -->
                    <div id="adminChatListCol" style="border-right:1px solid #d5c7b2;display:flex;flex-direction:column;background:#ede3d6;">
                        <div style="padding:16px 20px;border-bottom:1px solid #d5c7b2;background:#f4ece1;">
                            <div style="font:800 16px 'Outfit',sans-serif;color:#1c1917;display:flex;align-items:center;justify-content:space-between">
                                <span>Pesan Pelanggan</span>
                                <span id="adminChatTotalBadge" style="font-size:11px;background:#e50027;color:#fff;padding:2px 8px;border-radius:99px;font-weight:700">0</span>
                            </div>
                            <input type="text" id="adminSearchChat" onkeyup="filterAdminConversations()" placeholder="Cari nama pelanggan..." style="width:100%;margin-top:10px;padding:8px 12px;border:1px solid #d5c7b2;border-radius:8px;font-size:12px;background:#fdfbf7;outline:none;">
                        </div>
                        <div id="adminConversationList" style="flex:1;overflow-y:auto;padding:8px;">
                            <div style="text-align:center;padding:40px 20px;color:#786d5e;font-size:13px;">
                                <i class="fas fa-spinner fa-spin" style="margin-right:6px"></i> Memuat percakapan...
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: CHAT THREAD VIEW -->
                    <div id="adminThreadContainer" style="display:flex;flex-direction:column;background:#fdfbf7;">
                        <!-- State 1: No chat selected -->
                        <div id="adminNoChatSelected" style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#786d5e;text-align:center;padding:40px;">
                            <div style="width:64px;height:64px;border-radius:50%;background:#ede3d6;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                                <i class="fas fa-comments text-2xl" style="color:#e50027"></i>
                            </div>
                            <div style="font:700 16px 'Outfit',sans-serif;color:#1c1917;margin-bottom:4px">Pilih Percakapan</div>
                            <p style="font-size:13px;max-width:320px;margin:0;color:#786d5e">Pilih salah satu pesan pelanggan di sebelah kiri untuk membaca dan membalas secara langsung.</p>
                        </div>

                        <!-- State 2: Active Chat Thread -->
                        <div id="adminActiveChatBox" style="display:none;flex-direction:column;height:100%;">
                            <!-- Thread Header -->
                            <div style="padding:14px 20px;border-bottom:1px solid #d5c7b2;background:#f4ece1;display:flex;align-items:center;justify-content:space-between">
                                <div style="display:flex;align-items:center;gap:12px">
                                    <button type="button" id="adminChatBackBtn" class="btn-ghost" onclick="adminCloseMobileChat()" style="display:none;font-size:12px;padding:6px 10px;margin-right:4px">
                                        ← Kembali
                                    </button>
                                    <div id="adminThreadAvatar" style="width:40px;height:40px;border-radius:50%;background:#e5dbcb;display:flex;align-items:center;justify-content:center;font-weight:700;color:#e50027;font-size:16px;border:1px solid #d5c7b2;overflow:hidden">
                                        U
                                    </div>
                                    <div>
                                        <div style="display:flex;align-items:center;gap:8px">
                                            <span id="adminThreadName" style="font-weight:700;color:#1c1917;font-size:15px">Nama Pelanggan</span>
                                            <span id="adminThreadBadge" style="font-size:10px;padding:2px 6px;border-radius:4px;background:#ede3d6;color:#786d5e;font-weight:600">Member</span>
                                        </div>
                                        <div id="adminThreadPhone" style="font-size:12px;color:#786d5e">08123456789</div>
                                    </div>
                                </div>
                                <div>
                                    <a id="adminThreadWaBtn" href="#" target="_blank" class="btn-ghost" style="font-size:12px;background:#22c55e;color:#fff;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
                                        <i class="fab fa-whatsapp"></i> Chat WhatsApp
                                    </a>
                                </div>
                            </div>

                            <!-- Messages Area -->
                            <div id="adminThreadMessages" style="flex:1;overflow-y:auto;padding:20px;background:#f9f6f0;display:flex;flex-direction:column;gap:12px;">
                                <!-- Messages injected dynamically -->
                            </div>

                            <!-- Quick Reply Buttons -->
                            <div style="padding:8px 16px;border-top:1px solid #e2d6c6;background:#f4ece1;display:flex;gap:8px;overflow-x:auto;">
                                <button type="button" onclick="adminQuickReply('Halo kak! Pesanan sedang kami proses dan segera disiapkan ya 👨‍🍳')" style="font-size:11px;padding:5px 12px;border-radius:8px;border:1px solid #d5c7b2;background:#fff;cursor:pointer;white-space:nowrap;font-weight:500">
                                    👨‍🍳 Pesanan Diproses
                                </button>
                                <button type="button" onclick="adminQuickReply('Untuk ongkir kurir flat Rp 1.000 saja kak untuk area perumahan 🛵')" style="font-size:11px;padding:5px 12px;border-radius:8px;border:1px solid #d5c7b2;background:#fff;cursor:pointer;white-space:nowrap;font-weight:500">
                                    🛵 Info Ongkir
                                </button>
                                <button type="button" onclick="adminQuickReply('Toko kami buka setiap hari jam 08:00 - 21:00 kak ✨')" style="font-size:11px;padding:5px 12px;border-radius:8px;border:1px solid #d5c7b2;background:#fff;cursor:pointer;white-space:nowrap;font-weight:500">
                                    ⏰ Jam Buka
                                </button>
                            </div>

                            <!-- Input Area -->
                            <div style="padding:14px 20px;border-top:1px solid #d5c7b2;background:#fff;">
                                <form id="adminReplyForm" onsubmit="handleAdminSendReply(event)" style="display:flex;gap:10px;align-items:center;">
                                    <input type="text" id="adminReplyInput" placeholder="Ketik balasan untuk pelanggan..." required maxlength="1000" autocomplete="off" style="flex:1;padding:12px 16px;border:1px solid #d5c7b2;border-radius:10px;font-size:13px;background:#fdfbf7;outline:none;">
                                    <button type="submit" class="btn-primary" style="padding:12px 22px;">
                                        Kirim Balasan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
  let adminActiveChatKey = null;
  let adminLastThreadId = 0;
  let adminConversationsList = [];

  // === MOBILE SIDEBAR CONTROLS ===
  function toggleAdminSidebar(open) {
    const sidebar = document.querySelector('.adm-sidebar');
    const backdrop = document.getElementById('admSidebarBackdrop');
    if (!sidebar) return;
    if (open) {
      sidebar.classList.add('open');
      if (backdrop) backdrop.classList.add('active');
      document.body.style.overflow = 'hidden';
    } else {
      sidebar.classList.remove('open');
      if (backdrop) backdrop.classList.remove('active');
      document.body.style.overflow = '';
    }
  }

  // === AUDIO CHIME (WEB AUDIO API, ZERO NETWORK LATENCY) ===
  function playOrderChime() {
    try {
      const AudioCtx = window.AudioContext || window.webkitAudioContext;
      if (!AudioCtx) return;
      const ctx = new AudioCtx();
      const now = ctx.currentTime;
      
      const osc1 = ctx.createOscillator();
      const gain1 = ctx.createGain();
      osc1.type = 'sine';
      osc1.frequency.setValueAtTime(659.25, now);
      gain1.gain.setValueAtTime(0.2, now);
      gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
      osc1.connect(gain1);
      gain1.connect(ctx.destination);
      osc1.start(now);
      osc1.stop(now + 0.3);

      const osc2 = ctx.createOscillator();
      const gain2 = ctx.createGain();
      osc2.type = 'sine';
      osc2.frequency.setValueAtTime(880.0, now + 0.12);
      gain2.gain.setValueAtTime(0.25, now + 0.12);
      gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.55);
      osc2.connect(gain2);
      gain2.connect(ctx.destination);
      osc2.start(now + 0.12);
      osc2.stop(now + 0.55);
    } catch(e) {}
  }

  // === REALTIME TOAST NOTIFICATION ===
  function showAdminOrderToast(order) {
    let container = document.getElementById('admToastContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'admToastContainer';
      container.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px;pointer-events:none;';
      document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.style.cssText = 'pointer-events:auto;background:#1c1917;color:#fff;padding:14px 18px;border-radius:14px;box-shadow:0 10px 25px rgba(0,0,0,0.35);border:1px solid rgba(255,255,255,0.15);display:flex;align-items:center;gap:14px;min-width:280px;max-width:380px;animation:admToastSlide 0.35s ease;';
    toast.innerHTML = `
      <div style="width:38px;height:38px;border-radius:50%;background:#e50027;color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">
        🔔
      </div>
      <div style="flex:1;min-width:0;">
        <div style="font-weight:700;font-size:13px;color:#fff;display:flex;justify-content:space-between">
          <span>Pesanan Baru!</span>
          <span style="color:#fdba74">#${order.order_code}</span>
        </div>
        <div style="font-size:12px;color:#d6d3d1;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
          ${order.customer_name} • Rp ${Number(order.total).toLocaleString('id-ID')}
        </div>
      </div>
      <a href="{{ route('admin.dashboard') }}?section=orders" style="background:#e50027;color:#fff;text-decoration:none;padding:6px 12px;border-radius:8px;font-size:11px;font-weight:700;white-space:nowrap;">Lihat</a>
    `;
    container.appendChild(toast);
    setTimeout(() => {
      toast.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(15px)';
      setTimeout(() => toast.remove(), 400);
    }, 7000);
  }

  // === REALTIME ACTION TOAST (MENU, VISIBILITY, ACTIONS) ===
  function showRealtimeActionToast(message, type = 'success') {
    let container = document.getElementById('admToastContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'admToastContainer';
      container.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px;pointer-events:none;';
      document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    const isSuccess = (type === 'success');
    const icon = isSuccess ? '👁️' : '🙈';
    const borderColor = isSuccess ? 'rgba(34, 197, 94, 0.4)' : 'rgba(234, 179, 8, 0.4)';
    const iconBg = isSuccess ? '#15803d' : '#b45309';

    toast.style.cssText = `pointer-events:auto;background:#1c1917;color:#fff;padding:12px 18px;border-radius:14px;box-shadow:0 10px 25px rgba(0,0,0,0.35);border:1px solid ${borderColor};display:flex;align-items:center;gap:12px;min-width:280px;max-width:380px;animation:admToastSlide 0.35s ease;`;
    toast.innerHTML = `
      <div style="width:34px;height:34px;border-radius:50%;background:${iconBg};color:#fff;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">
        ${icon}
      </div>
      <div style="flex:1;min-width:0;">
        <div style="font-weight:700;font-size:12px;color:#fff;">${isSuccess ? 'Status Etalase Aktif' : 'Status Etalase Disembunyikan'}</div>
        <div style="font-size:12px;color:#d6d3d1;margin-top:2px;line-height:1.3;">
          ${escapeAdminHtml(message)}
        </div>
      </div>
    `;
    container.appendChild(toast);
    setTimeout(() => {
      toast.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(12px)';
      setTimeout(() => toast.remove(), 350);
    }, 3500);
  }

  // === REALTIME MENU VISIBILITY TOGGLE (ZERO RELOAD, ZERO SCROLL) ===
  async function toggleMenuVisibilityRealtime(event, form, itemId) {
    if (event) {
      event.preventDefault();
      event.stopPropagation();
    }

    const btn = form.querySelector('button[type="submit"]');
    const originalHtml = btn ? btn.innerHTML : '';
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    }

    const row = document.getElementById(`menu-row-${itemId}`);
    const badge = document.getElementById(`menu-status-badge-${itemId}`);
    const nameEl = document.getElementById(`menu-name-text-${itemId}`);

    try {
      const url = form.action;
      const csrf = form.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        body: new URLSearchParams({
          '_token': csrf,
          '_method': 'PATCH'
        })
      });

      if (res.ok) {
        const data = await res.json();
        if (data.success) {
          const isVisible = data.is_visible;

          // 1. Update row appearance smoothly
          if (row) {
            row.style.transition = 'opacity 0.3s ease, background 0.3s ease';
            if (!isVisible) {
              row.style.opacity = '0.62';
              row.style.background = '#fafafa';
            } else {
              row.style.opacity = '1';
              row.style.background = '';
            }
          }

          // 2. Update name text
          if (nameEl) {
            nameEl.style.color = isVisible ? '#1c1917' : '#64748b';
          }

          // 3. Update status badge
          if (badge) {
            badge.style.transition = 'all 0.25s ease';
            if (isVisible) {
              badge.style.background = '#ecfdf5';
              badge.style.color = '#047857';
              badge.style.borderColor = '#a7f3d0';
              badge.innerHTML = '<span style="width:6px;height:6px;border-radius:50%;background:#10b981;display:inline-block"></span> Tampil';
            } else {
              badge.style.background = '#fef2f2';
              badge.style.color = '#b91c1c';
              badge.style.borderColor = '#fecaca';
              badge.innerHTML = '<span style="width:6px;height:6px;border-radius:50%;background:#ef4444;display:inline-block"></span> Tersembunyi';
            }
          }

          // 4. Update the toggle button itself
          if (btn) {
            if (isVisible) {
              btn.style.color = '#d97706';
              btn.style.borderColor = '#fcd34d';
              btn.style.background = '#fffbeb';
              btn.title = 'Sembunyikan menu dari etalase pembeli';
              btn.innerHTML = '<i class="fas fa-eye-slash"></i> Sembunyikan';
            } else {
              btn.style.color = '#047857';
              btn.style.borderColor = '#a7f3d0';
              btn.style.background = '#ecfdf5';
              btn.title = 'Tampilkan menu di etalase pembeli';
              btn.innerHTML = '<i class="fas fa-eye"></i> Tampilkan';
            }
          }

          // 5. Update edit form checkbox if present
          const editCheckbox = document.querySelector(`#edit-menu-${itemId} input[name="is_visible"]`);
          if (editCheckbox) {
            editCheckbox.checked = isVisible;
          }

          // 6. Show clean toast notification (NO RELOAD, NO SCROLL)
          showRealtimeActionToast(data.message || (isVisible ? 'Menu berhasil ditampilkan di etalase.' : 'Menu berhasil disembunyikan dari etalase.'), isVisible ? 'success' : 'warning');
        }
      } else {
        if (btn) btn.innerHTML = originalHtml;
      }
    } catch(err) {
      console.error('Toggle visibility error:', err);
      if (btn) btn.innerHTML = originalHtml;
    } finally {
      if (btn) btn.disabled = false;
    }
  }

  // === REALTIME POLLING FOR ORDERS & METRICS ===
  let adminLastOrderId = {{ $orders->max('id') ?? 0 }};
  let adminIsFirstPoll = true;

  async function pollAdminRealtimeOrders() {
    try {
      const url = `{{ route('admin.realtime.poll') }}?last_order_id=${adminLastOrderId}`;
      const res = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      if (!res.ok) return;
      const data = await res.json();
      if (!data.success) return;

      if (!adminIsFirstPoll && data.has_new_order && data.orders && data.orders.length > 0) {
        playOrderChime();
        data.orders.forEach(order => {
          showAdminOrderToast(order);
        });
        
        const card = document.querySelector('.adm-card');
        if (card && '{{ $navSection }}' === 'orders' && !document.getElementById('admNewOrdersNotice')) {
          const b = document.createElement('div');
          b.id = 'admNewOrdersNotice';
          b.style.cssText = 'background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:12px 16px;border-radius:10px;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between;font-size:13px;font-weight:600;';
          b.innerHTML = `<span>🔔 Ada pesanan baru masuk!</span> <a href="{{ route('admin.dashboard') }}?section=orders" style="background:#e50027;color:#fff;padding:5px 12px;border-radius:6px;text-decoration:none;font-size:12px">Muat Ulang Pesanan</a>`;
          card.insertBefore(b, card.firstChild);
        }
      }

      if (data.last_order_id > adminLastOrderId) {
        adminLastOrderId = data.last_order_id;
      }
      adminIsFirstPoll = false;

      // Update counters
      if (data.counts) {
        const totalEl = document.getElementById('metric-total-orders');
        if (totalEl) totalEl.innerText = data.counts.total;

        const waitEl = document.getElementById('metric-waiting-orders');
        if (waitEl) {
          if (parseInt(waitEl.innerText) !== data.counts.waiting) {
            waitEl.classList.add('adm-metric-pulse');
            setTimeout(() => waitEl.classList.remove('adm-metric-pulse'), 600);
          }
          waitEl.innerText = data.counts.waiting;
        }

        const compEl = document.getElementById('metric-completed-orders');
        if (compEl) compEl.innerText = data.counts.completed;

        const courEl = document.getElementById('metric-courier-count');
        if (courEl) courEl.innerText = data.counts.couriers;

        const revEl = document.getElementById('metric-revenue');
        if (revEl) revEl.innerText = data.counts.formatted_revenue;

        const sideWaitBadge = document.getElementById('sidebar-waiting-badge');
        if (sideWaitBadge) {
          if (data.counts.waiting > 0) {
            sideWaitBadge.innerText = data.counts.waiting;
            sideWaitBadge.style.display = 'inline-block';
          } else {
            sideWaitBadge.style.display = 'none';
          }
        }

        const hambDot = document.getElementById('admHamburgerDot');
        if (hambDot) {
          hambDot.style.display = data.counts.waiting > 0 ? '' : 'none';
        }
      }

      // Update chat badge
      if (typeof data.chat_unread !== 'undefined') {
        const badge = document.getElementById('admin-chat-unread-badge');
        if (badge) {
          if (data.chat_unread > 0) {
            badge.innerText = data.chat_unread;
            badge.style.display = 'inline-block';
          } else {
            badge.style.display = 'none';
          }
        }
        const totalBdg = document.getElementById('adminChatTotalBadge');
        if (totalBdg) totalBdg.innerText = data.chat_unread;
      }
    } catch(e) {}
  }

  async function fetchAdminChatBadge() {
    try {
      const res = await fetch("{{ route('admin.chat.conversations') }}", {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      if (!res.ok) return;
      const data = await res.json();
      if (data.success) {
        const badge = document.getElementById('admin-chat-unread-badge');
        if (badge) {
          if (data.total_unread > 0) {
            badge.innerText = data.total_unread;
            badge.style.display = 'inline-block';
          } else {
            badge.style.display = 'none';
          }
        }
        const totalBdg = document.getElementById('adminChatTotalBadge');
        if (totalBdg) totalBdg.innerText = data.total_unread;
      }
    } catch(e) {}
  }

  @if($navSection === 'chat')
  async function loadAdminConversations() {
    try {
      const res = await fetch("{{ route('admin.chat.conversations') }}", {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      if (!res.ok) return;
      const data = await res.json();
      if (!data.success) return;

      adminConversationsList = data.conversations || [];
      renderAdminConversations(adminConversationsList);

      const totalBdg = document.getElementById('adminChatTotalBadge');
      if (totalBdg) totalBdg.innerText = data.total_unread;
      const navBadge = document.getElementById('admin-chat-unread-badge');
      if (navBadge) {
        if (data.total_unread > 0) {
          navBadge.innerText = data.total_unread;
          navBadge.style.display = 'inline-block';
        } else {
          navBadge.style.display = 'none';
        }
      }
    } catch(e) {}
  }

  function renderAdminConversations(list) {
    const container = document.getElementById('adminConversationList');
    if (!container) return;

    if (!list || list.length === 0) {
      container.innerHTML = `
        <div style="text-align:center;padding:40px 20px;color:#786d5e;font-size:13px;">
          Belum ada percakapan pelanggan.
        </div>
      `;
      return;
    }

    container.innerHTML = list.map(c => {
      const isActive = (c.key === adminActiveChatKey);
      const bg = isActive ? '#f4ece1' : '#ede3d6';
      const border = isActive ? '2px solid #e50027' : '1px solid #d5c7b2';
      const initial = (c.name ? c.name.charAt(0).toUpperCase() : 'U');

      return `
        <div onclick="selectAdminConversation('${c.key}')" style="background:${bg};border:${border};border-radius:12px;padding:12px;margin-bottom:8px;cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:12px;">
          <div style="width:40px;height:40px;border-radius:50%;background:#e5dbcb;display:flex;align-items:center;justify-content:center;font-weight:700;color:#e50027;font-size:16px;flex-shrink:0;overflow:hidden">
            ${c.avatar ? `<img src="${c.avatar}" style="width:100%;height:100%;object-fit:cover">` : initial}
          </div>
          <div style="flex:1;min-width:0;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2px">
              <span style="font-weight:700;font-size:13px;color:#1c1917;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${escapeAdminHtml(c.name)}</span>
              <span style="font-size:10px;color:#786d5e">${c.last_time}</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center">
              <span style="font-size:12px;color:#786d5e;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px">${escapeAdminHtml(c.last_message)}</span>
              ${c.unread_count > 0 ? `<span style="background:#e50027;color:#fff;font-size:10px;font-weight:700;padding:1px 6px;border-radius:99px">${c.unread_count}</span>` : ''}
            </div>
          </div>
        </div>
      `;
    }).join('');
  }

  function filterAdminConversations() {
    const q = (document.getElementById('adminSearchChat')?.value || '').toLowerCase();
    if (!q) {
      renderAdminConversations(adminConversationsList);
      return;
    }
    const filtered = adminConversationsList.filter(c => {
      return (c.name && c.name.toLowerCase().includes(q)) || (c.phone && c.phone.includes(q)) || (c.last_message && c.last_message.toLowerCase().includes(q));
    });
    renderAdminConversations(filtered);
  }

  function adminCloseMobileChat() {
    const listCol = document.getElementById('adminChatListCol');
    const threadCol = document.getElementById('adminThreadContainer');
    const backBtn = document.getElementById('adminChatBackBtn');
    if (window.innerWidth <= 991) {
      if (listCol) listCol.style.display = 'flex';
      if (threadCol) threadCol.style.display = 'none';
      if (backBtn) backBtn.style.display = 'none';
    }
  }

  function selectAdminConversation(key) {
    adminActiveChatKey = key;
    adminLastThreadId = 0;

    const conv = adminConversationsList.find(c => c.key === key);
    if (conv) {
      document.getElementById('adminNoChatSelected').style.display = 'none';
      const box = document.getElementById('adminActiveChatBox');
      box.style.display = 'flex';

      // Mobile column switch
      if (window.innerWidth <= 991) {
        const listCol = document.getElementById('adminChatListCol');
        const threadCol = document.getElementById('adminThreadContainer');
        const backBtn = document.getElementById('adminChatBackBtn');
        if (listCol) listCol.style.display = 'none';
        if (threadCol) threadCol.style.display = 'flex';
        if (backBtn) backBtn.style.display = 'inline-flex';
      }

      document.getElementById('adminThreadName').innerText = conv.name;
      document.getElementById('adminThreadBadge').innerText = conv.is_customer ? 'Member' : 'Tamu';
      document.getElementById('adminThreadPhone').innerText = conv.phone && conv.phone !== '-' ? conv.phone : 'Belum isi no HP';

      const waBtn = document.getElementById('adminThreadWaBtn');
      if (conv.phone && conv.phone !== '-') {
        const cleanPhone = conv.phone.replace(/[^0-9]/g, '');
        waBtn.href = `https://wa.me/${cleanPhone}`;
        waBtn.style.display = 'inline-flex';
      } else {
        waBtn.style.display = 'none';
      }

      const avEl = document.getElementById('adminThreadAvatar');
      if (conv.avatar) {
        avEl.innerHTML = `<img src="${conv.avatar}" style="width:100%;height:100%;object-fit:cover">`;
      } else {
        avEl.innerHTML = conv.name.charAt(0).toUpperCase();
      }

      renderAdminConversations(adminConversationsList);
      loadAdminThread(true);

      setTimeout(() => {
        document.getElementById('adminReplyInput')?.focus();
      }, 100);
    }
  }

  async function loadAdminThread(isFirst = false) {
    if (!adminActiveChatKey) return;
    try {
      const url = `{{ url('/admin/chat/thread') }}/${adminActiveChatKey}?last_id=${adminLastThreadId}`;
      const res = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      if (!res.ok) return;
      const data = await res.json();
      if (!data.success) return;

      const container = document.getElementById('adminThreadMessages');
      if (isFirst) {
        container.innerHTML = '';
      }

      if (data.messages && data.messages.length > 0) {
        data.messages.forEach(msg => {
          if (msg.id > adminLastThreadId) {
            adminLastThreadId = msg.id;
          }
          appendAdminThreadBubble(msg);
        });
        container.scrollTop = container.scrollHeight;
      }
    } catch(e) {}
  }

  function appendAdminThreadBubble(msg) {
    const container = document.getElementById('adminThreadMessages');
    if (!container || document.getElementById(`adm-msg-${msg.id}`)) return;

    const isAdmin = (msg.sender_type === 'admin');
    const bubble = document.createElement('div');
    bubble.id = `adm-msg-${msg.id}`;
    bubble.style.display = 'flex';
    bubble.style.justifyContent = isAdmin ? 'flex-end' : 'flex-start';

    if (isAdmin) {
      bubble.innerHTML = `
        <div style="max-width:420px;text-align:right">
          <div style="background:#e50027;color:#fff;padding:12px 16px;border-radius:16px 16px 4px 16px;font-size:13px;line-height:1.45;text-align:left;box-shadow:0 2px 4px rgba(229,0,39,0.15)">
            ${escapeAdminHtml(msg.message)}
          </div>
          <span style="font-size:10px;color:#786d5e;margin-top:4px;display:block;padding-right:4px">${msg.time} • Anda</span>
        </div>
      `;
    } else {
      bubble.innerHTML = `
        <div style="max-width:420px">
          <div style="background:#fff;color:#1c1917;padding:12px 16px;border-radius:16px 16px 16px 4px;font-size:13px;line-height:1.45;border:1px solid #d5c7b2;box-shadow:0 1px 3px rgba(40,20,10,0.04)">
            ${escapeAdminHtml(msg.message)}
          </div>
          <span style="font-size:10px;color:#786d5e;margin-top:4px;display:block;padding-left:4px">${escapeAdminHtml(msg.sender_name)} • ${msg.time}</span>
        </div>
      `;
    }

    container.appendChild(bubble);
  }

  function adminQuickReply(text) {
    const input = document.getElementById('adminReplyInput');
    if (input) {
      input.value = text;
      input.focus();
    }
  }

  async function handleAdminSendReply(e) {
    if (e && e.preventDefault) e.preventDefault();
    if (!adminActiveChatKey) return;
    const input = document.getElementById('adminReplyInput');
    if (!input || !input.value.trim()) return;

    const msgText = input.value.trim();
    input.value = '';

    const tempId = 'temp-' + Date.now();
    const nowTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    appendAdminThreadBubble({
      id: tempId,
      sender_type: 'admin',
      sender_name: 'Admin 3GFood',
      message: msgText,
      time: nowTime
    });
    const container = document.getElementById('adminThreadMessages');
    if (container) container.scrollTop = container.scrollHeight;

    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch("{{ route('admin.chat.reply') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          conversation_key: adminActiveChatKey,
          message: msgText
        })
      });

      if (res.ok) {
        const data = await res.json();
        if (data.success && data.message) {
          const tempEl = document.getElementById(`adm-msg-${tempId}`);
          if (tempEl) tempEl.id = `adm-msg-${data.message.id}`;
          if (data.message.id > adminLastThreadId) {
            adminLastThreadId = data.message.id;
          }
          loadAdminConversations();
        }
      }
    } catch(e) {}
  }
  @endif

  function escapeAdminHtml(str) {
    return (str || '').replace(/[&<>"']/g, function(m) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    pollAdminRealtimeOrders();
    setInterval(pollAdminRealtimeOrders, 3500);

    @if($navSection === 'chat')
    loadAdminConversations();
    setInterval(loadAdminConversations, 3500);
    setInterval(function() {
      if (adminActiveChatKey) {
        loadAdminThread(false);
      }
    }, 3000);
    @endif
  });
</script>
</body>
