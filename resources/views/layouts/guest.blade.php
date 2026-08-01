<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'NovaAdmin') }} — Sign In</title>
        <meta name="description" content="Sign in to your {{ config('app.name', 'NovaAdmin') }} dashboard">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            *, *::before, *::after { box-sizing: border-box; }

            body.login-body {
                font-family: 'Inter', sans-serif;
                margin: 0;
                padding: 0;
                min-height: 100vh;
                overflow: hidden;
                background: #0a0a1a;
            }

            /* ── Full-screen Background ── */
            .login-fullscreen {
                position: relative;
                min-height: 100vh;
                width: 100%;
                background: url('/images/login-bg.png') center/cover no-repeat fixed;
                overflow: hidden;
            }

            /* Content sits on top as a flex row */
            .login-content {
                position: relative;
                z-index: 3;
                display: flex;
                align-items: center;
                justify-content: space-between;
                min-height: 100vh;
                padding: 3rem 6rem;
            }

            /* Dark overlay for readability */
            .login-brand-overlay {
                position: absolute;
                inset: 0;
                background: linear-gradient(
                    135deg,
                    rgba(10, 10, 26, 0.65) 0%,
                    rgba(15, 12, 41, 0.55) 50%,
                    rgba(36, 36, 62, 0.60) 100%
                );
                z-index: 1;
            }

            /* Floating orbs on full-screen */
            .login-fullscreen::before,
            .login-fullscreen::after {
                content: '';
                position: absolute;
                border-radius: 50%;
                filter: blur(80px);
                opacity: 0.3;
                animation: floatOrb 8s ease-in-out infinite;
                z-index: 1;
            }

            .login-fullscreen::before {
                width: 350px;
                height: 350px;
                background: radial-gradient(circle, #6366f1, transparent 70%);
                top: 10%;
                left: 10%;
            }

            .login-fullscreen::after {
                width: 280px;
                height: 280px;
                background: radial-gradient(circle, #a78bfa, transparent 70%);
                bottom: 15%;
                right: 10%;
                animation-delay: -4s;
                animation-duration: 10s;
            }

            @keyframes floatOrb {
                0%, 100% { transform: translate(0, 0) scale(1); }
                25%      { transform: translate(30px, -40px) scale(1.1); }
                50%      { transform: translate(-20px, 20px) scale(0.95); }
                75%      { transform: translate(15px, 30px) scale(1.05); }
            }

            /* Geometric grid overlay — full screen */
            .brand-grid {
                position: absolute;
                inset: 0;
                background-image:
                    linear-gradient(rgba(99, 102, 241, 0.06) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(99, 102, 241, 0.06) 1px, transparent 1px);
                background-size: 60px 60px;
                animation: gridPulse 4s ease-in-out infinite;
                z-index: 1;
            }

            @keyframes gridPulse {
                0%, 100% { opacity: 0.5; }
                50%      { opacity: 1; }
            }

            /* Brand content */
            .brand-content {
                position: relative;
                z-index: 2;
                text-align: center;
                padding: 2rem;
                width: 70%;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            .brand-logo-ring {
                width: 100px;
                height: 100px;
                margin: 0 auto 2rem;
                border-radius: 24px;
                background: rgba(255, 255, 255, 0.08);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.12);
                display: flex;
                align-items: center;
                justify-content: center;
                animation: logoFloat 6s ease-in-out infinite;
                box-shadow:
                    0 0 40px rgba(99, 102, 241, 0.15),
                    inset 0 0 40px rgba(99, 102, 241, 0.05);
            }

            @keyframes logoFloat {
                0%, 100% { transform: translateY(0); }
                50%      { transform: translateY(-12px); }
            }

            .brand-logo-ring svg {
                width: 48px;
                height: 48px;
                fill: #c4b5fd;
                filter: drop-shadow(0 0 8px rgba(196, 181, 253, 0.4));
            }

            .brand-title {
                font-size: 2.5rem;
                font-weight: 800;
                background: linear-gradient(135deg, #e0e7ff 0%, #c4b5fd 50%, #818cf8 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                letter-spacing: -0.02em;
                margin: 0 0 0.75rem;
                line-height: 1.2;
            }

            .brand-subtitle {
                color: rgba(196, 181, 253, 0.6);
                font-size: 1rem;
                font-weight: 400;
                letter-spacing: 0.05em;
                max-width: 300px;
                margin: 0 auto;
                line-height: 1.6;
            }

            /* Floating shapes */
            .floating-shape {
                position: absolute;
                border: 1px solid rgba(99, 102, 241, 0.15);
                border-radius: 12px;
                animation: shapeFloat 20s linear infinite;
                z-index: 1;
            }

            .floating-shape:nth-child(3) {
                width: 80px; height: 80px;
                top: 20%; left: 15%;
                animation-duration: 18s;
                transform: rotate(15deg);
            }

            .floating-shape:nth-child(4) {
                width: 50px; height: 50px;
                top: 60%; right: 10%;
                animation-duration: 22s;
                animation-delay: -5s;
                border-radius: 50%;
                border-color: rgba(167, 139, 250, 0.12);
            }

            .floating-shape:nth-child(5) {
                width: 120px; height: 120px;
                bottom: 10%; left: 40%;
                animation-duration: 25s;
                animation-delay: -10s;
                transform: rotate(45deg);
            }

            .floating-shape:nth-child(6) {
                width: 40px; height: 40px;
                top: 10%; right: 30%;
                animation-duration: 15s;
                animation-delay: -3s;
                border-radius: 50%;
            }

            @keyframes shapeFloat {
                0%   { transform: translateY(0) rotate(0deg); opacity: 0.3; }
                25%  { transform: translateY(-30px) rotate(90deg); opacity: 0.6; }
                50%  { transform: translateY(10px) rotate(180deg); opacity: 0.3; }
                75%  { transform: translateY(-20px) rotate(270deg); opacity: 0.5; }
                100% { transform: translateY(0) rotate(360deg); opacity: 0.3; }
            }



            /* Glass Card */
            .login-card {
                position: relative;
                z-index: 2;
                width: 100%;
                max-width: 420px;
                padding: 2.5rem;
                border-radius: 24px;
                background: rgba(13, 13, 31, 0.75);
                backdrop-filter: blur(40px) saturate(1.2);
                border: 1px solid rgba(255, 255, 255, 0.08);
                box-shadow:
                    0 25px 60px rgba(0, 0, 0, 0.5),
                    0 0 0 1px rgba(255, 255, 255, 0.04) inset;
                animation: cardAppear 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                opacity: 0;
                transform: translateY(20px);
            }

            @keyframes cardAppear {
                to { opacity: 1; transform: translateY(0); }
            }

            .login-card-header {
                text-align: center;
                margin-bottom: 2rem;
            }

            .login-card-header h1 {
                font-size: 1.5rem;
                font-weight: 700;
                color: #f1f5f9;
                margin: 0 0 0.5rem;
                letter-spacing: -0.01em;
            }

            .login-card-header p {
                color: rgba(148, 163, 184, 0.7);
                font-size: 0.875rem;
                margin: 0;
                font-weight: 400;
            }

            /* Form overrides (scoped to login) */
            .login-card .login-field {
                margin-bottom: 1.25rem;
            }

            .login-card .login-field label {
                display: block;
                font-size: 0.8rem;
                font-weight: 500;
                color: #94a3b8 !important;
                margin-bottom: 0.5rem;
                letter-spacing: 0.03em;
                text-transform: uppercase;
            }

            .login-card .login-field input[type="email"],
            .login-card .login-field input[type="password"] {
                width: 100%;
                padding: 0.75rem 1rem !important;
                font-size: 0.9rem !important;
                border-radius: 12px !important;
                border: 1px solid rgba(99, 102, 241, 0.15) !important;
                background: rgba(15, 23, 42, 0.6) !important;
                color: #f1f5f9 !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                outline: none;
                box-shadow: none !important;
            }

            .login-card .login-field input[type="email"]:focus,
            .login-card .login-field input[type="password"]:focus {
                border-color: #6366f1 !important;
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15), 0 0 20px rgba(99, 102, 241, 0.08) !important;
                background: rgba(15, 23, 42, 0.8) !important;
            }

            .login-card .login-field input::placeholder {
                color: rgba(148, 163, 184, 0.4);
            }

            /* Remember & Forgot row */
            .login-meta-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 1.5rem;
                margin-top: 0.25rem;
            }

            .login-meta-row label {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                cursor: pointer;
                font-size: 0.8rem;
                color: #94a3b8;
                text-transform: none !important;
                margin-bottom: 0 !important;
            }

            .login-meta-row input[type="checkbox"] {
                width: 16px;
                height: 16px;
                border-radius: 5px !important;
                border: 1px solid rgba(99, 102, 241, 0.3) !important;
                background: rgba(15, 23, 42, 0.6) !important;
                cursor: pointer;
                accent-color: #6366f1;
            }

            .login-meta-row input[type="checkbox"]:checked {
                background: #6366f1 !important;
                border-color: #6366f1 !important;
            }

            .login-forgot-link {
                font-size: 0.8rem;
                color: #818cf8;
                text-decoration: none;
                font-weight: 500;
                transition: color 0.2s;
            }

            .login-forgot-link:hover {
                color: #a5b4fc;
            }

            /* Submit Button */
            .login-submit-btn {
                width: 100%;
                padding: 0.85rem 1.5rem !important;
                font-size: 0.9rem !important;
                font-weight: 600 !important;
                text-transform: none !important;
                letter-spacing: 0 !important;
                border-radius: 12px !important;
                border: none !important;
                background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a78bfa 100%) !important;
                background-size: 200% 200% !important;
                color: #ffffff !important;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3), 0 1px 3px rgba(0, 0, 0, 0.2) !important;
                position: relative;
                overflow: hidden;
                height: auto !important;
            }

            .login-submit-btn::before {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent 50%);
                border-radius: 12px;
                opacity: 0;
                transition: opacity 0.3s;
            }

            .login-submit-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4), 0 2px 6px rgba(0, 0, 0, 0.3) !important;
                background-position: 100% 100% !important;
            }

            .login-submit-btn:hover::before {
                opacity: 1;
            }

            .login-submit-btn:active {
                transform: translateY(0);
            }

            /* Divider */
            .login-divider {
                display: flex;
                align-items: center;
                margin: 1.5rem 0;
                gap: 1rem;
            }

            .login-divider::before,
            .login-divider::after {
                content: '';
                flex: 1;
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(99, 102, 241, 0.15), transparent);
            }

            .login-divider span {
                font-size: 0.75rem;
                color: rgba(148, 163, 184, 0.4);
                text-transform: uppercase;
                letter-spacing: 0.1em;
                font-weight: 500;
            }

            /* Footer text */
            .login-footer {
                text-align: center;
                margin-top: 1.5rem;
                font-size: 0.8rem;
                color: rgba(148, 163, 184, 0.4);
            }

            .login-footer a {
                color: #818cf8;
                text-decoration: none;
                font-weight: 500;
                transition: color 0.2s;
            }

            .login-footer a:hover {
                color: #a5b4fc;
            }

            /* Session status */
            .login-session-status {
                margin-bottom: 1rem;
                padding: 0.75rem 1rem;
                border-radius: 12px;
                background: rgba(34, 197, 94, 0.1);
                border: 1px solid rgba(34, 197, 94, 0.2);
                color: #4ade80;
                font-size: 0.85rem;
            }

            /* ── Vertical Divider ── */
            .login-divider-line {
                position: absolute;
                top: 0;
                bottom: 0;
                left: 70%;
                width: 1px;
                background: linear-gradient(
                    to bottom,
                    transparent 0%,
                    rgba(99, 102, 241, 0.1) 15%,
                    rgba(139, 92, 246, 0.3) 50%,
                    rgba(99, 102, 241, 0.1) 85%,
                    transparent 100%
                );
                z-index: 4;
            }

            /* ── Right-side Blur Backdrop ── */
            .login-blur-panel {
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                width: 30%;
                backdrop-filter: blur(10px) saturate(1.1);
                background: rgba(10, 10, 26, 0.45);
                z-index: 2;
                border-left: 1px solid rgba(99, 102, 241, 0.06);
            }

            /* ── Responsive ── */
            @media (max-width: 900px) {
                .login-content {
                    justify-content: center;
                    padding: 2rem;
                }
                .brand-content {
                    display: none;
                }
                .login-divider-line {
                    display: none;
                }
                .login-blur-panel {
                    width: 100%;
                    border-left: none;
                }
            }

            @media (max-width: 480px) {
                .login-content {
                    padding: 1.25rem;
                }
                .login-card {
                    padding: 1.75rem;
                    border-radius: 20px;
                }
            }
        </style>
    </head>
    <body class="login-body">
        <div class="login-fullscreen">
            {{-- Dark overlay --}}
            <div class="login-brand-overlay"></div>

            {{-- Right-side blur backdrop --}}
            <div class="login-blur-panel"></div>

            {{-- Vertical divider --}}
            <div class="login-divider-line"></div>

            {{-- Grid + floating shapes (decorative, full screen) --}}
            <div class="brand-grid"></div>
            <div class="floating-shape"></div>
            <div class="floating-shape"></div>
            <div class="floating-shape"></div>
            <div class="floating-shape"></div>

            {{-- Content wrapper --}}
            <div class="login-content">
                {{-- Left: Branding --}}
                <div class="brand-content">
                    <div class="brand-logo-ring">
                        <svg viewBox="0 0 316 316" xmlns="http://www.w3.org/2000/svg">
                            <path d="M305.8 81.125C305.77 80.995 305.69 80.885 305.65 80.755C305.56 80.525 305.49 80.285 305.37 80.075C305.29 79.935 305.17 79.815 305.07 79.685C304.94 79.515 304.83 79.325 304.68 79.175C304.55 79.045 304.39 78.955 304.25 78.845C304.09 78.715 303.95 78.575 303.77 78.475L251.32 48.275C249.97 47.495 248.31 47.495 246.96 48.275L194.51 78.475C194.33 78.575 194.19 78.725 194.03 78.845C193.89 78.955 193.73 79.045 193.6 79.175C193.45 79.325 193.34 79.515 193.21 79.685C193.11 79.815 192.99 79.935 192.91 80.075C192.79 80.285 192.71 80.525 192.63 80.755C192.58 80.875 192.51 80.995 192.48 81.125C192.38 81.495 192.33 81.875 192.33 82.265V139.625L148.62 164.795V52.575C148.62 52.185 148.57 51.805 148.47 51.435C148.44 51.305 148.36 51.195 148.32 51.065C148.23 50.835 148.16 50.595 148.04 50.385C147.96 50.245 147.84 50.125 147.74 49.995C147.61 49.825 147.5 49.635 147.35 49.485C147.22 49.355 147.06 49.265 146.92 49.155C146.76 49.025 146.62 48.885 146.44 48.785L93.99 18.585C92.64 17.805 90.98 17.805 89.63 18.585L37.18 48.785C37 48.885 36.86 49.035 36.7 49.155C36.56 49.265 36.4 49.355 36.27 49.485C36.12 49.635 36.01 49.825 35.88 49.995C35.78 50.125 35.66 50.245 35.58 50.385C35.46 50.595 35.38 50.835 35.3 51.065C35.25 51.185 35.18 51.305 35.15 51.435C35.05 51.805 35 52.185 35 52.575V232.235C35 233.795 35.84 235.245 37.19 236.025L142.1 296.425C142.33 296.555 142.58 296.635 142.82 296.725C142.93 296.765 143.04 296.835 143.16 296.865C143.53 296.965 143.9 297.015 144.28 297.015C144.66 297.015 145.03 296.965 145.4 296.865C145.5 296.835 145.59 296.775 145.69 296.745C145.95 296.655 146.21 296.565 146.45 296.435L251.36 236.035C252.72 235.255 253.55 233.815 253.55 232.245V174.885L303.81 145.945C305.17 145.165 306 143.725 306 142.155V82.265C305.95 81.875 305.89 81.495 305.8 81.125ZM144.2 227.205L100.57 202.515L146.39 176.135L196.66 147.195L240.33 172.335L208.29 190.625L144.2 227.205ZM244.75 114.995V164.795L226.39 154.225L201.03 139.625V89.825L219.39 100.395L244.75 114.995ZM249.12 57.105L292.81 82.265L249.12 107.425L205.43 82.265L249.12 57.105ZM114.49 184.425L96.13 194.995V85.305L121.49 70.705L139.85 60.135V169.815L114.49 184.425ZM91.76 27.425L135.45 52.585L91.76 77.745L48.07 52.585L91.76 27.425ZM43.67 60.135L62.03 70.705L87.39 85.305V202.545V202.555V202.565C87.39 202.735 87.44 202.895 87.46 203.055C87.49 203.265 87.49 203.485 87.55 203.695V203.705C87.6 203.875 87.69 204.035 87.76 204.195C87.84 204.375 87.89 204.575 87.99 204.745C87.99 204.745 87.99 204.755 88 204.755C88.09 204.905 88.22 205.035 88.33 205.175C88.45 205.335 88.55 205.495 88.69 205.635L88.7 205.645C88.82 205.765 88.98 205.855 89.12 205.965C89.28 206.085 89.42 206.225 89.59 206.325C89.6 206.325 89.6 206.325 89.61 206.335C89.62 206.335 89.62 206.345 89.63 206.345L139.87 234.775V285.065L43.67 229.705V60.135ZM244.75 229.705L148.58 285.075V234.775L219.8 194.115L244.75 179.875V229.705ZM297.2 139.625L253.49 164.795V114.995L278.85 100.395L297.21 89.825V139.625H297.2Z"/>
                        </svg>
                    </div>
                    <h2 class="brand-title">{{ config('app.name', 'NovaAdmin') }}</h2>
                    <p class="brand-subtitle">Your intelligent business management platform</p>
                </div>

                {{-- Right: Login Card --}}
                <div class="login-card">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>

