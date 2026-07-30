<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('install.title') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <style>
        .install-overlay {
            position: fixed; inset: 0; z-index: 90;
            background: rgba(15, 23, 42, 0.55);
            display: none; align-items: center; justify-content: center;
            padding: 1rem;
        }
        .install-overlay.is-active { display: flex; }
        .install-overlay__card {
            width: 100%; max-width: 22rem;
            background: #fff; border-radius: 1rem; padding: 1.25rem 1.35rem;
            box-shadow: 0 20px 50px rgba(0,0,0,.25);
        }
        .install-bar {
            height: 0.65rem; border-radius: 999px; background: #e2e8f0; overflow: hidden;
        }
        .install-bar > span {
            display: block; height: 100%; width: 0%;
            background: linear-gradient(90deg, #f59e0b, #ea580c);
            transition: width .25s ease;
        }
        .install-bar.is-indeterminate > span {
            width: 40% !important;
            animation: install-slide 1.1s ease-in-out infinite;
        }
        @keyframes install-slide {
            0% { transform: translateX(-120%); }
            100% { transform: translateX(280%); }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    @php
        $step = (int) ($step ?? 1);
        $totalSteps = (int) ($totalSteps ?? 7);
        $percent = (int) ($percent ?? round(($step / max(1, $totalSteps)) * 100));
    @endphp
    <div class="w-full max-w-xl bg-white rounded-xl shadow-lg p-8 relative">
        <h1 class="text-2xl font-bold text-slate-800 mb-2">{{ __('install.title') }}</h1>
        <div class="mb-2 flex items-center justify-between gap-3 text-sm">
            <p class="text-slate-500">{{ __('install.step_of', ['step' => $step, 'total' => $totalSteps]) }}</p>
            <p class="font-semibold text-amber-700 tabular-nums">{{ $percent }}%</p>
        </div>
        <div class="install-bar mb-2" aria-hidden="true">
            <span style="width: {{ $percent }}%"></span>
        </div>
        <div class="mb-6 flex gap-1" aria-hidden="true">
            @for($i = 1; $i <= $totalSteps; $i++)
                <span class="h-1.5 flex-1 rounded-full {{ $i <= $step ? 'bg-amber-500' : 'bg-slate-200' }}" title="{{ __('install.step_of', ['step' => $i, 'total' => $totalSteps]) }}"></span>
            @endfor
        </div>
        @if(session('error'))
            <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-800 text-sm">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <ul class="mb-4 list-disc list-inside text-red-600 text-sm">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        @endif
        @yield('content')
    </div>

    <div id="install-loading" class="install-overlay" aria-live="polite" aria-busy="true">
        <div class="install-overlay__card">
            <p id="install-loading-text" class="text-sm font-semibold text-slate-800 mb-2">{{ __('install.loading') }}</p>
            <p class="text-xs text-slate-500 mb-3"><span id="install-loading-pct">0</span>%</p>
            <div class="install-bar is-indeterminate" id="install-loading-bar"><span></span></div>
        </div>
    </div>

    <script>
        (function () {
            const overlay = document.getElementById('install-loading');
            const pctEl = document.getElementById('install-loading-pct');
            const bar = document.getElementById('install-loading-bar');
            const textEl = document.getElementById('install-loading-text');
            let timer = null;

            function showLoading(label) {
                if (!overlay) return;
                if (label && textEl) textEl.textContent = label;
                overlay.classList.add('is-active');
                let p = 8;
                pctEl.textContent = String(p);
                bar.classList.add('is-indeterminate');
                clearInterval(timer);
                timer = setInterval(function () {
                    p = Math.min(92, p + Math.floor(Math.random() * 7) + 2);
                    pctEl.textContent = String(p);
                }, 450);
            }

            document.querySelectorAll('form[data-loading]').forEach(function (form) {
                form.addEventListener('submit', function () {
                    showLoading(form.getAttribute('data-loading') || @json(__('install.loading')));
                });
            });
            document.querySelectorAll('a[data-loading]').forEach(function (link) {
                link.addEventListener('click', function () {
                    showLoading(link.getAttribute('data-loading') || @json(__('install.loading')));
                });
            });
            document.querySelectorAll('button[data-loading][form], button[data-loading][type="submit"]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    if (btn.disabled) return;
                    showLoading(btn.getAttribute('data-loading') || @json(__('install.loading')));
                });
            });
        })();
    </script>

@include('partials.cms-toasts')
</body>
</html>
