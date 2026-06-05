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
            <img src=""
                 class="img-fluid d-none d-sm-block position-relative top-0 z-2"
                 height="30"
                 alt="Logo">
            {{--            <span class="vcard__logo-text"><span class="logo-off">OFF</span>LIMITS</span>--}}
            {{--            <span class="vcard__logo-tagline">bestrijdt<br>online misbruik</span>--}}
        </header>

        <h1 class="vcard__name">{{ $title }}</h1>
        <p class="vcard__role">{{ $job_description }}</p>

        <ul class="vcard__contacts">
            @if ($mobile_phone)
                <li class="vcard__contact-item">
                    {{--                    <div class="vcard__icon vcard__icon--mobile">--}}
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M16.75 10.5H14.3125V30H25.6875V10.5H23.25V12.125H16.75V10.5ZM14.3125 7.25H25.6875H28.9375V10.5V30V33.25H25.6875H14.3125H11.0625V30V10.5V7.25H14.3125ZM16.75 26.75H17.5625H22.4375H23.25V28.375H22.4375H17.5625H16.75V26.75Z"
                            fill="{{ $colorDark}}"/>
                    </svg>
                    {{--                    </div>--}}
                    <div class="vcard__contact-detail"><a href="tel:{{ $mobile_phone}}">{{ $mobile_phone}}</a></div>
                </li>
            @endif

            @if ($phone_number)
                <li class="vcard__contact-item d-flex align-items-center">
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M7 8.875L14.3125 7.25L18.375 14.5625L14.1094 17.9648C15.9375 21.4688 18.7812 24.3125 22.2852 26.1406L25.6875 21.875L33 25.9375L31.375 33.25H29.75C17.1562 33.25 7 23.0938 7 10.5V8.875Z"
                            fill="{{ $colorDark }}" />
                    </svg>
                    <span class="vcard__contact-detail"><a href="tel:{{ $phone_number }}">{{ $phone_number }}</a></span>
                    {{--                    <div class="vcard__icon">--}}
                    {{--                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">--}}
                    {{--                        <path d="M7 8.875L14.3125 7.25L18.375 14.5625L14.1094 17.9648C15.9375 21.4688 18.7812 24.3125 22.2852 26.1406L25.6875 21.875L33 25.9375L31.375 33.25H29.75C17.1562 33.25 7 23.0938 7 10.5V8.875Z" fill="{{ $colorDark }}" />--}}
                    {{--                        </svg>--}}
                    {{--                    </div>--}}
                    {{--                    <div>--}}
                    {{--                        <span class="vcard__contact-detail"><a href="tel:{{ $phone_number }}">{{ $phone_number }}</a></span>--}}
                    {{--                    </div>--}}
                </li>
            @endif

            @if ($email)
                <li class="vcard__contact-item">
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M7 10.5H33V14.5625L20 23.5L7 14.5625V10.5ZM7 30V16.543L19.0352 24.8711L20 25.4805L20.9141 24.8711L33 16.543V30H7Z"
                            fill="{{ $colorDark }}" />
                    </svg>

                    <span class="vcard__contact-detail"><a href="mailto:{{ $email }}">{{ $email }}</a></span>
                </li>
            @endif

            @if ($website)
                <li class="vcard__contact-item">
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M24.875 20.25C24.875 21.418 24.7227 22.4844 24.5195 23.5H15.4297C15.2266 22.4844 15.125 21.418 15.125 20.25C15.125 19.1328 15.2266 18.0664 15.4297 17H24.5195C24.7227 18.0664 24.875 19.1328 24.875 20.25ZM26.1953 17H32.5938C32.8477 18.0664 33 19.1328 33 20.25C33 21.418 32.8477 22.4844 32.5938 23.5H26.1953C26.3984 22.4844 26.5 21.418 26.5 20.25C26.5 19.1328 26.3984 18.0664 26.1953 17ZM32.0352 15.375H25.8398C24.9766 12.0742 23.4023 9.28125 22.082 7.45312C26.6016 8.16406 30.3594 11.2617 32.0352 15.375ZM24.1641 15.375H15.7852C16.3945 13.3438 17.2578 11.5664 18.1211 10.043C18.7812 8.92578 19.4414 8.01172 20 7.35156C20.5078 8.01172 21.168 8.92578 21.8281 10.043C22.6914 11.5156 23.6055 13.3438 24.1641 15.375ZM14.1094 15.375H7.91406C9.58984 11.2617 13.3477 8.16406 17.8672 7.45312C16.5469 9.28125 14.9727 12.0742 14.1094 15.375ZM7.40625 17H13.7539C13.6016 18.0664 13.5 19.1328 13.5 20.25C13.5 21.418 13.6016 22.4844 13.7539 23.5H7.40625C7.10156 22.4844 7 21.418 7 20.25C7 19.1328 7.10156 18.0664 7.40625 17ZM15.7852 25.125H24.1641C23.5547 27.207 22.6914 28.9844 21.8281 30.5078C21.168 31.625 20.5078 32.5391 20 33.1992C19.4414 32.5391 18.7812 31.625 18.1211 30.5078C17.2578 28.9844 16.3945 27.207 15.7852 25.125ZM14.1094 25.125C14.9727 28.4766 16.5469 31.2188 17.8672 33.0977C13.3477 32.3867 9.58984 29.2891 7.91406 25.125H14.1094ZM32.0352 25.125C30.3594 29.2891 26.6016 32.3867 22.082 33.0977C23.4023 31.2188 24.9766 28.4766 25.8398 25.125H32.0352Z"
                            fill="{{ $colorDark }}" />
                    </svg>

                    <span class="vcard__contact-detail"><a href="{{ $website }}" target="_blank"
                                                           rel="noopener noreferrer">{{ $website }}</a></span>
                </li>
            @endif

            @if ($address)
                <li class="vcard__contact-item" style="align-items: flex-start;">
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M29.75 17C29.75 22.6875 20 33.25 20 33.25C20 33.25 10.25 22.6875 10.25 17C10.25 11.6172 14.6172 7.25 20 7.25C25.3828 7.25 29.75 11.6172 29.75 17Z"
                            fill="{{ $colorDark }}" />
                    </svg>

                    <span class="vcard__contact-detail">{!! nl2br(e($address)) !!}</span>
                </li>
            @endif
        </ul>

        <div class="vcard__actions">
            <a href="/vcards/{{ $slug }}.vcf" class="vcard__download-btn" download>
                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M25.9688 6.65625V23.7188H4.03125V6.65625H25.9688ZM13.7812 16.4062H7.6875L6.46875 20.0625H15L13.7812 16.4062ZM8.29688 12.75C8.29688 13.626 8.75391 14.4258 9.51562 14.8828C10.2393 15.3018 11.1914 15.3018 11.9531 14.8828C12.6768 14.4258 13.1719 13.626 13.1719 12.75C13.1719 11.9121 12.6768 11.1123 11.9531 10.6553C11.1914 10.2363 10.2393 10.2363 9.51562 10.6553C8.75391 11.1123 8.29688 11.9121 8.29688 12.75ZM18.0469 11.5312H17.4375V12.75H18.0469H22.9219H23.5312V11.5312H22.9219H18.0469ZM18.0469 13.9688H17.4375V15.1875H18.0469H22.9219H23.5312V13.9688H22.9219H18.0469ZM18.0469 16.4062H17.4375V17.625H18.0469H22.9219H23.5312V16.4062H22.9219H18.0469Z"
                        fill="white"/>
                </svg>

                Zet in contacten
            </a>
        </div>
        @if ($qr_code)
            <div class="vcard__actions" style="margin-top: 10px;">
                <button class="vcard__download-btn" onclick="document.getElementById('qrModal').classList.add('is-open')">
                    <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.75 0V9.75H0V0H9.75ZM6.5 3.25H3.25V6.5H6.5V3.25ZM9.75 13V22.75H0V13H9.75ZM6.5 16.25H3.25V19.5H6.5V16.25ZM13 0H22.75V9.75H13V0ZM16.25 3.25V6.5H19.5V3.25H16.25ZM17.875 21.125H19.5V22.75H17.875V21.125ZM22.75 21.125V22.75H21.125V21.125H22.75ZM21.125 13H22.75V14.625V16.25V19.5H17.875V17.875H16.25V22.75H13V17.875V13H17.875V14.625H21.125V13Z" fill="white"/>
                    </svg>


                    QR code
                </button>
            </div>
        @endif

    </article>

</div>
@if ($qr_code)
    @php
        $qrAsset = null;
        if ($qr_code) {
            $raw = is_object($qr_code) ? $qr_code->raw() : $qr_code;
            if ($raw) {
                [$containerHandle, $path] = explode('::', $raw);
                $qrAsset = \Statamic\Facades\Asset::findById("{$containerHandle}::{$path}");
            }
        }
    @endphp
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
</body>
</html>