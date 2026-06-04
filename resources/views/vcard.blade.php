@php
    // Resolve QR asset from Statamic Value object
    $qrField = config('vcard.fields.qr_code', 'qr_code');
    $qrRaw   = $$qrField ?? null;
    $qrAsset = null;

    if ($qrRaw) {
        $raw = is_object($qrRaw) && method_exists($qrRaw, 'raw') ? $qrRaw->raw() : $qrRaw;
        if ($raw && str_contains($raw, '::')) {
            [$containerHandle, $path] = explode('::', $raw, 2);
            $qrAsset = \Statamic\Facades\Asset::findById("{$containerHandle}::{$path}");
        }
    }

    // Brand config
    $brand      = config('vcard.brand', []);
    $colorBg     = $brand['color_bg']     ?? '#b3d4f5';
    $colorDark   = $brand['color_dark']   ?? '#1a237e';
    $colorBright = $brand['color_bright'] ?? '#1e90ff';
    $colorMid    = $brand['color_mid']    ?? '#1565c0';
    $brandName   = $brand['name']         ?? '';
    $brandTagline = $brand['tagline']     ?? '';

    // Field map
    $fields      = config('vcard.fields', []);
    $f           = fn(string $key) => $$fields[$key] ?? null;

    $vcfRoute    = '/' . config('vcard.route_prefix', 'vcards') . '/' . $slug . '.vcf';
@endphp
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} — {{ $brandName }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --vc-bg:     {{ $colorBg }};
            --vc-dark:   {{ $colorDark }};
            --vc-bright: {{ $colorBright }};
            --vc-mid:    {{ $colorMid }};
        }

        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            min-height: 100dvh;
            background-color: var(--vc-bg);
            font-family: 'Barlow', sans-serif;
        }

        .vcard-page {
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .vcard {
            background: #fff;
            border-radius: 18px;
            width: 100%;
            max-width: 400px;
            padding: 2.25rem 2rem 2rem;
            box-shadow: 0 8px 32px rgba(30, 60, 120, 0.13);
        }

        .vcard__logo-wrap {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            margin-bottom: 2rem;
        }

        .vcard__logo-text {
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 800;
            font-size: 2.4rem;
            letter-spacing: 0.04em;
            color: var(--vc-bright);
            text-transform: uppercase;
            line-height: 1;
        }

        .logo-strike {
            position: relative;
            display: inline-block;
        }
        .logo-strike::after {
            content: '';
            position: absolute;
            left: 0; right: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 3px;
            background: #111;
            border-radius: 1px;
        }

        .vcard__logo-tagline {
            font-size: 0.78rem;
            font-weight: 500;
            line-height: 1.35;
            color: #1a1a2e;
        }

        .vcard__name {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--vc-dark);
            margin: 0 0 0.3rem;
        }

        .vcard__role {
            font-size: 1rem;
            font-weight: 500;
            color: var(--vc-bright);
            margin: 0 0 1.5rem;
        }

        .vcard__contacts {
            list-style: none;
            padding: 0;
            margin: 0 0 2rem;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .vcard__contact-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .vcard__icon {
            flex-shrink: 0;
            width: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--vc-dark);
            font-size: 1.15rem;
            padding-top: 0.1rem;
        }

        .vcard__icon--mobile .bi {
            border: 2px solid var(--vc-dark);
            border-radius: 4px;
            padding: 1px 4px;
            font-size: 1rem;
            line-height: 1.3;
        }

        .vcard__contact-detail {
            font-size: 0.975rem;
            color: #1a1a2e;
            line-height: 1.45;
            word-break: break-word;
        }

        .vcard__contact-detail a { color: inherit; text-decoration: none; }
        .vcard__contact-detail a:hover { color: var(--vc-mid); text-decoration: underline; }

        .vcard__actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
        }

        .vcard__btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: var(--vc-dark);
            color: #fff;
            font-family: 'Barlow', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            padding: 0.75rem 1.25rem;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.18s ease, transform 0.12s ease;
            flex: 1;
            max-width: 180px;
        }
        .vcard__btn:hover { background: var(--vc-mid); color: #fff; transform: translateY(-1px); }

        .vcard__btn--outline {
            background: transparent;
            border: 2px solid var(--vc-dark);
            color: var(--vc-dark);
        }
        .vcard__btn--outline:hover { background: var(--vc-dark); color: #fff; }

        .qr-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(10, 20, 60, 0.55);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .qr-modal-backdrop.is-open { display: flex; }

        .qr-modal {
            background: #fff;
            border-radius: 18px;
            padding: 2rem;
            width: 100%;
            max-width: 320px;
            text-align: center;
            box-shadow: 0 16px 48px rgba(10, 20, 80, 0.2);
            animation: modal-in 0.2s ease;
        }

        @keyframes modal-in {
            from { opacity: 0; transform: scale(0.93); }
            to   { opacity: 1; transform: scale(1); }
        }

        .qr-modal__title    { font-size: 1rem; font-weight: 600; color: var(--vc-dark); margin: 0 0 0.25rem; }
        .qr-modal__subtitle { font-size: 0.82rem; color: #666; margin: 0 0 1.25rem; }
        .qr-modal__image    { width: 100%; max-width: 220px; height: auto; border-radius: 8px; border: 1px solid #e8eaf6; }

        .qr-modal__close {
            margin-top: 1.25rem;
            background: none;
            border: none;
            color: #999;
            font-size: 0.875rem;
            cursor: pointer;
            padding: 0.25rem 0.5rem;
        }
        .qr-modal__close:hover { color: var(--vc-dark); }

        .qr-modal__download {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            margin-top: 1rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--vc-dark);
            text-decoration: none;
        }
        .qr-modal__download:hover { color: var(--vc-mid); }
    </style>
</head>
<body>
    <div class="vcard-page">
        <article class="vcard">

            <header class="vcard__logo-wrap">
                <span class="vcard__logo-text">
                    {{-- First word gets the strikethrough, rest follows --}}
                    @php $parts = explode(' ', $brandName, 2); @endphp
                    <span class="logo-strike">{{ $parts[0] }}</span>{{ isset($parts[1]) ? $parts[1] : '' }}
                </span>
                @if ($brandTagline)
                <span class="vcard__logo-tagline">{!! nl2br(e($brandTagline)) !!}</span>
                @endif
            </header>

            <h1 class="vcard__name">{{ $title }}</h1>

            @if ($f('role'))
            <p class="vcard__role">{{ $f('role') }}</p>
            @endif

            <ul class="vcard__contacts">
                @if ($f('mobile'))
                <li class="vcard__contact-item">
                    <span class="vcard__icon vcard__icon--mobile"><i class="bi bi-phone"></i></span>
                    <span class="vcard__contact-detail"><a href="tel:{{ $f('mobile') }}">{{ $f('mobile') }}</a></span>
                </li>
                @endif

                @if ($f('phone'))
                <li class="vcard__contact-item">
                    <span class="vcard__icon"><i class="bi bi-telephone-fill"></i></span>
                    <span class="vcard__contact-detail"><a href="tel:{{ $f('phone') }}">{{ $f('phone') }}</a></span>
                </li>
                @endif

                @if ($f('email'))
                <li class="vcard__contact-item">
                    <span class="vcard__icon"><i class="bi bi-envelope-fill"></i></span>
                    <span class="vcard__contact-detail"><a href="mailto:{{ $f('email') }}">{{ $f('email') }}</a></span>
                </li>
                @endif

                @if ($f('website'))
                <li class="vcard__contact-item">
                    <span class="vcard__icon"><i class="bi bi-globe2"></i></span>
                    <span class="vcard__contact-detail">
                        <a href="{{ $f('website') }}" target="_blank" rel="noopener noreferrer">{{ $f('website') }}</a>
                    </span>
                </li>
                @endif

                @if ($f('address'))
                <li class="vcard__contact-item">
                    <span class="vcard__icon"><i class="bi bi-geo-alt-fill"></i></span>
                    <span class="vcard__contact-detail">{!! nl2br(e($f('address'))) !!}</span>
                </li>
                @endif
            </ul>

            <div class="vcard__actions">
                <a href="{{ $vcfRoute }}" class="vcard__btn" download>
                    <i class="bi bi-person-vcard-fill"></i>
                    Download
                </a>

                @if ($qrAsset)
                <button class="vcard__btn vcard__btn--outline" onclick="document.getElementById('qrModal').classList.add('is-open')">
                    <i class="bi bi-qr-code"></i>
                    QR code
                </button>
                @endif
            </div>

        </article>
    </div>

    @if ($qrAsset)
    <div class="qr-modal-backdrop" id="qrModal" onclick="closeQrModal(event)">
        <div class="qr-modal" role="dialog" aria-modal="true" aria-label="QR code voor {{ $title }}">
            <p class="qr-modal__title">{{ $title }}</p>
            <p class="qr-modal__subtitle">Scan om het visitekaartje te openen</p>
            <img src="{{ $qrAsset->url() }}" alt="QR code voor {{ $title }}" class="qr-modal__image">
            <br>
            <a href="{{ $qrAsset->url() }}" class="qr-modal__download" download>
                <i class="bi bi-download"></i> Download QR
            </a>
            <br>
            <button class="qr-modal__close" onclick="document.getElementById('qrModal').classList.remove('is-open')">
                Sluiten
            </button>
        </div>
    </div>

    <script>
        function closeQrModal(e) {
            if (e.target === document.getElementById('qrModal')) {
                document.getElementById('qrModal').classList.remove('is-open');
            }
        }
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') document.getElementById('qrModal').classList.remove('is-open');
        });
    </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmrXEbwQQ9xnl7q0mQPnJMG0VFEtUyFMi/RHkrDQEJ7FTk" crossorigin="anonymous"></script>
</body>
</html>
