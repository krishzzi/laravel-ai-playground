<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AI Relm — Elite AI Product Systems</title>
    <meta name="description" content="AI Relm builds elite-grade AI products, agents, and intelligent interfaces for modern businesses.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://api.fontshare.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@300,400,500,700,900&f[]=clash-display@400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root,
        [data-theme="light"] {
            --text-xs: clamp(0.75rem, 0.7rem + 0.25vw, 0.875rem);
            --text-sm: clamp(0.875rem, 0.8rem + 0.35vw, 1rem);
            --text-base: clamp(1rem, 0.95rem + 0.25vw, 1.125rem);
            --text-lg: clamp(1.125rem, 1rem + 0.75vw, 1.5rem);
            --text-xl: clamp(1.5rem, 1.2rem + 1.25vw, 2.2rem);
            --text-2xl: clamp(2.2rem, 1.4rem + 3vw, 4.8rem);
            --text-3xl: clamp(3rem, 1.2rem + 5vw, 7rem);

            --space-1: .25rem;
            --space-2: .5rem;
            --space-3: .75rem;
            --space-4: 1rem;
            --space-5: 1.25rem;
            --space-6: 1.5rem;
            --space-8: 2rem;
            --space-10: 2.5rem;
            --space-12: 3rem;
            --space-16: 4rem;
            --space-20: 5rem;
            --space-24: 6rem;
            --space-32: 8rem;

            --color-bg: #f6f7fb;
            --color-surface: #ffffff;
            --color-surface-2: #edf2ff;
            --color-surface-3: #e6ecff;
            --color-border: rgba(18, 24, 43, 0.12);
            --color-divider: rgba(18, 24, 43, 0.08);
            --color-text: #101626;
            --color-text-muted: #5f6882;
            --color-text-faint: #8791aa;
            --color-text-inverse: #f8fbff;
            --color-primary: #56f0d0;
            --color-primary-hover: #31d7b6;
            --color-primary-active: #18b597;
            --color-secondary: #6c7cff;
            --color-accent: #8e5bff;
            --color-success: #42d392;
            --radius-sm: .45rem;
            --radius-md: .8rem;
            --radius-lg: 1.2rem;
            --radius-xl: 1.7rem;
            --radius-2xl: 2rem;
            --radius-full: 9999px;
            --shadow-sm: 0 10px 30px rgba(16, 22, 38, 0.06);
            --shadow-md: 0 18px 45px rgba(16, 22, 38, 0.12);
            --shadow-lg: 0 35px 90px rgba(16, 22, 38, 0.2);
            --transition-interactive: 180ms cubic-bezier(0.16, 1, 0.3, 1);
            --ease-enter: cubic-bezier(0.16, 1, 0.3, 1);
            --ease-exit: cubic-bezier(0.4, 0, 1, 1);
            --container: 1240px;
            --font-display: 'Clash Display', 'Inter', sans-serif;
            --font-body: 'Satoshi', 'Inter', sans-serif;
        }

        [data-theme="dark"] {
            --color-bg: #03050b;
            --color-surface: #08101a;
            --color-surface-2: #0d1626;
            --color-surface-3: #121d32;
            --color-border: rgba(181, 203, 255, 0.12);
            --color-divider: rgba(181, 203, 255, 0.08);
            --color-text: #eef5ff;
            --color-text-muted: #99a8c8;
            --color-text-faint: #667594;
            --color-text-inverse: #071019;
            --color-primary: #66f5d7;
            --color-primary-hover: #8df9e3;
            --color-primary-active: #2ed7b5;
            --color-secondary: #7f8dff;
            --color-accent: #9f73ff;
            --color-success: #42d392;
            --shadow-sm: 0 10px 26px rgba(0, 0, 0, 0.22);
            --shadow-md: 0 24px 60px rgba(0, 0, 0, 0.34);
            --shadow-lg: 0 40px 120px rgba(0, 0, 0, 0.46);
        }

        * , *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html {
            scroll-behavior: smooth;
            -webkit-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }
        body {
            min-height: 100vh;
            overflow-x: hidden;
            font-family: var(--font-body);
            font-size: var(--text-base);
            line-height: 1.65;
            color: var(--color-text);
            background:
                radial-gradient(circle at 10% 8%, rgba(102,245,215,.10), transparent 24%),
                radial-gradient(circle at 88% 12%, rgba(127,141,255,.14), transparent 20%),
                radial-gradient(circle at 50% 70%, rgba(159,115,255,.08), transparent 24%),
                var(--color-bg);
        }
        a { color: inherit; text-decoration: none; }
        button { background: none; border: none; color: inherit; font: inherit; cursor: pointer; }
        input, textarea { font: inherit; color: inherit; }
        img, svg { display: block; max-width: 100%; height: auto; }
        ul { list-style: none; }
        h1, h2, h3, h4 {
            font-family: var(--font-display);
            line-height: 1.02;
            letter-spacing: -.035em;
        }
        p {
            max-width: 68ch;
            color: var(--color-text-muted);
        }
        :focus-visible {
            outline: 2px solid var(--color-primary);
            outline-offset: 3px;
            border-radius: var(--radius-sm);
        }

        .skip-link {
            position: absolute;
            top: -3rem;
            left: 1rem;
            z-index: 999;
            padding: .85rem 1rem;
            border-radius: var(--radius-full);
            background: var(--color-primary);
            color: #05110d;
            transition: top var(--transition-interactive);
        }
        .skip-link:focus { top: 1rem; }

        .container {
            width: min(calc(100% - 2rem), var(--container));
            margin-inline: auto;
        }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 120;
            backdrop-filter: blur(16px);
            background: color-mix(in srgb, var(--color-bg) 72%, transparent);
            border-bottom: 1px solid transparent;
            transition: border-color .22s ease, box-shadow .22s ease, background .22s ease;
        }
        .site-header.is-scrolled {
            border-color: var(--color-divider);
            box-shadow: var(--shadow-sm);
        }
        .nav-shell {
            min-height: 84px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--space-4);
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: .9rem;
        }
        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            border: 1px solid var(--color-border);
            background:
                linear-gradient(145deg, rgba(102,245,215,.14), rgba(127,141,255,.18)),
                color-mix(in srgb, var(--color-surface) 90%, transparent);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.06), var(--shadow-sm);
        }
        .brand-text strong {
            display: block;
            font-size: var(--text-sm);
            letter-spacing: .04em;
        }
        .brand-text span {
            display: block;
            font-size: var(--text-xs);
            color: var(--color-text-faint);
            text-transform: uppercase;
            letter-spacing: .16em;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: .4rem;
            padding: .35rem;
            border-radius: var(--radius-full);
            border: 1px solid var(--color-border);
            background: color-mix(in srgb, var(--color-surface) 82%, transparent);
        }
        .nav-links a {
            padding: .82rem 1rem;
            border-radius: var(--radius-full);
            font-size: var(--text-sm);
            color: var(--color-text-muted);
            transition: color var(--transition-interactive), background var(--transition-interactive), transform var(--transition-interactive);
        }
        .nav-links a:hover,
        .nav-links a:focus-visible {
            color: var(--color-text);
            background: rgba(255,255,255,.05);
            transform: translateY(-1px);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .theme-toggle,
        .menu-toggle,
        .icon-btn {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            border: 1px solid var(--color-border);
            background: color-mix(in srgb, var(--color-surface) 84%, transparent);
            transition: transform var(--transition-interactive), background var(--transition-interactive), border-color var(--transition-interactive);
        }
        .theme-toggle:hover,
        .menu-toggle:hover,
        .icon-btn:hover {
            transform: translateY(-1px);
            background: var(--color-surface-2);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .7rem;
            min-height: 50px;
            padding: 0 1.25rem;
            border-radius: var(--radius-full);
            font-size: var(--text-sm);
            font-weight: 700;
            transition: transform var(--transition-interactive), box-shadow var(--transition-interactive), background var(--transition-interactive), color var(--transition-interactive), border-color var(--transition-interactive);
        }
        .btn:hover { transform: translateY(-1px); }
        .btn:active { transform: translateY(0); }

        .btn-primary {
            background: linear-gradient(135deg, var(--color-primary), #a3ffec);
            color: #04120e;
            box-shadow: 0 14px 34px rgba(102,245,215,.22);
        }
        .btn-secondary {
            color: var(--color-text);
            border: 1px solid var(--color-border);
            background: color-mix(in srgb, var(--color-surface) 88%, transparent);
        }

        .hero {
            position: relative;
            padding: clamp(5.5rem, 9vw, 8rem) 0 var(--space-24);
            overflow: clip;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.04fr .96fr;
            gap: clamp(var(--space-8), 5vw, var(--space-16));
            align-items: center;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .7rem;
            padding: .55rem .9rem;
            margin-bottom: var(--space-6);
            border-radius: var(--radius-full);
            border: 1px solid rgba(102,245,215,.18);
            background: rgba(102,245,215,.08);
            color: var(--color-primary);
            font-size: var(--text-xs);
            text-transform: uppercase;
            letter-spacing: .18em;
        }
        .eyebrow::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
            box-shadow: 0 0 14px currentColor;
            animation: signal-pulse 2s ease-in-out infinite;
        }

        .hero-copy h1 {
            font-size: var(--text-3xl);
            max-width: 10.5ch;
            margin-bottom: var(--space-6);
        }
        .hero-copy .lead {
            font-size: var(--text-lg);
            margin-bottom: var(--space-8);
            max-width: 55ch;
        }
        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: var(--space-4);
            margin-bottom: var(--space-10);
        }

        .trust-row {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: var(--space-4);
            max-width: 46rem;
        }
        .trust-card {
            padding: 1rem 1.05rem;
            border-radius: 18px;
            border: 1px solid var(--color-border);
            background: color-mix(in srgb, var(--color-surface) 90%, transparent);
            box-shadow: var(--shadow-sm);
        }
        .trust-card strong {
            display: block;
            font-size: 1.35rem;
            color: var(--color-text);
            margin-bottom: .15rem;
        }
        .trust-card span {
            font-size: var(--text-xs);
            color: var(--color-text-faint);
            text-transform: uppercase;
            letter-spacing: .12em;
        }

        .hero-visual {
            position: relative;
            min-height: 620px;
        }

        .command-shell {
            position: relative;
            height: 100%;
            min-height: 620px;
            border-radius: 34px;
            padding: 1rem;
            overflow: hidden;
            border: 1px solid rgba(181,203,255,.12);
            background:
                linear-gradient(180deg, rgba(255,255,255,.05), rgba(255,255,255,.015)),
                rgba(6, 11, 22, .9);
            box-shadow: var(--shadow-lg);
            isolation: isolate;
        }
        .command-shell::before {
            content: "";
            position: absolute;
            inset: -20%;
            background:
                radial-gradient(circle at 30% 30%, rgba(102,245,215,.14), transparent 26%),
                radial-gradient(circle at 75% 20%, rgba(127,141,255,.16), transparent 24%),
                radial-gradient(circle at 50% 80%, rgba(159,115,255,.1), transparent 28%);
            filter: blur(40px);
            animation: ambient-float 10s ease-in-out infinite;
            z-index: -1;
        }

        .hero-topline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: .85rem 1rem;
            border-radius: 22px;
            border: 1px solid rgba(255,255,255,.06);
            background: rgba(255,255,255,.03);
            margin-bottom: 1rem;
        }
        .topline-title {
            display: flex;
            align-items: center;
            gap: .7rem;
            color: var(--color-text-muted);
            font-size: var(--text-xs);
            text-transform: uppercase;
            letter-spacing: .18em;
        }
        .status-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: var(--color-success);
            box-shadow: 0 0 14px var(--color-success);
            animation: signal-pulse 1.8s ease-in-out infinite;
        }

        .hero-stage {
            position: relative;
            height: calc(100% - 72px);
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 1rem;
        }

        .matrix-panel,
        .stack-panel {
            border-radius: 28px;
            border: 1px solid rgba(255,255,255,.06);
            background:
                linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02)),
                rgba(255,255,255,.015);
            overflow: hidden;
            position: relative;
        }

        .matrix-panel {
            padding: 1.25rem;
            display: grid;
            grid-template-rows: auto 1fr auto;
        }

        .signal-grid {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            gap: .5rem;
            margin-top: 1rem;
            align-content: start;
        }
        .signal-grid span {
            aspect-ratio: 1;
            border-radius: 12px;
            background:
                linear-gradient(180deg, rgba(255,255,255,.07), rgba(255,255,255,.02)),
                rgba(255,255,255,.02);
            border: 1px solid rgba(255,255,255,.05);
            animation: grid-flicker 4.5s ease-in-out infinite;
            animation-delay: calc(var(--i) * .08s);
        }

        .matrix-core {
            position: absolute;
            inset: 50% auto auto 50%;
            width: 180px;
            height: 180px;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            background:
                radial-gradient(circle at 30% 30%, rgba(255,255,255,.95), rgba(102,245,215,.65) 24%, rgba(127,141,255,.38) 56%, rgba(8,16,26,0) 70%);
            filter: blur(4px);
            box-shadow:
                0 0 80px rgba(102,245,215,.18),
                0 0 120px rgba(127,141,255,.12);
            animation: core-breathe 5s ease-in-out infinite;
            pointer-events: none;
        }

        .orbit-ring,
        .orbit-ring::before,
        .orbit-ring::after {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(181,203,255,.12);
            content: "";
        }
        .orbit-ring {
            inset: 50% auto auto 50%;
            width: 260px;
            height: 260px;
            transform: translate(-50%, -50%);
            animation: rotate-slow 18s linear infinite;
        }
        .orbit-ring::before {
            inset: 22px;
        }
        .orbit-ring::after {
            inset: 54px;
        }

        .orbit-dot,
        .orbit-dot.secondary {
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            box-shadow: 0 0 16px currentColor;
        }
        .orbit-dot {
            top: 18px;
            left: 50px;
            background: var(--color-primary);
            color: var(--color-primary);
        }
        .orbit-dot.secondary {
            bottom: 38px;
            right: 44px;
            background: var(--color-accent);
            color: var(--color-accent);
        }

        .matrix-footer {
            display: flex;
            gap: .75rem;
            margin-top: 1rem;
        }
        .micro-card {
            flex: 1;
            padding: .9rem 1rem;
            border-radius: 18px;
            background: rgba(255,255,255,.03);
            border: 1px solid rgba(255,255,255,.05);
        }
        .micro-card strong {
            display: block;
            font-size: 1.15rem;
            color: var(--color-text);
            margin-bottom: .2rem;
        }
        .micro-card span {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .14em;
            color: var(--color-text-faint);
        }

        .stack-panel {
            padding: 1rem;
            display: grid;
            gap: .8rem;
            align-content: start;
        }

        .stack-card {
            padding: 1rem;
            border-radius: 20px;
            background:
                linear-gradient(180deg, rgba(255,255,255,.05), rgba(255,255,255,.02)),
                rgba(255,255,255,.018);
            border: 1px solid rgba(255,255,255,.06);
            transform: translateY(0);
            transition: transform var(--transition-interactive), border-color var(--transition-interactive), background var(--transition-interactive);
        }
        .stack-card:hover {
            transform: translateY(-2px);
            border-color: rgba(102,245,215,.16);
            background: rgba(255,255,255,.035);
        }
        .stack-card span {
            display: block;
            color: var(--color-text-faint);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .15em;
            margin-bottom: .55rem;
        }
        .stack-card strong {
            display: block;
            color: var(--color-text);
            font-size: 1.06rem;
            margin-bottom: .35rem;
        }
        .stack-card p {
            font-size: var(--text-sm);
        }

        .section {
            padding: clamp(var(--space-12), 8vw, var(--space-24)) 0;
        }
        .section-head {
            display: grid;
            grid-template-columns: .8fr 1.2fr;
            gap: var(--space-8);
            align-items: end;
            margin-bottom: var(--space-10);
        }
        .section-kicker {
            color: var(--color-primary);
            font-size: var(--text-xs);
            text-transform: uppercase;
            letter-spacing: .18em;
            margin-bottom: var(--space-3);
        }
        .section-head h2 {
            font-size: var(--text-2xl);
            margin-bottom: var(--space-4);
        }

        .editorial-grid {
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            gap: var(--space-5);
        }

        .panel {
            position: relative;
            padding: clamp(1.25rem, 2vw, 2rem);
            border-radius: 30px;
            border: 1px solid var(--color-border);
            background:
                linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.015)),
                color-mix(in srgb, var(--color-surface) 90%, transparent);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .feature-ledger {
            display: grid;
            gap: .9rem;
            margin-top: var(--space-6);
        }
        .feature-ledger li {
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: .9rem;
            padding: 1rem 1rem;
            border-radius: 18px;
            border: 1px solid rgba(255,255,255,.06);
            background: rgba(255,255,255,.025);
        }
        .feature-ledger em {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: rgba(102,245,215,.1);
            color: var(--color-primary);
            font-style: normal;
            font-size: 12px;
            font-weight: 800;
        }
        .feature-ledger strong {
            font-size: var(--text-sm);
            color: var(--color-text);
        }
        .feature-ledger span {
            color: var(--color-text-faint);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .14em;
        }

        .radar-box {
            min-height: 100%;
            display: grid;
            place-items: center;
        }
        .radar {
            width: min(360px, 88%);
            aspect-ratio: 1;
            position: relative;
            border-radius: 50%;
            border: 1px solid rgba(181,203,255,.12);
            background:
                radial-gradient(circle, rgba(102,245,215,.08), transparent 44%),
                linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,.01));
            box-shadow: inset 0 0 80px rgba(102,245,215,.05);
            animation: rotate-slow 16s linear infinite;
        }
        .radar::before,
        .radar::after {
            content: "";
            position: absolute;
            inset: 18%;
            border-radius: 50%;
            border: 1px solid rgba(181,203,255,.12);
        }
        .radar::after {
            inset: 34%;
        }
        .radar-sweep {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: conic-gradient(from 90deg, rgba(102,245,215,.26), transparent 28%, transparent 100%);
            filter: blur(4px);
            animation: rotate-slow 4s linear infinite;
        }
        .radar-point,
        .radar-point.two,
        .radar-point.three {
            position: absolute;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--color-primary);
            box-shadow: 0 0 18px rgba(102,245,215,.4);
        }
        .radar-point { top: 20%; left: 58%; }
        .radar-point.two { top: 60%; left: 26%; background: var(--color-secondary); }
        .radar-point.three { top: 72%; left: 70%; background: var(--color-accent); }

        .timeline {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: var(--space-4);
        }
        .timeline-card {
            padding: var(--space-6);
            border-radius: 26px;
            border: 1px solid var(--color-border);
            background: color-mix(in srgb, var(--color-surface) 92%, transparent);
            box-shadow: var(--shadow-sm);
        }
        .timeline-card .no {
            width: 36px;
            height: 36px;
            margin-bottom: var(--space-4);
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: rgba(102,245,215,.1);
            color: var(--color-primary);
            font-size: 12px;
            font-weight: 800;
        }
        .timeline-card h3 {
            font-size: 1.08rem;
            margin-bottom: .5rem;
        }

        .quote-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: var(--space-5);
        }
        .quote-box,
        .stat-box {
            padding: clamp(1.25rem, 2vw, 2rem);
            border-radius: 30px;
            border: 1px solid var(--color-border);
            background:
                linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.015)),
                color-mix(in srgb, var(--color-surface) 90%, transparent);
            box-shadow: var(--shadow-sm);
        }
        .quote-box blockquote {
            font-family: var(--font-display);
            font-size: clamp(1.7rem, 1rem + 2vw, 2.8rem);
            line-height: 1.08;
            max-width: 18ch;
            margin-bottom: var(--space-8);
        }
        .quote-box footer {
            color: var(--color-text-faint);
            font-size: var(--text-sm);
            text-transform: uppercase;
            letter-spacing: .14em;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: var(--space-4);
        }
        .stat-grid article {
            padding: var(--space-5);
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,.06);
            background: rgba(255,255,255,.025);
        }
        .stat-grid strong {
            display: block;
            color: var(--color-text);
            font-size: 1.9rem;
            margin-bottom: .25rem;
        }
        .stat-grid span {
            color: var(--color-text-faint);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .14em;
        }

        .cta-shell {
            position: relative;
            overflow: hidden;
            padding: clamp(1.5rem, 4vw, 4rem);
            border-radius: 36px;
            border: 1px solid rgba(102,245,215,.16);
            background:
                radial-gradient(circle at 14% 18%, rgba(102,245,215,.12), transparent 22%),
                radial-gradient(circle at 84% 74%, rgba(127,141,255,.16), transparent 22%),
                linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.015)),
                color-mix(in srgb, var(--color-surface) 90%, transparent);
            box-shadow: var(--shadow-md);
        }
        .cta-shell h2 {
            font-size: clamp(2rem, 1.2rem + 2.8vw, 4rem);
            max-width: 12ch;
            margin-bottom: var(--space-5);
        }
        .cta-shell p {
            margin-bottom: var(--space-8);
            max-width: 54ch;
        }

        .site-footer {
            padding: var(--space-10) 0 var(--space-12);
            color: var(--color-text-faint);
        }
        .footer-shell {
            display: flex;
            justify-content: space-between;
            gap: var(--space-6);
            align-items: center;
            padding-top: var(--space-6);
            border-top: 1px solid var(--color-divider);
        }

        [data-reveal] {
            opacity: 0;
            transform: translateY(22px);
            filter: blur(8px);
            transition:
                opacity .8s var(--ease-enter),
                transform .8s var(--ease-enter),
                filter .8s var(--ease-enter);
            transition-delay: var(--delay, 0ms);
        }
        [data-reveal].is-visible {
            opacity: 1;
            transform: translateY(0);
            filter: blur(0);
        }

        .mobile-panel {
            display: none;
        }
        .mobile-panel.is-open {
            display: block;
            padding-bottom: var(--space-4);
        }
        .mobile-panel nav {
            display: grid;
            gap: .65rem;
            padding-top: .65rem;
        }
        .mobile-panel a {
            padding: 1rem 1.05rem;
            border-radius: 18px;
            border: 1px solid var(--color-border);
            background: color-mix(in srgb, var(--color-surface) 92%, transparent);
            color: var(--color-text-muted);
        }

        /* Chat widget */
        .chat-widget {
            position: fixed;
            right: 22px;
            bottom: 22px;
            z-index: 240;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 14px;
            pointer-events: none;
        }
        .chat-launcher-wrap,
        .chat-window {
            pointer-events: auto;
        }
        .chat-launcher-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .chat-launcher-label {
            padding: .9rem 1rem;
            border-radius: 999px;
            white-space: nowrap;
            font-size: var(--text-sm);
            color: var(--color-text);
            border: 1px solid var(--color-border);
            background: color-mix(in srgb, var(--color-surface) 88%, transparent);
            box-shadow: var(--shadow-md);
            backdrop-filter: blur(16px);
            transition: opacity .22s var(--ease-enter), transform .22s var(--ease-enter);
        }
        .chat-widget.compact .chat-launcher-label {
            opacity: 0;
            transform: scale(.92);
            width: 0;
            overflow: hidden;
            padding-inline: 0;
            border-width: 0;
        }

        .chat-launcher {
            width: 70px;
            height: 70px;
            position: relative;
            border-radius: 50%;
            display: grid;
            place-items: center;
            border: 1px solid rgba(102,245,215,.22);
            background:
                radial-gradient(circle at 28% 28%, rgba(102,245,215,.28), transparent 36%),
                linear-gradient(145deg, rgba(102,245,215,.14), rgba(127,141,255,.18)),
                color-mix(in srgb, var(--color-surface) 90%, transparent);
            box-shadow:
                0 18px 40px rgba(0,0,0,.3),
                0 0 0 8px rgba(102,245,215,.05);
            transition: transform var(--transition-interactive), box-shadow var(--transition-interactive);
            isolation: isolate;
        }
        .chat-launcher:hover {
            transform: translateY(-2px) scale(1.02);
        }
        .chat-launcher-ring::before,
        .chat-launcher-ring::after {
            content: "";
            position: absolute;
            inset: -5px;
            border-radius: 50%;
            border: 1px solid rgba(102,245,215,.24);
            animation: launcher-ping 3s ease-out infinite;
        }
        .chat-launcher-ring::after {
            inset: -13px;
            border-color: rgba(127,141,255,.18);
            animation-delay: .55s;
        }

        .ai-face {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background:
                radial-gradient(circle at 32% 30%, #c4fff5 0%, #7bf5dd 32%, #1ea58a 60%, #0a1722 100%);
            box-shadow: inset 0 -8px 18px rgba(0,0,0,.2), 0 0 18px rgba(102,245,215,.3);
            overflow: hidden;
        }
        .ai-face::before,
        .ai-face::after {
            content: "";
            position: absolute;
            top: 14px;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #07131b;
            animation: ai-blink 5s infinite;
        }
        .ai-face::before { left: 10px; }
        .ai-face::after { right: 10px; }
        .ai-face-mouth {
            position: absolute;
            left: 50%;
            bottom: 9px;
            width: 14px;
            height: 7px;
            transform: translateX(-50%);
            border-bottom: 2px solid #08141d;
            border-radius: 0 0 16px 16px;
            animation: ai-smile 3.2s ease-in-out infinite;
        }

        .chat-notify {
            position: absolute;
            top: 0;
            right: 0;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #ff936f, #ff5d8b);
            color: white;
            font-size: 11px;
            font-weight: 800;
            box-shadow: 0 8px 18px rgba(255,93,139,.32);
        }

        .chat-window {
            width: min(390px, calc(100vw - 20px));
            height: min(680px, calc(100vh - 110px));
            display: grid;
            grid-template-rows: auto 1fr auto;
            border-radius: 28px;
            overflow: hidden;
            border: 1px solid rgba(181,203,255,.14);
            background:
                linear-gradient(180deg, rgba(255,255,255,.05), rgba(255,255,255,.015)),
                rgba(8, 12, 24, .94);
            box-shadow: 0 30px 80px rgba(0,0,0,.44), inset 0 1px 0 rgba(255,255,255,.05);
            backdrop-filter: blur(22px);
            opacity: 0;
            visibility: hidden;
            transform: translateY(16px) scale(.97);
            transform-origin: bottom right;
            transition: opacity .28s var(--ease-enter), transform .28s var(--ease-enter), visibility .28s step-end;
        }
        .chat-widget.open .chat-window {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
            transition: opacity .34s var(--ease-enter), transform .34s var(--ease-enter), visibility 0s;
        }

        .chat-header {
            padding: 1rem 1rem .95rem;
            border-bottom: 1px solid rgba(255,255,255,.06);
            background:
                radial-gradient(circle at top left, rgba(102,245,215,.12), transparent 28%),
                radial-gradient(circle at top right, rgba(127,141,255,.12), transparent 22%),
                rgba(255,255,255,.02);
        }
        .chat-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
        }
        .chat-agent {
            display: flex;
            align-items: center;
            gap: .8rem;
        }
        .chat-agent-meta strong {
            display: block;
            font-size: var(--text-sm);
            color: var(--color-text);
        }
        .chat-agent-meta span {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            font-size: var(--text-xs);
            color: var(--color-text-faint);
            text-transform: uppercase;
            letter-spacing: .14em;
        }
        .chat-agent-meta span::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--color-success);
            box-shadow: 0 0 10px var(--color-success);
            animation: signal-pulse 1.8s ease-in-out infinite;
        }

        .chat-controls {
            display: flex;
            gap: .5rem;
        }
        .chat-icon-btn {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: var(--color-text-muted);
            border: 1px solid rgba(255,255,255,.08);
            background: rgba(255,255,255,.03);
            transition: transform var(--transition-interactive), background var(--transition-interactive), color var(--transition-interactive);
        }
        .chat-icon-btn:hover {
            transform: translateY(-1px);
            background: rgba(255,255,255,.07);
            color: var(--color-text);
        }

        .chat-subline {
            margin-top: .8rem;
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }
        .chat-chip {
            padding: .45rem .7rem;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.06);
            background: rgba(255,255,255,.025);
            color: var(--color-text-faint);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .12em;
        }

        .chat-messages {
            padding: 1rem;
            overflow: auto;
            display: flex;
            flex-direction: column;
            gap: .9rem;
            scroll-behavior: smooth;
        }
        .chat-messages::-webkit-scrollbar { width: 8px; }
        .chat-messages::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,.08);
            border-radius: 999px;
        }

        .chat-row {
            display: flex;
            align-items: flex-end;
            gap: .7rem;
            animation: msg-in .34s var(--ease-enter);
        }
        .chat-row.user {
            justify-content: flex-end;
        }

        .chat-avatar {
            width: 38px;
            height: 38px;
            flex: 0 0 auto;
            border-radius: 50%;
            overflow: hidden;
            position: relative;
            box-shadow: 0 10px 24px rgba(0,0,0,.22);
        }
        .chat-avatar.ai {
            background:
                radial-gradient(circle at 32% 30%, #c1fff5 0%, #76f3dc 34%, #17967f 60%, #0a1621 100%);
            border: 1px solid rgba(102,245,215,.18);
        }
        .chat-avatar.ai::before,
        .chat-avatar.ai::after {
            content: "";
            position: absolute;
            top: 14px;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #07131b;
        }
        .chat-avatar.ai::before { left: 10px; }
        .chat-avatar.ai::after { right: 10px; }
        .chat-avatar.ai .mini-mouth {
            position: absolute;
            left: 50%;
            bottom: 8px;
            width: 12px;
            height: 6px;
            transform: translateX(-50%);
            border-bottom: 2px solid #09131c;
            border-radius: 0 0 12px 12px;
        }

        .chat-avatar.user {
            background: linear-gradient(145deg, #7f8dff, #9f73ff);
            border: 1px solid rgba(127,141,255,.22);
        }
        .chat-avatar.user::before {
            content: "";
            position: absolute;
            left: 50%;
            top: 8px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            transform: translateX(-50%);
            background: rgba(255,255,255,.94);
        }
        .chat-avatar.user::after {
            content: "";
            position: absolute;
            left: 50%;
            bottom: 6px;
            width: 20px;
            height: 12px;
            border-radius: 12px 12px 6px 6px;
            transform: translateX(-50%);
            background: rgba(255,255,255,.94);
        }

        .chat-bubble {
            max-width: 78%;
            padding: .9rem 1rem;
            border-radius: 18px;
            font-size: var(--text-sm);
            line-height: 1.6;
            border: 1px solid rgba(255,255,255,.06);
        }
        .chat-row.ai .chat-bubble {
            color: var(--color-text);
            background:
                linear-gradient(180deg, rgba(255,255,255,.05), rgba(255,255,255,.02)),
                rgba(255,255,255,.02);
            border-bottom-left-radius: 6px;
        }
        .chat-row.user .chat-bubble {
            color: #06120d;
            background: linear-gradient(135deg, var(--color-primary), #acffef);
            border-bottom-right-radius: 6px;
            box-shadow: 0 12px 28px rgba(102,245,215,.2);
        }
        .chat-bubble time {
            display: block;
            margin-top: .45rem;
            font-size: 11px;
            opacity: .68;
        }

        .chat-typing {
            display: none;
            align-items: flex-end;
            gap: .7rem;
            padding-inline: 1rem;
            padding-bottom: .35rem;
        }
        .chat-typing.show {
            display: flex;
            animation: fade-up .25s var(--ease-enter);
        }
        .typing-bubble {
            display: inline-flex;
            gap: .35rem;
            padding: .75rem .9rem;
            border-radius: 18px;
            border-bottom-left-radius: 6px;
            background: rgba(255,255,255,.03);
            border: 1px solid rgba(255,255,255,.06);
        }
        .typing-bubble span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,.56);
            animation: typing-bounce 1.1s infinite ease-in-out;
        }
        .typing-bubble span:nth-child(2) { animation-delay: .12s; }
        .typing-bubble span:nth-child(3) { animation-delay: .24s; }

        .chat-quick-actions {
            padding: .2rem 1rem .9rem;
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }
        .quick-prompt {
            padding: .62rem .8rem;
            border-radius: 999px;
            font-size: 12px;
            color: var(--color-text-muted);
            border: 1px solid rgba(255,255,255,.06);
            background: rgba(255,255,255,.025);
            transition: transform var(--transition-interactive), background var(--transition-interactive), color var(--transition-interactive);
        }
        .quick-prompt:hover {
            transform: translateY(-1px);
            background: rgba(255,255,255,.05);
            color: var(--color-text);
        }

        .chat-form-wrap {
            border-top: 1px solid rgba(255,255,255,.06);
            padding: .9rem;
            background:
                linear-gradient(180deg, rgba(255,255,255,.025), rgba(255,255,255,.01)),
                rgba(7, 11, 21, .95);
        }
        .chat-form {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: .7rem;
            align-items: end;
        }
        .chat-input-shell {
            padding: .72rem .85rem;
            border-radius: 22px;
            border: 1px solid rgba(255,255,255,.08);
            background: rgba(255,255,255,.035);
            transition: border-color var(--transition-interactive), box-shadow var(--transition-interactive), background var(--transition-interactive);
        }
        .chat-input-shell:focus-within {
            border-color: rgba(102,245,215,.28);
            box-shadow: 0 0 0 4px rgba(102,245,215,.08);
            background: rgba(255,255,255,.045);
        }
        .chat-input {
            width: 100%;
            min-height: 48px;
            max-height: 120px;
            outline: none;
            border: none;
            background: transparent;
            resize: none;
            font-size: var(--text-sm);
            line-height: 1.55;
            color: var(--color-text);
        }
        .chat-input::placeholder {
            color: var(--color-text-faint);
        }
        .chat-send {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: #05120d;
            background: linear-gradient(135deg, var(--color-primary), #aaffed);
            box-shadow: 0 12px 28px rgba(102,245,215,.22);
            transition: transform var(--transition-interactive), box-shadow var(--transition-interactive), filter var(--transition-interactive);
        }
        .chat-send:hover {
            transform: translateY(-1px) scale(1.02);
            box-shadow: 0 16px 32px rgba(102,245,215,.28);
        }
        .chat-send:disabled {
            opacity: .55;
            box-shadow: none;
            filter: grayscale(.1);
            cursor: not-allowed;
        }
        .chat-helper {
            display: flex;
            justify-content: space-between;
            gap: .75rem;
            margin-top: .6rem;
            color: var(--color-text-faint);
            font-size: 11px;
        }

        @keyframes signal-pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(.82); opacity: .72; }
        }
        @keyframes ambient-float {
            0%, 100% { transform: translate3d(0,0,0) scale(1); }
            50% { transform: translate3d(0,-18px,0) scale(1.02); }
        }
        @keyframes core-breathe {
            0%, 100% { transform: translate(-50%, -50%) scale(1); }
            50% { transform: translate(-50%, -50%) scale(1.08); }
        }
        @keyframes rotate-slow {
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }
        @keyframes grid-flicker {
            0%, 100% { opacity: .32; transform: scale(1); }
            50% { opacity: .95; transform: scale(1.04); }
        }
        @keyframes launcher-ping {
            0% { transform: scale(.86); opacity: 0; }
            35% { opacity: .42; }
            100% { transform: scale(1.28); opacity: 0; }
        }
        @keyframes ai-blink {
            0%, 46%, 50%, 100% { transform: scaleY(1); }
            48% { transform: scaleY(.12); }
        }
        @keyframes ai-smile {
            0%, 100% { width: 14px; }
            50% { width: 18px; }
        }
        @keyframes typing-bounce {
            0%, 80%, 100% { transform: translateY(0); opacity: .45; }
            40% { transform: translateY(-5px); opacity: 1; }
        }
        @keyframes msg-in {
            from { opacity: 0; transform: translateY(8px) scale(.985); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes fade-up {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 1080px) {
            .hero-grid,
            .section-head,
            .editorial-grid,
            .quote-grid,
            .timeline,
            .hero-stage {
                grid-template-columns: 1fr;
            }
            .hero-visual,
            .command-shell {
                min-height: 540px;
            }
        }

        @media (max-width: 860px) {
            .nav-links { display: none; }
            .menu-toggle { display: grid; }
            .trust-row,
            .stat-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (min-width: 861px) {
            .menu-toggle,
            .mobile-panel {
                display: none !important;
            }
        }

        @media (max-width: 640px) {
            .hero {
                padding-top: 5rem;
            }
            .hero-copy h1 {
                max-width: 9ch;
            }
            .hero-actions {
                flex-direction: column;
                align-items: stretch;
            }
            .btn {
                width: 100%;
            }
            .footer-shell {
                flex-direction: column;
                align-items: flex-start;
            }
            .panel,
            .quote-box,
            .stat-box,
            .cta-shell,
            .command-shell {
                border-radius: 24px;
                padding: 1.1rem;
            }
            .chat-widget {
                right: 12px;
                bottom: 12px;
                left: 12px;
                align-items: stretch;
            }
            .chat-launcher-wrap {
                align-self: flex-end;
            }
            .chat-window {
                width: 100%;
                height: min(72vh, 620px);
                border-radius: 24px;
            }
            .chat-launcher {
                width: 64px;
                height: 64px;
            }
            .chat-bubble {
                max-width: 84%;
            }
            .chat-helper {
                flex-direction: column;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
                scroll-behavior: auto !important;
            }
            [data-reveal] {
                opacity: 1 !important;
                transform: none !important;
                filter: none !important;
            }
        }
    </style>
</head>
<body>
<a href="#main" class="skip-link">Skip to content</a>

<header class="site-header" id="siteHeader">
    <div class="container">
        <div class="nav-shell">
            <a href="#top" class="brand" aria-label="AI Relm home">
                    <span class="brand-mark" aria-hidden="true">
                        <svg viewBox="0 0 48 48" width="24" height="24" fill="none">
                            <path d="M24 6 39 15v18L24 42 9 33V15L24 6Z" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M18 19.5 24 16l6 3.5v9L24 32l-6-3.5v-9Z" fill="currentColor" opacity=".92"/>
                        </svg>
                    </span>
                <span class="brand-text">
                        <strong>AI Relm</strong>
                        <span>Elite AI Product Systems</span>
                    </span>
            </a>

            <nav class="nav-links" aria-label="Primary navigation">
                <a href="#capabilities">Capabilities</a>
                <a href="#architecture">Architecture</a>
                <a href="#process">Process</a>
                <a href="#contact">Contact</a>
            </nav>

            <div class="header-actions">
                <button class="theme-toggle" type="button" data-theme-toggle aria-label="Switch theme">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                </button>
                <a href="#contact" class="btn btn-secondary">Book Discovery</a>
                <button class="menu-toggle" type="button" data-menu-toggle aria-label="Open menu">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 7h16M4 12h16M4 17h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="mobile-panel" id="mobilePanel">
            <nav aria-label="Mobile navigation">
                <a href="#capabilities">Capabilities</a>
                <a href="#architecture">Architecture</a>
                <a href="#process">Process</a>
                <a href="#contact">Contact</a>
            </nav>
        </div>
    </div>
</header>

<main id="main">
    <section class="hero" id="top">
        <div class="container hero-grid">
            <div class="hero-copy">
                <div class="eyebrow" data-reveal>AI-native systems for ambitious companies</div>
                <h1 data-reveal style="--delay:120ms;">Build a homepage that feels like a live intelligence engine.</h1>
                <p class="lead" data-reveal style="--delay:220ms;">
                    AI Relm combines premium product design, agent workflows, and elite interface systems to make your AI brand look trustworthy, powerful, and conversion-ready from the first second.
                </p>

                <div class="hero-actions" data-reveal style="--delay:320ms;">
                    <a href="#contact" class="btn btn-primary">Start Your AI Build</a>
                    <a href="#capabilities" class="btn btn-secondary">See Capabilities</a>
                </div>

                <div class="trust-row" data-reveal style="--delay:420ms;">
                    <article class="trust-card">
                        <strong>4x</strong>
                        <span>Stronger perceived quality</span>
                    </article>
                    <article class="trust-card">
                        <strong>48h</strong>
                        <span>Rapid premium prototype</span>
                    </article>
                    <article class="trust-card">
                        <strong>99%</strong>
                        <span>Trust-first surface clarity</span>
                    </article>
                </div>
            </div>

            <div class="hero-visual" data-reveal style="--delay:240ms;">
                <div class="command-shell">
                    <div class="hero-topline">
                        <div class="topline-title">
                            <span class="status-dot"></span>
                            <span>Neural Command Interface</span>
                        </div>
                        <span class="topline-title">Realtime orchestration</span>
                    </div>

                    <div class="hero-stage">
                        <div class="matrix-panel">
                            <div class="topline-title">System map</div>

                            <div class="signal-grid" aria-hidden="true">
                                @for ($i = 1; $i <= 50; $i++)
                                    <span style="--i: {{ $i }}"></span>
                                @endfor
                            </div>

                            <div class="matrix-core" aria-hidden="true"></div>
                            <div class="orbit-ring" aria-hidden="true">
                                <span class="orbit-dot"></span>
                                <span class="orbit-dot secondary"></span>
                            </div>

                            <div class="matrix-footer">
                                <article class="micro-card">
                                    <strong>Agent mesh</strong>
                                    <span>reasoning layer</span>
                                </article>
                                <article class="micro-card">
                                    <strong>UI relay</strong>
                                    <span>signal design</span>
                                </article>
                            </div>
                        </div>

                        <aside class="stack-panel">
                            <article class="stack-card">
                                <span>Brand layer</span>
                                <strong>Authority-driven presence</strong>
                                <p>Position the product like a premium platform, not a generic AI tool.</p>
                            </article>
                            <article class="stack-card">
                                <span>Agent layer</span>
                                <strong>Workflow intelligence</strong>
                                <p>Show routing, memory, tool calling, and context as one coherent system.</p>
                            </article>
                            <article class="stack-card">
                                <span>Interface layer</span>
                                <strong>Conversion through confidence</strong>
                                <p>Users trust calm, structured, elegant interaction far more than loud effects.</p>
                            </article>
                            <article class="stack-card">
                                <span>Execution layer</span>
                                <strong>Laravel-ready foundations</strong>
                                <p>Everything is prepared for Blade pages, APIs, and real production flows.</p>
                            </article>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="capabilities">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="section-kicker" data-reveal>Capabilities</div>
                    <h2 data-reveal style="--delay:120ms;">A different UI direction: less template, more command-level product identity.</h2>
                </div>
                <p data-reveal style="--delay:220ms;">
                    This page avoids generic centered SaaS patterns and instead uses editorial composition, signal-rich panels, and varied density to feel more custom and elite.
                </p>
            </div>

            <div class="editorial-grid">
                <article class="panel" data-reveal>
                    <h3 style="font-size: 1.75rem; margin-bottom: .75rem;">Premium interface language</h3>
                    <p>Use structure, rhythm, depth, and motion to communicate intelligence without making the page visually chaotic.</p>

                    <ul class="feature-ledger">
                        <li>
                            <em>01</em>
                            <div>
                                <strong>Hero that sells system value</strong>
                                <p>A strong headline, one supporting message, and one clear CTA.</p>
                            </div>
                            <span>identity</span>
                        </li>
                        <li>
                            <em>02</em>
                            <div>
                                <strong>Visual intelligence surfaces</strong>
                                <p>Panels, grids, and signal layers that imply a live operational product.</p>
                            </div>
                            <span>motion</span>
                        </li>
                        <li>
                            <em>03</em>
                            <div>
                                <strong>Built-in AI conversation point</strong>
                                <p>A bottom-right chat widget that feels familiar and premium.</p>
                            </div>
                            <span>chat ui</span>
                        </li>
                    </ul>
                </article>

                <article class="panel radar-box" data-reveal style="--delay:140ms;">
                    <div class="radar" aria-hidden="true">
                        <div class="radar-sweep"></div>
                        <span class="radar-point"></span>
                        <span class="radar-point two"></span>
                        <span class="radar-point three"></span>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="section" id="architecture">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="section-kicker" data-reveal>Architecture</div>
                    <h2 data-reveal style="--delay:120ms;">The landing page tells users how the AI system thinks before they even ask.</h2>
                </div>
                <p data-reveal style="--delay:220ms;">
                    The goal is not decoration. The goal is to make the interface itself feel like evidence that the platform is engineered well.
                </p>
            </div>

            <div class="timeline">
                <article class="timeline-card" data-reveal>
                    <div class="no">01</div>
                    <h3>Signal</h3>
                    <p>Establish authority, clarity, and ambition immediately.</p>
                </article>
                <article class="timeline-card" data-reveal style="--delay:90ms;">
                    <div class="no">02</div>
                    <h3>Structure</h3>
                    <p>Turn abstract AI talk into visible product layers and roles.</p>
                </article>
                <article class="timeline-card" data-reveal style="--delay:180ms;">
                    <div class="no">03</div>
                    <h3>Trust</h3>
                    <p>Use clear surfaces, compact proof, and refined chat behavior.</p>
                </article>
                <article class="timeline-card" data-reveal style="--delay:270ms;">
                    <div class="no">04</div>
                    <h3>Conversion</h3>
                    <p>Guide users toward contact, chat, or project inquiry without clutter.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="section" id="process">
        <div class="container quote-grid">
            <article class="quote-box" data-reveal>
                <blockquote>
                    “The best AI landing pages do not shout that they are advanced. They make advanced systems feel calm, clear, and obvious.”
                </blockquote>
                <footer>AI Relm / interface philosophy</footer>
            </article>

            <article class="stat-box" data-reveal style="--delay:120ms;">
                <div class="stat-grid">
                    <article>
                        <strong>12d</strong>
                        <span>Full design-to-live sprint</span>
                    </article>
                    <article>
                        <strong>3x</strong>
                        <span>More qualified intent</span>
                    </article>
                    <article>
                        <strong>1</strong>
                        <span>Primary conversion goal</span>
                    </article>
                    <article>
                        <strong>24/7</strong>
                        <span>AI widget readiness</span>
                    </article>
                </div>
            </article>
        </div>
    </section>

    <section class="section" id="contact">
        <div class="container">
            <div class="cta-shell" data-reveal>
                <div class="section-kicker">Start now</div>
                <h2>Turn your AI product into a high-trust premium brand surface.</h2>
                <p>
                    This Blade page is designed to be your public-facing AI identity layer and can connect directly to Laravel APIs, real chat models, lead capture, or internal orchestration services.
                </p>
                <div class="hero-actions">
                    <a href="mailto:hello@airelm.com" class="btn btn-primary">Start a project</a>
                    <button type="button" class="btn btn-secondary" id="openChatFromCta">Chat with AI</button>
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="site-footer">
    <div class="container footer-shell">
        <div>© {{ date('Y') }} AI Relm. Elite-grade AI product presence.</div>
        <div>Laravel Blade · animated UI · production-ready front layer</div>
    </div>
</footer>

<div class="chat-widget compact" id="chatWidget">
    <div class="chat-window" id="chatWindow" aria-live="polite" aria-label="AI chat widget">
        <div class="chat-header">
            <div class="chat-header-top">
                <div class="chat-agent">
                    <div class="chat-avatar ai" aria-hidden="true">
                        <span class="mini-mouth"></span>
                    </div>
                    <div class="chat-agent-meta">
                        <strong>AI Relm Assistant</strong>
                        <span>AI online</span>
                    </div>
                </div>

                <div class="chat-controls">
                    <button class="chat-icon-btn" type="button" id="chatClearBtn" aria-label="Clear chat">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14"/>
                        </svg>
                    </button>
                    <button class="chat-icon-btn" type="button" id="chatMinimizeBtn" aria-label="Minimize chat">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M5 12h14"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="chat-subline">
                <span class="chat-chip">Bottom-right widget</span>
                <span class="chat-chip">Laravel API ready</span>
                <span class="chat-chip">Animated face ui</span>
            </div>
        </div>

        <div class="chat-messages" id="chatMessages">
            <div class="chat-row ai">
                <div class="chat-avatar ai" aria-hidden="true">
                    <span class="mini-mouth"></span>
                </div>
                <div class="chat-bubble">
                    Hi, I’m the AI Relm assistant. Ask about services, pricing, Laravel AI integration, or project workflow.
                    <time>Now</time>
                </div>
            </div>
        </div>

        <div class="chat-typing" id="chatTyping">
            <div class="chat-avatar ai" aria-hidden="true">
                <span class="mini-mouth"></span>
            </div>
            <div class="typing-bubble" aria-label="AI is typing">
                <span></span><span></span><span></span>
            </div>
        </div>

        <div class="chat-quick-actions">
            <button class="quick-prompt" type="button" data-prompt="What AI services do you offer?">AI services</button>
            <button class="quick-prompt" type="button" data-prompt="Can you build AI agents with Laravel API?">Laravel API agents</button>
            <button class="quick-prompt" type="button" data-prompt="How fast can we launch a premium AI landing page?">Launch timeline</button>
        </div>

        <div class="chat-form-wrap">
            <form class="chat-form" id="chatForm">
                <div class="chat-input-shell">
                        <textarea
                            id="chatInput"
                            class="chat-input"
                            rows="1"
                            placeholder="Ask about AI systems, pricing, agents, UX, or Laravel integration..."
                        ></textarea>
                </div>
                <button class="chat-send" id="chatSendBtn" type="submit" aria-label="Send message">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 2 11 13"/>
                        <path d="M22 2 15 22 11 13 2 9 22 2Z"/>
                    </svg>
                </button>
            </form>
            <div class="chat-helper">
                <span>Enter to send · Shift+Enter for newline</span>
                <span>Connected to Laravel API endpoint</span>
            </div>
        </div>
    </div>

    <div class="chat-launcher-wrap">
        <div class="chat-launcher-label">Chat with AI</div>
        <button class="chat-launcher" id="chatLauncher" type="button" aria-label="Open AI chat" aria-expanded="false">
            <span class="chat-launcher-ring" aria-hidden="true"></span>
            <span class="ai-face" aria-hidden="true">
                    <span class="ai-face-mouth"></span>
                </span>
            <span class="chat-notify" id="chatNotify">1</span>
        </button>
    </div>
</div>

<script>
    (() => {
        const root = document.documentElement;
        const themeBtn = document.querySelector('[data-theme-toggle]');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
        let currentTheme = root.getAttribute('data-theme') || (prefersDark.matches ? 'dark' : 'light');

        const setTheme = (theme) => {
            currentTheme = theme;
            root.setAttribute('data-theme', theme);
            if (!themeBtn) return;
            themeBtn.setAttribute('aria-label', theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
            themeBtn.innerHTML = theme === 'dark'
                ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>'
                : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="4.5"></circle><path d="M12 2v2.2M12 19.8V22M4.93 4.93l1.56 1.56M17.51 17.51l1.56 1.56M2 12h2.2M19.8 12H22M4.93 19.07l1.56-1.56M17.51 6.49l1.56-1.56"></path></svg>';
        };

        setTheme(currentTheme);

        themeBtn?.addEventListener('click', () => {
            setTheme(currentTheme === 'dark' ? 'light' : 'dark');
        });

        const header = document.getElementById('siteHeader');
        const onScroll = () => {
            header.classList.toggle('is-scrolled', window.scrollY > 24);
        };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });

        const menuBtn = document.querySelector('[data-menu-toggle]');
        const mobilePanel = document.getElementById('mobilePanel');
        menuBtn?.addEventListener('click', () => {
            const isOpen = mobilePanel.classList.toggle('is-open');
            menuBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        document.querySelectorAll('#mobilePanel a').forEach((link) => {
            link.addEventListener('click', () => mobilePanel.classList.remove('is-open'));
        });

        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.14 });

        document.querySelectorAll('[data-reveal]').forEach((el) => revealObserver.observe(el));

        const chatWidget = document.getElementById('chatWidget');
        const chatLauncher = document.getElementById('chatLauncher');
        const chatMessages = document.getElementById('chatMessages');
        const chatTyping = document.getElementById('chatTyping');
        const chatForm = document.getElementById('chatForm');
        const chatInput = document.getElementById('chatInput');
        const chatSendBtn = document.getElementById('chatSendBtn');
        const chatNotify = document.getElementById('chatNotify');
        const chatMinimizeBtn = document.getElementById('chatMinimizeBtn');
        const chatClearBtn = document.getElementById('chatClearBtn');
        const quickPrompts = document.querySelectorAll('[data-prompt]');
        const openChatFromCta = document.getElementById('openChatFromCta');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        let chatOpen = false;
        let isSending = false;
        let unreadCount = 1;

        const timeNow = () => {
            const d = new Date();
            return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        };

        const scrollChatToBottom = () => {
            requestAnimationFrame(() => {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
        };

        const setUnread = (count) => {
            unreadCount = count;
            if (chatNotify) {
                chatNotify.textContent = String(count);
                chatNotify.style.display = count > 0 ? 'inline-flex' : 'none';
            }
        };

        const openChat = () => {
            chatOpen = true;
            chatWidget.classList.add('open');
            chatWidget.classList.remove('compact');
            chatLauncher.setAttribute('aria-expanded', 'true');
            setUnread(0);
            setTimeout(() => chatInput?.focus(), 180);
            scrollChatToBottom();
        };

        const closeChat = () => {
            chatOpen = false;
            chatWidget.classList.remove('open');
            chatLauncher.setAttribute('aria-expanded', 'false');
        };

        chatLauncher?.addEventListener('click', () => {
            chatOpen ? closeChat() : openChat();
        });

        chatMinimizeBtn?.addEventListener('click', closeChat);
        openChatFromCta?.addEventListener('click', openChat);

        window.addEventListener('scroll', () => {
            if (window.scrollY > 100) {
                chatWidget?.classList.add('compact');
            } else if (!chatOpen) {
                chatWidget?.classList.remove('compact');
            }
        }, { passive: true });

        const autoResize = (el) => {
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 120) + 'px';
        };

        chatInput?.addEventListener('input', () => autoResize(chatInput));
        chatInput?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                chatForm?.requestSubmit();
            }
        });

        const buildAvatar = (role) => {
            if (role === 'user') {
                return `<div class="chat-avatar user" aria-hidden="true"></div>`;
            }
            return `<div class="chat-avatar ai" aria-hidden="true"><span class="mini-mouth"></span></div>`;
        };

        const escapeHtml = (str) => {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        };

        const appendMessage = (role, content, stamp = timeNow()) => {
            const row = document.createElement('div');
            row.className = `chat-row ${role}`;
            row.innerHTML = `
                    ${role === 'ai' ? buildAvatar(role) : ''}
                    <div class="chat-bubble">
                        ${content}
                        <time>${stamp}</time>
                    </div>
                    ${role === 'user' ? buildAvatar(role) : ''}
                `;
            chatMessages.appendChild(row);
            scrollChatToBottom();
        };

        const showTyping = () => {
            chatTyping.classList.add('show');
            scrollChatToBottom();
        };

        const hideTyping = () => {
            chatTyping.classList.remove('show');
        };

        const sendMessage = async (message) => {
            if (!message || isSending) return;

            isSending = true;
            chatSendBtn.disabled = true;

            appendMessage('user', escapeHtml(message));
            chatInput.value = '';
            autoResize(chatInput);
            showTyping();

            try {
                const response = await fetch('/api/ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        message,
                        source: 'welcome-chat',
                        context: {
                            page: 'welcome',
                            brand: 'AI Relm'
                        }
                    })
                });

                const data = await response.json();
                hideTyping();

                const reply =
                    data?.reply ||
                    data?.message ||
                    data?.data?.reply ||
                    'I am online, but the API response format needs mapping.';

                appendMessage('ai', escapeHtml(reply));
            } catch (error) {
                hideTyping();
                appendMessage(
                    'ai',
                    'The chat endpoint is not reachable right now. Connect <strong>/api/ai/chat</strong> in Laravel and try again.'
                );
            } finally {
                isSending = false;
                chatSendBtn.disabled = false;
            }
        };

        chatForm?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = chatInput.value.trim();
            await sendMessage(message);
        });

        quickPrompts.forEach((btn) => {
            btn.addEventListener('click', async () => {
                openChat();
                const prompt = btn.getAttribute('data-prompt') || '';
                await sendMessage(prompt);
            });
        });

        chatClearBtn?.addEventListener('click', () => {
            chatMessages.innerHTML = `
                    <div class="chat-row ai">
                        <div class="chat-avatar ai" aria-hidden="true">
                            <span class="mini-mouth"></span>
                        </div>
                        <div class="chat-bubble">
                            Fresh chat started. Ask me anything about AI systems, Laravel integration, or project planning.
                            <time>${timeNow()}</time>
                        </div>
                    </div>
                `;
            scrollChatToBottom();
        });

        setTimeout(() => {
            if (!chatOpen) setUnread(1);
        }, 1400);
    })();
</script>
</body>
</html>
