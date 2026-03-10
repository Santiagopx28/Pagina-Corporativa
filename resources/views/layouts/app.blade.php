<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CCV - @yield('title', 'Portal Corporativo')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --ccv-primary: #1e3a5f;
            --ccv-secondary: #2d6a9f;
            --ccv-accent: #f0a500;
            --ccv-light: #f4f7fa;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: var(--ccv-light);
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
        }

        /* ══════════════════════════════
           DESKTOP — Sidebar fijo lateral
        ══════════════════════════════ */
        .sidebar {
            background: var(--ccv-primary);
            width: 260px;
            min-width: 260px;
            min-height: 100vh;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-link {
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .55rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            transition: all .2s;
            font-size: .9rem;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: var(--ccv-secondary);
            color: #fff;
        }

        .topbar {
            background: #fff;
            border-bottom: 3px solid var(--ccv-accent);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        /* ══════════════════════════════
           MÓDULOS DESPLEGABLES
        ══════════════════════════════ */
        .modulo-header {
            cursor: pointer;
            user-select: none;
            background: var(--ccv-primary);
            color: #fff;
            border-radius: 8px;
            padding: .85rem 1.1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background .2s;
        }

        .modulo-header:hover {
            background: var(--ccv-secondary);
        }

        .modulo-body {
            display: none;
        }

        .modulo-body.open {
            display: block;
            animation: fadeIn .25s ease;
        }

        .modulo-arrow {
            transition: transform .3s;
            flex-shrink: 0;
        }

        .modulo-header.open .modulo-arrow {
            transform: rotate(180deg);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .card-doc {
            border-left: 4px solid var(--ccv-accent);
            transition: transform .2s, box-shadow .2s;
        }

        .card-doc:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, .1);
        }

        /* ══════════════════════════════
           MÓVIL — Menú inferior fijo
        ══════════════════════════════ */
        .mobile-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--ccv-primary);
            z-index: 1000;
            border-top: 2px solid var(--ccv-accent);
            padding: 6px 0 8px;
        }

        .mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            text-decoration: none;
            font-size: .65rem;
            gap: 3px;
            flex: 1;
            padding: 4px 2px;
            border-radius: 6px;
            transition: color .2s;
        }

        .mobile-nav-item .icon {
            font-size: 1.3rem;
            line-height: 1;
        }

        .mobile-nav-item.active,
        .mobile-nav-item:hover {
            color: var(--ccv-accent);
        }

        /* Panel lateral móvil (drawer) */
        .mobile-drawer-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .5);
            z-index: 1050;
        }

        .mobile-drawer-overlay.open {
            display: block;
        }

        .mobile-drawer {
            position: fixed;
            top: 0;
            left: -280px;
            width: 280px;
            height: 100vh;
            background: var(--ccv-primary);
            z-index: 1060;
            transition: left .3s ease;
            overflow-y: auto;
            padding: 1rem;
        }

        .mobile-drawer.open {
            left: 0;
        }

        /* Topbar móvil */
        .mobile-topbar {
            display: none;
            background: var(--ccv-primary);
            color: white;
            padding: .75rem 1rem;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 3px solid var(--ccv-accent);
        }

        /* Ajuste contenido en móvil */
        @media (max-width: 768px) {
            .sidebar {
                display: none !important;
            }

            .desktop-topbar {
                display: none !important;
            }

            .mobile-topbar {
                display: flex !important;
            }

            .mobile-nav {
                display: flex !important;
            }

            .main-content {
                padding-bottom: 75px !important;
            }

            /* Tablas responsivas en móvil */
            .table-responsive table thead {
                display: none;
            }

            .table-responsive table tr {
                display: block;
                margin-bottom: .75rem;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                padding: .5rem;
                background: white;
            }

            .table-responsive table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: .4rem .5rem !important;
                border: none !important;
                font-size: .85rem;
            }

            .table-responsive table td:before {
                content: attr(data-label);
                font-weight: 600;
                color: var(--ccv-primary);
                font-size: .75rem;
                flex-shrink: 0;
                margin-right: .5rem;
            }

            /* Cards en móvil */
            .card-doc .d-flex {
                flex-wrap: wrap;
            }
        }
    </style>
</head>

<body>

    {{-- ═══════════════════════════════════════
     MÓVIL: Topbar superior
═══════════════════════════════════════ --}}
    <header class="mobile-topbar">
        <div class="d-flex align-items-center gap-2">
            <button onclick="openDrawer()" class="btn btn-link p-0 text-white" style="font-size:1.4rem;">☰</button>
            <div>
                <span class="fw-bold" style="font-size:.95rem;">🏛️ CCV</span>
                <span class="text-warning ms-1" style="font-size:.75rem;">Portal</span>
            </div>
        </div>
        <a href="{{ route('portal.buscar') }}" class="btn btn-sm"
            style="background:var(--ccv-accent);color:#333;font-weight:600;font-size:.8rem;">
            🔍
        </a>
    </header>

    {{-- ═══════════════════════════════════════
     MÓVIL: Drawer lateral
═══════════════════════════════════════ --}}
    <div class="mobile-drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>
    <div class="mobile-drawer" id="mobileDrawer">
        <div class="position-relative text-center mb-4 pb-3">
            <button onclick="closeDrawer()"
                class="btn btn-link text-danger p-0 d-flex align-items-center justify-content-center ms-auto">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
            <div class="text-center mb-4 pb-3 border-bottom border-secondary d-flex flex-column align-items-center justify-content-center"
                style="min-height: 150px;">
                {{-- Contenedor del Logo --}}
                <div class="mb-2 w-100">
                    <img src="{{ asset('img/icon.png') }}" alt="Logo"
                        style="width: 70px; height: auto; filter: drop-shadow(0px 4px 8px rgba(0,0,0,0.3));">
                </div>

                {{-- Texto Principal --}}
                <h4 class="text-white fw-bold mb-0" style="letter-spacing: 0.5px; font-size: 1.5rem;">
                    Portal
                </h4>

                {{-- Subtexto Presidencial --}}
                <small class="text-secondary"
                    style="
        text-transform: uppercase; 
        letter-spacing: 3px; 
        font-size: 0.85rem; 
        font-weight: 700; 
        display: block;
        margin-top: -2px;
        opacity: 0.9;">
                    Presidencial
                </small>
            </div>
        </div>

        <ul class="list-unstyled">
            <li class="mb-1">
                <a href="{{ route('portal.index') }}"
                    class="sidebar-link {{ request()->routeIs('portal.index') ? 'active' : '' }}">
                    🏠 Inicio
                </a>
            </li>
            <li class="mb-1">
                <a href="{{ route('portal.buscar') }}" class="sidebar-link">
                    🔍 Buscar documentos
                </a>
            </li>

            <li class="mt-3 mb-1">
                <small class="text-secondary px-2" style="font-size:.7rem;letter-spacing:.05em;">CATEGORÍAS</small>
            </li>
            @foreach ($sidebarCategorias ?? [] as $cat)
                <li class="mb-1">
                    <a href="{{ route('portal.categoria', $cat->slug) }}" class="sidebar-link">
                        📁 {{ $cat->nombre }}
                        <span class="badge ms-auto" style="background:var(--ccv-accent);color:#333;font-size:.65rem;">
                            {{ $cat->documentos_activos_count }}
                        </span>
                    </a>
                </li>
            @endforeach

            @auth
                <li class="mt-3 mb-1">
                    <small class="text-secondary px-2" style="font-size:.7rem;letter-spacing:.05em;">ADMINISTRACIÓN</small>
                </li>
                <li class="mb-1">
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                        📊 <span>Dashboard</span>
                    </a>
                </li>
                <li class="mb-1">
                    <a href="{{ route('admin.documentos.index') }}" class="sidebar-link">⚙️ <span>Gestionar docs</span></a>
                </li>
                <li class="mb-1">
                    <a href="{{ route('admin.documentos.create') }}" class="sidebar-link">➕ <span>Subir
                            documentos</span></a>
                </li>
                <li class="mb-1">
                    <a href="{{ route('admin.subcategorias.index') }}" class="sidebar-link">📅 <span>Gestionar
                            años</span></a>
                </li>
                <li class="mb-1">
                    <a href="{{ route('admin.meses.index') }}" class="sidebar-link">📆 <span>Gestionar meses</span></a>
                </li>
                <li class="mb-1">
                    <a href="{{ route('admin.usuarios.index') }}" class="sidebar-link">👥 <span>Gestionar
                            usuarios</span></a>
                </li>
            @endauth
        </ul>

        <div class="border-top border-secondary pt-3 mt-3">
            <div class="text-white small mb-2">👤 {{ auth()->user()->name ?? '' }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm w-100">Cerrar sesión</button>
            </form>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
     LAYOUT PRINCIPAL
═══════════════════════════════════════ --}}
    <div class="d-flex" style="min-height:100vh;">

        {{-- SIDEBAR DESKTOP --}}
        <nav class="sidebar p-3 d-flex flex-column desktop-sidebar">
            <div class="text-center mb-4 pb-3 border-bottom border-secondary">
                <div class="mb-2">
                    <img src="{{ asset('img/icon.png') }}" alt="Logo" style="height: 50px; width: auto;">
                </div>
                <h6 class="text-white fw-bold mb-0">Portal</h6>
                <small class="text-secondary"
                    style="text-transform: uppercase; letter-apacing: 12px; font-size: 1rem;">
                    Presidencial
                </small>
            </div>

            <ul class="list-unstyled mb-auto">
                <li class="mb-1">
                    <a href="{{ route('portal.index') }}"
                        class="sidebar-link {{ request()->routeIs('portal.index') ? 'active' : '' }}">
                        🏠 <span>Inicio</span>
                    </a>
                </li>
                <li class="mb-1">
                    <a href="{{ route('portal.buscar') }}" class="sidebar-link">
                        🔍 <span>Buscar documentos</span>
                    </a>
                </li>
                <li class="mt-3 mb-1">
                    <small class="text-secondary px-2"
                        style="font-size:.7rem;letter-spacing:.05em;">CATEGORÍAS</small>
                </li>
                @foreach ($sidebarCategorias ?? [] as $cat)
                    <li class="mb-1">
                        <a href="{{ route('portal.categoria', $cat->slug) }}" class="sidebar-link">
                            📁 <span>{{ $cat->nombre }}</span>
                            <span class="badge ms-auto"
                                style="background:var(--ccv-accent);color:#333;font-size:.65rem;">
                                {{ $cat->documentos_activos_count }}
                            </span>
                        </a>
                    </li>
                @endforeach
                @auth
                    <li class="mt-3 mb-1">
                        <small class="text-secondary px-2"
                            style="font-size:.7rem;letter-spacing:.05em;">ADMINISTRACIÓN</small>
                    </li>
                    <li class="mb-1">
                        <a href="{{ route('admin.dashboard') }}" class="sidebar-link">📊 <span>Dashboard</span></a>
                    </li>
                    <li class="mb-1">
                        <a href="{{ route('admin.documentos.index') }}" class="sidebar-link">⚙️ <span>Gestionar
                                docs</span></a>
                    </li>
                    <li class="mb-1">
                        <a href="{{ route('admin.documentos.create') }}" class="sidebar-link">➕ <span>Subir
                                documentos</span></a>
                    </li>
                    <li class="mb-1">
                        <a href="{{ route('admin.subcategorias.index') }}" class="sidebar-link">📅 <span>Gestionar
                                años</span></a>
                    </li>
                    <li class="mb-1">
                        <a href="{{ route('admin.meses.index') }}" class="sidebar-link">📆 <span>Gestionar
                                meses</span></a>
                    </li>
                    <li class="mb-1">
                        <a href="{{ route('admin.usuarios.index') }}" class="sidebar-link">👥 <span>Gestionar
                                usuarios</span></a>
                    </li>
                @endauth
            </ul>

            <div class="border-top border-secondary pt-3 mt-3">
                <div class="text-white small mb-1">👤 {{ auth()->user()->name ?? '' }}</div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link p-0 text-secondary small">Cerrar sesión →</button>
                </form>
            </div>
        </nav>

        {{-- CONTENIDO PRINCIPAL --}}
        <div class="flex-grow-1 d-flex flex-column overflow-auto">

            {{-- Topbar Desktop --}}
            <header class="topbar desktop-topbar px-4 py-2 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 fw-bold" style="color:var(--ccv-primary);">@yield('title', 'Portal Corporativo')</h6>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0" style="font-size:.8rem;">@yield('breadcrumb')</ol>
                    </nav>
                </div>
                <a href="{{ route('portal.buscar') }}" class="btn btn-sm"
                    style="background:var(--ccv-accent);color:#333;font-weight:600;">
                    🔍 Buscar
                </a>
            </header>

            {{-- Título en móvil --}}
            <div class="d-md-none px-3 pt-3 pb-1">
                <h6 class="fw-bold mb-0" style="color:var(--ccv-primary);">@yield('title', 'Portal Corporativo')</h6>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
                    ✅ {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <main class="flex-grow-1 p-3 p-md-4 main-content">
                @yield('content')
            </main>

            <footer class="text-center py-2 text-muted border-top d-none d-md-block" style="font-size:.75rem;">
                © {{ date('Y') }} CCV — Portal de Información Corporativa
            </footer>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
     MÓVIL: Barra de navegación inferior
═══════════════════════════════════════ --}}
    <nav class="mobile-nav">
        <a href="{{ route('portal.index') }}"
            class="mobile-nav-item {{ request()->routeIs('portal.index') ? 'active' : '' }}">
            <span class="icon">🏠</span>
            <span>Inicio</span>
        </a>
        <a href="{{ route('portal.buscar') }}"
            class="mobile-nav-item {{ request()->routeIs('portal.buscar') ? 'active' : '' }}">
            <span class="icon">🔍</span>
            <span>Buscar</span>
        </a>
        <a href="{{ route('admin.documentos.create') }}"
            class="mobile-nav-item {{ request()->routeIs('admin.documentos.create') ? 'active' : '' }}">
            <span class="icon">➕</span>
            <span>Subir</span>
        </a>
        <a href="{{ route('admin.documentos.index') }}"
            class="mobile-nav-item {{ request()->routeIs('admin.documentos.*') ? 'active' : '' }}">
            <span class="icon">⚙️</span>
            <span>Admin</span>
        </a>
        <a href="#" onclick="openDrawer()" class="mobile-nav-item">
            <span class="icon">☰</span>
            <span>Menú</span>
        </a>
    </nav>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Drawer móvil
        function openDrawer() {
            document.getElementById('mobileDrawer').classList.add('open');
            document.getElementById('drawerOverlay').classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeDrawer() {
            document.getElementById('mobileDrawer').classList.remove('open');
            document.getElementById('drawerOverlay').classList.remove('open');
            document.body.style.overflow = '';
        }

        // Módulos desplegables
        document.querySelectorAll('.modulo-header').forEach(h => {
            h.addEventListener('click', () => {
                const body = h.nextElementSibling;
                const isOpen = body.classList.contains('open');
                document.querySelectorAll('.modulo-body').forEach(b => b.classList.remove('open'));
                document.querySelectorAll('.modulo-header').forEach(x => x.classList.remove('open'));
                if (!isOpen) {
                    body.classList.add('open');
                    h.classList.add('open');
                }
            });
        });
    </script>
    @stack('scripts')
    {{-- ═══════════════════════════════════════
     CHATBOT FLOTANTE
═══════════════════════════════════════ --}}
    <div id="chatbot-container">
        {{-- Botón flotante --}}
        <button id="chatbot-toggle" class="chatbot-btn" onclick="toggleChatbot()">
            <span id="chatbot-icon">💬</span>
            <span id="chatbot-close" style="display:none;">✕</span>
        </button>

        {{-- Ventana del chat --}}
        <div id="chatbot-window" class="chatbot-window" style="display:none;">
            <div class="chatbot-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="chatbot-avatar">🤖</div>
                    <div>
                        <div class="fw-bold">Asistente CAMARA</div>
                        <small class="text-white-50">Consulta documentos con IA</small>
                    </div>
                </div>
                <button onclick="toggleChatbot()" class="btn-close btn-close-white"></button>
            </div>

            <div id="chatbot-messages" class="chatbot-messages">
                <div class="chatbot-message bot-message">
                    <strong>🤖 CAMARA:</strong> ¡Hola! Soy tu asistente inteligente. Puedo ayudarte a buscar información
                    en los documentos del portal. ¿Qué necesitas saber?
                </div>
            </div>

            <form id="chatbot-form" class="chatbot-input-container">
                <input type="text" id="chatbot-input" class="chatbot-input" placeholder="Escribe tu pregunta..."
                    autocomplete="off">
                <button type="submit" class="chatbot-send-btn">
                    📤
                </button>
            </form>
        </div>
    </div>

    <style>
        /* Botón flotante */
        .chatbot-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--ccv-primary), var(--ccv-secondary));
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            cursor: pointer;
            z-index: 1100;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .3s;
        }

        .chatbot-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }

        /* Ventana del chat */
        .chatbot-window {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 380px;
            max-width: calc(100vw - 40px);
            height: 500px;
            max-height: calc(100vh - 120px);
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            z-index: 1100;
            display: flex;
            flex-direction: column;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header del chat */
        .chatbot-header {
            background: linear-gradient(135deg, var(--ccv-primary), var(--ccv-secondary));
            color: white;
            padding: 15px;
            border-radius: 15px 15px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chatbot-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        /* Mensajes */
        .chatbot-messages {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            background: #f8f9fa;
        }

        .chatbot-message {
            margin-bottom: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            line-height: 1.5;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .bot-message {
            background: white;
            border-left: 3px solid var(--ccv-primary);
        }

        .user-message {
            background: var(--ccv-primary);
            color: white;
            text-align: right;
            margin-left: 40px;
        }

        .typing-indicator {
            display: inline-block;
            padding: 10px 15px;
            background: white;
            border-radius: 10px;
            border-left: 3px solid var(--ccv-accent);
        }

        .typing-indicator span {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--ccv-accent);
            margin: 0 2px;
            animation: typing 1.4s infinite;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {

            0%,
            60%,
            100% {
                transform: translateY(0);
            }

            30% {
                transform: translateY(-10px);
            }
        }

        /* Input */
        .chatbot-input-container {
            display: flex;
            gap: 8px;
            padding: 12px;
            border-top: 1px solid #dee2e6;
            background: white;
            border-radius: 0 0 15px 15px;
        }

        .chatbot-input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #dee2e6;
            border-radius: 25px;
            outline: none;
            font-size: 0.9rem;
        }

        .chatbot-input:focus {
            border-color: var(--ccv-primary);
        }

        .chatbot-send-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--ccv-primary);
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all .2s;
        }

        .chatbot-send-btn:hover {
            background: var(--ccv-secondary);
            transform: scale(1.1);
        }

        /* Responsive móvil */
        @media (max-width: 768px) {
            .chatbot-btn {
                bottom: 80px;
            }

            .chatbot-window {
                bottom: 150px;
                right: 10px;
                left: 10px;
                width: auto;
                max-width: none;
            }
        }
    </style>

    <script>
        let chatbotOpen = false;

        function toggleChatbot() {
            chatbotOpen = !chatbotOpen;
            const win = document.getElementById('chatbot-window');
            const icon = document.getElementById('chatbot-icon');
            const close = document.getElementById('chatbot-close');

            if (chatbotOpen) {
                win.style.display = 'flex';
                icon.style.display = 'none';
                close.style.display = 'block';
                document.getElementById('chatbot-input').focus();
            } else {
                win.style.display = 'none';
                icon.style.display = 'block';
                close.style.display = 'none';
            }
        }

        function addMessage(text, sender) {
            const messagesDiv = document.getElementById('chatbot-messages');
            const messageDiv = document.createElement('div');
            messageDiv.className = 'chatbot-message ' + (sender === 'user' ? 'user-message' : 'bot-message');

            if (sender === 'bot') {
                messageDiv.innerHTML = '<strong>🤖 CAMARA:</strong> ' + text;
            } else {
                messageDiv.textContent = text;
            }

            messagesDiv.appendChild(messageDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        function showTyping() {
            const messagesDiv = document.getElementById('chatbot-messages');
            const typingDiv = document.createElement('div');
            typingDiv.className = 'typing-indicator';
            typingDiv.innerHTML = '<span></span><span></span><span></span>';
            typingDiv.id = 'typing-' + Date.now();
            messagesDiv.appendChild(typingDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
            return typingDiv.id;
        }

        function removeTyping(id) {
            const typing = document.getElementById(id);
            if (typing) typing.remove();
        }

        // Enviar mensaje
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('chatbot-form').addEventListener('submit', async function(e) {
                e.preventDefault();

                const input = document.getElementById('chatbot-input');
                const question = input.value.trim();
                if (!question) return;

                addMessage(question, 'user');
                input.value = '';
                const typingId = showTyping();

                try {
                    const response = await fetch('http://127.0.0.1:8001/api/chatbot/preguntar', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content
                        },
                        body: JSON.stringify({
                            question: question
                        })
                    });

                    const data = await response.json();
                    removeTyping(typingId);

                    if (!response.ok) {
                        addMessage('⚠️ Error del servidor (' + response.status + '): ' + (data.answer ||
                            data.message || 'Sin detalles'), 'bot');
                        return;
                    }

                    addMessage(data.answer || 'No recibí respuesta', 'bot');

                } catch (error) {
                    removeTyping(typingId);
                    addMessage('❌ Error: ' + error.message, 'bot');
                }
            });
        });
    </script>
</body>

</html>
