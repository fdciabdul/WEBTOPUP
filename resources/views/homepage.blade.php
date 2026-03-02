<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Marspedia') }} - Top Up Game Cepat & Murah</title>
    <meta name="description" content="Top Up Game Termurah dan Tercepat di Indonesia">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* ==========================================================================
           1. VARIABLES & THEME SETUP
           ========================================================================== */
        :root {
            /* Warna Utama: Biru Tegas #0033AA */
            --brand-primary: #0033AA;
            --brand-secondary: #002288;
            --brand-accent: #3366FF;
            --brand-light: #E6F0FF;

            /* Backgrounds & Text */
            --ios-bg: #F2F2F7;
            --body-bg: #EEF1F5;
            --card-bg: #FFFFFF;

            --text-main: #1C1C1E;
            --text-sub: #8E8E93;
            --text-white: #FFFFFF;

            /* Radii & Spacing */
            --radius-icon: 22px;
            --radius-card: 20px;
            --radius-btn: 14px;

            /* Mobile Layout Variables */
            --app-width-mobile: 480px;
            --header-height: 180px;
            --safe-area-bottom: 34px;
            --nav-height: 65px;
            --section-spacing: 50px;
        }

        /* Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
            outline: none;
        }

        body {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--body-bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, .price-tag, .section-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: -0.02em;
        }

        /* ==========================================================================
           2. MAIN CONTAINER (RESPONSIVE WRAPPER)
           ========================================================================== */
        #app-container {
            width: 100%;
            /* Mobile Default */
            max-width: var(--app-width-mobile);
            background-color: var(--ios-bg);
            min-height: 100vh;
            position: relative;
            box-shadow: 0 0 50px rgba(0,0,0,0.1);
            padding-top: var(--header-height);
            padding-bottom: calc(90px + var(--safe-area-bottom));
            overflow-x: hidden;
            transition: max-width 0.3s ease, padding 0.3s ease;
            z-index: 10;
        }

        /* ==========================================================================
           3. ANIMATIONS
           ========================================================================== */
        @keyframes pulse-glow {
            0% { box-shadow: 0 0 0 0 rgba(0, 51, 170, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(0, 51, 170, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 51, 170, 0); }
        }

        @keyframes bounce-effect {
            0% { transform: scale(1); }
            40% { transform: scale(0.9); }
            60% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .touch-effect { transition: transform 0.1s cubic-bezier(0.4, 0, 0.2, 1); }
        .touch-effect:active { transform: scale(0.95); opacity: 0.9; }

        .animate-bounce {
            animation: bounce-effect 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            will-change: transform;
        }

        /* ==========================================================================
           4. HEADER SECTION (MOBILE PRESERVED PERFECTLY)
           ========================================================================== */
        .header {
            background: var(--brand-primary);
            position: fixed;
            top: 0; left: 50%; transform: translateX(-50%);
            width: 100%; max-width: var(--app-width-mobile);
            z-index: 1000;
            /* Mobile Spacing */
            padding: 15px 15px 10px 15px;
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 51, 170, 0.25);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
            text-align: center;
        }

        /* Mobile Header Top */
        .header-top {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            margin-bottom: 2px;
        }
        .user-info {
            width: 100%;
            text-align: center;
        }
        .user-info h1 { font-size: 22px; font-weight: 700; color: #FFFFFF; margin-bottom: 0; }
        .user-info p { font-size: 12px; color: rgba(255, 255, 255, 0.9); font-weight: 500; letter-spacing: 0.01em; display: none; }

        /* HIDDEN ELEMENTS ON MOBILE */
        .action-icons { display: none; }
        .auth-btn-mobile { display: none; }
        .filter-btn { display: none; } /* Hide history button on mobile */

        /* Search Row Mobile - Centered & Compact */
        .search-row {
            display: flex;
            gap: 0;
            align-items: center;
            width: 100%;
            justify-content: center;
        }

        .search-container {
            position: relative;
            /* Lebar proporsional Mobile */
            width: 95%;
            max-width: 400px;
        }

        .search-input {
            width: 100%;
            padding: 12px 16px 12px 40px;
            border-radius: 12px; border: none;
            background: #FFFFFF;
            color: #000; font-size: 14px; font-weight: 400;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            text-align: left;
        }
        .search-input::placeholder { color: #B5B5B5; }
        .search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--brand-primary); font-size: 15px; transition: color 0.3s; }

        /* Category Tabs */
        .category-wrapper { padding: 0; margin-bottom: 0; width: 100%; }
        .category-scroll {
            display: flex; gap: 8px; overflow-x: auto;
            padding: 4px 2px 8px 2px;
            scrollbar-width: none;
        }
        .category-scroll::-webkit-scrollbar { display: none; }
        .cat-pill {
            padding: 7px 14px; border-radius: 10px; font-size: 11px; font-weight: 600;
            color: rgba(255, 255, 255, 0.9); white-space: nowrap; transition: all 0.2s;
            cursor: pointer; flex-shrink: 0; user-select: none;
            background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            display: flex; align-items: center;
        }
        .cat-pill.active {
            background: #FFFFFF; color: var(--brand-primary);
            box-shadow: none; border: none; transform: scale(1.02); font-weight: 700;
        }
        .cat-pill.active::before {
            content: '\f00c';
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            margin-right: 6px;
            font-size: 10px;
        }

        /* ==========================================================================
           5. CONTENT SECTIONS & GRID SYSTEM
           ========================================================================== */
        .section-header {
            padding: 0 20px 14px 20px;
            display: flex; justify-content: space-between; align-items: flex-end;
            scroll-margin-top: 200px; position: relative;
        }
        .section-title { font-size: 20px; font-weight: 700; color: #000; letter-spacing: -0.02em; }
        .see-all-btn {
            font-size: 14px; color: var(--brand-primary); font-weight: 500;
            cursor: pointer; transition: all 0.2s;
            padding: 4px 8px; border-radius: 8px;
        }
        .section-content { margin-bottom: var(--section-spacing); display: block; animation: fadeIn 0.3s ease; }

        /* --- PRODUCT GRID --- */
        .game-grid {
            display: grid;
            /* Mobile Default: 4 Columns */
            grid-template-columns: repeat(4, 1fr);
            gap: 36px 12px;
            padding: 0 20px;
        }

        /* Mobile Small Screen Handling */
        @media (max-width: 360px) {
            .game-grid { grid-template-columns: repeat(3, 1fr); gap: 20px 8px; }
            .header { padding: 12px 12px 8px 12px; }
        }

        .game-card {
            background: transparent; text-align: center; cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            position: relative;
            text-decoration: none;
        }

        .game-icon-wrap {
            width: 80px; height: 80px; margin: 0 auto 12px auto;
            border-radius: var(--radius-icon); overflow: hidden;
            background: #FFFFFF; box-shadow: 0 4px 12px rgba(0,0,0,0.06), inset 0 0 0 1px rgba(0,0,0,0.04);
            display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;
            position: relative;
            border: 2px solid transparent;
        }
        .game-icon-wrap img { width: 100%; height: 100%; object-fit: cover; z-index: 2; position: relative; transition: opacity 0.3s; }
        .game-icon-wrap img.contain-img { object-fit: contain; padding: 16px; }

        .game-title {
            font-size: 11px; font-weight: 500; color: #000000;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            margin-top: 4px; letter-spacing: -0.01em; transition: color 0.2s;
        }

        /* Selected State */
        .game-card.selected .game-icon-wrap {
            border-color: #22c55e;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.2);
        }
        .game-card.selected::after {
            content: '\f00c';
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            top: 0px; right: 0px;
            background: #22c55e;
            color: white;
            font-size: 10px;
            width: 20px; height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transform: translate(25%, -25%);
        }
        @media (max-width: 1023px) {
             .game-card.selected::after {
                 right: 50%; margin-right: -40px;
                 top: -5px;
                 transform: none;
             }
        }

        /* ==========================================================================
           6. MEDIA PARTNERS SECTION
           ========================================================================== */
        .media-section {
            padding: 0 20px;
            margin-bottom: 40px;
            text-align: center;
            width: 100%;
            overflow: hidden;
        }
        .media-title { font-size: 14px; color: #94A3B8; font-weight: 600; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 1px; }
        .media-logos {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px 8px;
            align-items: center;
            justify-items: center;
            width: 100%;
        }
        .media-logo-item {
            height: 25px;
            width: auto;
            max-width: 100%;
            object-fit: contain;
            filter: none; opacity: 1;
            transition: transform 0.1s;
            cursor: pointer;
        }

        /* ==========================================================================
           7. TESTIMONIALS (MARQUEE)
           ========================================================================== */
        .testimonial-section { padding: 0; margin-bottom: 50px; overflow: hidden; }
        .testi-scroll-container-mobile { width: 100%; overflow: hidden; display: flex; position: relative; }
        .testi-track-mobile {
            display: flex; gap: 16px; width: max-content;
            animation: scroll-mobile 30s linear infinite;
            padding: 0 16px; will-change: transform;
        }
        .testi-track-mobile:hover, .testi-track-mobile:active { animation-play-state: paused; }
        @keyframes scroll-mobile { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }

        .testi-card-mobile {
            min-width: 280px; max-width: 280px;
            background: #FFFFFF; border-radius: 20px; padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); flex-shrink: 0;
            border: 1px solid rgba(0,0,0,0.03); transition: transform 0.1s;
        }
        .testi-card-mobile:active { transform: scale(0.98); background: #F9FAFB; }

        .testi-header { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
        .testi-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: linear-gradient(45deg, var(--brand-primary), var(--brand-accent));
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: 14px;
        }
        .testi-info h4 { font-size: 14px; font-weight: 700; color: #1E293B; margin-bottom: 0px; }
        .testi-stars { color: #F59E0B; font-size: 10px; }
        .testi-text { font-size: 13px; color: #64748B; line-height: 1.5; font-style: italic; }

        /* Desktop testimonials - hidden on mobile */
        .testi-mask { display: none; }
        @keyframes scroll-left { to { transform: translateX(-50%); } }
        .testi-scroller { display: flex; gap: 20px; width: max-content; animation: scroll-left 40s linear infinite; }
        .testi-scroller:hover { animation-play-state: paused; }
        .testi-card {
            background: #F8FAFC; border: 1px solid #E2E8F0; padding: 25px; border-radius: 20px;
            min-width: 320px; max-width: 320px; transition: all 0.3s;
            display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box;
        }
        .testi-card:hover { transform: translateY(-5px); background: #fff; box-shadow: 0 15px 40px rgba(0,0,0,0.05); border-color: transparent; }

        /* ==========================================================================
           8. FAQ SECTION
           ========================================================================== */
        .faq-wrapper {
            padding: 0 20px;
            margin-bottom: var(--section-spacing);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            align-items: start;
        }

        .faq-item {
            background: #FFFFFF;
            border-radius: 16px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.02);
            overflow: hidden;
            transition: all 0.3s ease;
            height: fit-content;
        }

        .faq-item.active {
            box-shadow: 0 10px 25px rgba(0, 51, 170, 0.1);
            border-color: rgba(0, 51, 170, 0.1);
        }

        .faq-header {
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            font-weight: 600;
            color: #1C1C1E;
            cursor: pointer;
            background: #FFFFFF;
            transition: background 0.1s;
            min-height: 50px;
        }

        .faq-header:active { background: #F8F9FA; }

        .faq-body {
            padding: 15px;
            padding-top: 5px;
            font-size: 12px;
            color: #475569;
            line-height: 1.6;
            display: none;
            background: #FFFFFF;
            border-top: 1px dashed rgba(0,0,0,0.05);
        }

        .faq-item.active .faq-body { display: block; }
        .faq-item.active .faq-header i { transform: rotate(180deg); color: var(--brand-primary); }

        /* ==========================================================================
           9. ABOUT SECTION (INFORMATION CARD - OPTIMIZED RESPONSIVE)
           ========================================================================== */
        .about-section { padding: 0 20px; margin-bottom: var(--section-spacing); }

        /* Container Card */
        .about-card {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
            border-radius: 24px;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 51, 170, 0.25);
            cursor: pointer;

            /* Responsive Layout: Flexbox */
            display: flex;
            flex-direction: column; /* Default Mobile: Stack vertikal */
            align-items: center;
            text-align: center;

            /* Fluid Padding: clamp(min, preferred, max) */
            padding: clamp(25px, 6vw, 50px);
            gap: clamp(20px, 4vw, 40px);
            transition: transform 0.1s;
        }

        .about-card:active { transform: scale(0.98); }
        .about-card::before { content: ''; position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%; }

        .about-content {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        /* Responsive Image Wrapper */
        .about-logo {
            /* Fluid Size: menyesuaikan layar */
            width: clamp(100px, 25vw, 160px);
            height: clamp(100px, 25vw, 160px);

            border-radius: 35px;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 0;
            overflow: hidden;
        }

        .about-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .about-text {
            width: 100%;
            /* Max width agar teks enak dibaca di layar lebar */
            max-width: 800px;
        }

        /* Responsive Typography using Clamp */
        .about-text h3 {
            /* Font size dinamis: Min 20px, Max 36px */
            font-size: clamp(20px, 5vw, 36px);
            margin-bottom: 12px;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -0.5px;
        }

        .about-text p {
            /* Font size dinamis: Min 13px, Max 16px */
            font-size: clamp(13px, 3.5vw, 16px);
            opacity: 0.9;
            line-height: 1.6;
            color: #F2F2F7;
            margin-bottom: 25px;
        }

        .social-row { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .social-link {
            width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,0.2); color: white;
            display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 16px;
            backdrop-filter: blur(10px); transition: transform 0.1s, background 0.1s;
            cursor: pointer;
        }

        /* ==========================================================================
           10. FOOTER & NAVIGATION
           ========================================================================== */
        .footer-section {
            padding: 30px 20px 40px 20px;
            text-align: center;
            color: #94A3B8;
            font-size: 14px;
            line-height: 1.6;
            background: transparent;
            margin-top: -20px;
        }
        .footer-copyright { font-weight: 600; margin-bottom: 8px; color: #64748B; font-size: 14px; }
        .footer-text { font-size: 12px; color: #94A3B8; margin-bottom: 10px; }
        .footer-links { display: flex; justify-content: center; gap: 15px; margin-top: 10px; flex-wrap: wrap; font-size: 13px; }
        .footer-links a { color: var(--brand-primary); text-decoration: none; font-weight: 500; }

        .bottom-nav {
            position: fixed; bottom: 0; left: 50%; transform: translateX(-50%);
            width: 100%; max-width: var(--app-width-mobile); height: calc(var(--nav-height) + var(--safe-area-bottom));
            background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border-top: 0.5px solid rgba(0,0,0,0.1);
            display: flex; justify-content: space-around; align-items: flex-start; padding-top: 12px; z-index: 999;
            transition: max-width 0.3s ease;
        }
        .bottom-nav::after { content: ''; position: absolute; bottom: 8px; left: 50%; transform: translateX(-50%); width: 130px; height: 5px; background: #000; border-radius: 10px; opacity: 0.2; }
        .nav-item { flex: 1; text-align: center; color: #999999; font-size: 10px; text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 4px; cursor: pointer; transition: all 0.3s; }
        .nav-item i { font-size: 24px; margin-bottom: 2px; transition: transform 0.2s; }
        .nav-item:active i { transform: scale(0.9); }
        .nav-item span { font-weight: 500; letter-spacing: -0.2px; }
        .nav-item.active { color: var(--brand-primary); }
        .nav-item.active i { transform: translateY(-2px) scale(1.1); }
        .nav-center-wrapper { position: relative; top: -25px; }
        .nav-center-btn {
            width: 60px; height: 60px; background: var(--brand-primary); border-radius: 50%;
            display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(0, 51, 170, 0.4);
            color: #fff; font-size: 24px; cursor: pointer; transition: transform 0.1s; border: 4px solid var(--ios-bg);
            animation: pulse-glow 2s infinite;
            text-decoration: none;
        }
        .nav-center-btn:active { transform: scale(0.9); }

        .desktop-only-section { display: none; }
        .pill-all-products { display: block; }
        .auth-btn { display: none; }

        /* ==========================================================================
           11. SCROLL TO TOP BUTTON (OPTIMIZED SHAPE & POSITION)
           ========================================================================== */
        #scroll-top-btn {
            position: fixed;

            /* POSISI PRESISI MOBILE */
            bottom: calc(var(--nav-height) + var(--safe-area-bottom) + 15px);
            right: 20px;

            width: 45px;
            height: 45px;
            background: var(--brand-primary);
            color: white;

            border-radius: 12px;

            display: flex;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;

            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 5px 20px rgba(0, 51, 170, 0.3);
            cursor: pointer;
            z-index: 2000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateY(20px);
        }

        #scroll-top-btn.visible {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateY(0);
        }

        #scroll-top-btn:hover {
            transform: translateY(-5px);
            background: var(--brand-secondary);
        }

        /* ==========================================================================
           12. DESKTOP OPTIMIZATION (min-width: 1024px)
           ========================================================================== */
        @media (min-width: 1024px) {

            ::-webkit-scrollbar { width: 8px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
            ::-webkit-scrollbar-thumb:hover { background: var(--brand-primary); }

            :root {
                --header-height: 0px;
            }

            body { background-color: #E2E8F0; overflow-x: hidden; }

            /* SCROLL BUTTON DESKTOP POSITION */
            #scroll-top-btn {
                right: 40px;
                left: auto;
                margin-left: 0;
                bottom: 40px;
                width: 55px;
                height: 55px;
                font-size: 22px;
                border-radius: 16px;
            }

            /* MAIN CONTAINER */
            #app-container {
                width: 92%;
                max-width: 1600px;
                box-shadow: 0 25px 80px -20px rgba(0,0,0,0.15);
                background: #FFFFFF;
                min-height: 95vh;
                padding-bottom: 50px;
                padding-top: 0;
                margin: 30px auto;
                border-radius: 30px;
                border: 1px solid rgba(255,255,255,0.5);
            }

            .desktop-only-section { display: block; }
            .testi-scroll-container-mobile { display: none; }

            /* MEDIA LOGOS */
            .media-section {
                padding: 0 50px;
                margin-bottom: 40px;
                text-align: center;
                margin-top: 60px;
                width: 100%;
            }
            .media-title { font-size: 13px; color: #94A3B8; font-weight: 600; margin-bottom: 25px; }
            .media-logos {
                display: flex;
                justify-content: center;
                gap: 30px;
                flex-wrap: wrap;
                align-items: center;
                max-width: 100%;
                margin: 0 auto;
            }
            .media-logo-item {
                height: 40px; object-fit: contain; opacity: 1; filter: none; transition: transform 0.3s;
            }
            .media-logo-item:hover { transform: scale(1.1); }
            .media-logo-item:active { transform: none; }

            /* HEADER - REVISI DESKTOP SPACING & ALIGNMENT */
            .header {
                position: relative;
                top: 0; transform: none; left: auto;
                width: 100%; max-width: 100%;
                border-radius: 30px 30px 0 0;
                /* REVISI DESKTOP: Padding dikurangi dari 40px ke 20px agar "secukupnya" */
                padding: 20px 50px;
                background: var(--brand-primary);
                box-shadow: none;
                border: none;
                color: #FFFFFF;
                display: flex;
                flex-direction: column;
                /* REVISI DESKTOP: Gap dikurangi agar compact */
                gap: 15px;
                align-items: center;
            }

            /* Header Top Row */
            .header-top {
                width: 100%;
                margin-bottom: 0;
                flex-direction: row;
                align-items: center;
                justify-content: center; /* Center User Info */
                position: relative; /* Context for Auth Button */
            }

            .user-info {
                text-align: center; /* Center text */
            }

            .user-info h1 { font-size: 32px; color: #FFFFFF; letter-spacing: -1px; margin-bottom: 5px; }
            .user-info p { font-size: 16px; color: rgba(255,255,255,0.8); font-weight: 500; display: block; }

            /* Actions - Visible on Desktop */
            .action-icons {
                display: flex; /* Restore display for desktop */
                position: absolute;
                right: 0;
                top: 50%;
                transform: translateY(-50%);
            }

            .auth-btn-mobile { display: none; }
            .auth-btn {
                display: flex; align-items: center; gap: 10px;
                background: rgba(255, 255, 255, 0.15); padding: 10px 24px; border-radius: 12px;
                color: #FFFFFF; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s;
                border: 1px solid rgba(255,255,255,0.1);
                text-decoration: none;
            }
            .auth-btn:hover { background: #FFFFFF; color: var(--brand-primary); transform: translateY(-2px); }

            /* Search Row - Centered */
            .search-row {
                width: 100%;
                margin: 0;
                display: flex;
                /* Gap secukupnya */
                gap: 15px;
                justify-content: center;
                align-items: center; /* Vertical Center */
                height: 50px; /* Fixed Height Row for Alignment */
            }

            /* Search Container - Constrained Width */
            .search-container {
                width: 100%;
                max-width: 600px; /* Comfortable width for desktop */
                height: 100%;
            }

            .search-input {
                /* REVISI DESKTOP: Height fixed 50px agar sejajar tombol riwayat */
                height: 50px;
                padding: 0 24px 0 55px;
                font-size: 16px; border-radius: 14px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                border: none; background: #FFFFFF; color: var(--text-main); font-weight: 500;
                display: flex; align-items: center;
            }
            .search-icon { left: 24px; font-size: 18px; color: #94A3B8; }

            /* Filter Button - REVISI: WHITE BACKGROUND, ROUNDED SQUARE, BLUE ICON */
            .filter-btn {
                display: flex;
                /* Fixed size matching search input height */
                width: 50px;
                height: 50px;
                /* Rounded sedikit kotak (14px), bukan 50% */
                border-radius: 14px;
                background: #FFFFFF; /* White Background */
                color: var(--brand-primary); /* Blue Icon */
                font-size: 20px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                /* Ensure icon is centered */
                align-items: center;
                justify-content: center;
                margin: 0;
                text-decoration: none;
            }
            .filter-btn:hover { background: #f0f0f0; transform: translateY(-2px); }

            /* Categories - Centered */
            .category-wrapper {
                position: relative;
                width: 100%;
                padding: 0;
                margin-top: 5px;
                display: flex;
                justify-content: center;
            }

            .category-scroll {
                background: transparent;
                padding: 0;
                gap: 14px;
                border: none;
                justify-content: center; /* Center items */
            }

            .cat-pill {
                padding: 10px 24px; font-size: 14px; border-radius: 12px;
                background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.1);
                color: #FFFFFF; transition: all 0.2s;
            }
            .cat-pill:hover { background: #FFFFFF; color: var(--brand-primary); transform: translateY(-2px); }
            .cat-pill:active { transform: none; }
            .cat-pill.active {
                background: #FFFFFF; color: var(--brand-primary);
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                transform: translateY(-1px); font-weight: 700;
            }

            .banner-wrapper { display: none !important; }
            .bottom-nav { display: none !important; }

            #sec-popular { margin-top: 80px; }

            .filterable-section { transition: margin 0.3s; }
            .section-content { max-width: 100%; margin: 0 auto 50px auto; padding: 0 50px; }
            .section-header { padding: 0 0 20px 0; border-bottom: 2px solid #F1F5F9; margin-bottom: 25px; align-items: center; }
            .section-title { font-size: 22px; color: #1E293B; font-weight: 800; letter-spacing: -0.5px; }

            .see-all-btn {
                background: #FFFFFF; color: var(--brand-primary);
                padding: 10px 20px; border-radius: 10px;
                font-weight: 600; font-size: 14px;
                border: 1px solid #E2E8F0;
                box-shadow: 0 2px 4px rgba(0,0,0,0.02);
                transition: all 0.2s;
            }
            .see-all-btn:hover {
                background: var(--brand-primary); color: white; border-color: var(--brand-primary);
                transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 51, 170, 0.2);
            }
            .see-all-btn:active { transform: none; }

            /* GRID OPTIMIZATION */
            .game-grid { grid-template-columns: repeat(8, 1fr); gap: 24px 16px; padding: 0; }
            .game-card:active { transform: none; }

            .game-icon-wrap {
                width: 110px; height: 110px;
                border-radius: 20px;
                background: #FFFFFF; box-shadow: 0 2px 8px rgba(0,0,0,0.04);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                border: 2px solid transparent;
            }
            .game-card:hover .game-icon-wrap {
                transform: translateY(-6px);
                box-shadow: 0 15px 30px rgba(0, 51, 170, 0.15); border-color: rgba(0, 51, 170, 0.3);
            }

            .game-card.selected::after {
                width: 28px; height: 28px; font-size: 14px;
                transform: translate(25%, -25%);
            }

            .game-title { font-size: 13px; margin-top: 12px; font-weight: 600; color: #64748B; transition: color 0.3s; }
            .game-card:hover .game-title { color: var(--brand-primary); }

            /* TESTIMONIALS */
            .testimonial-section { padding: 0 50px; margin-bottom: 60px; overflow: hidden; position: relative; }
            .testi-mask {
                display: block;
                mask-image: linear-gradient(to right, transparent 0%, black 5%, black 95%, transparent 100%);
                -webkit-mask-image: linear-gradient(to right, transparent 0%, black 5%, black 95%, transparent 100%);
                padding: 10px 0;
            }
            .testi-scroller { display: flex; gap: 20px; width: max-content; animation: scroll-left 40s linear infinite; }
            .testi-scroller:hover { animation-play-state: paused; }

            .testi-card {
                background: #F8FAFC; border: 1px solid #E2E8F0; padding: 25px; border-radius: 20px;
                min-width: 320px; max-width: 320px; transition: all 0.3s;
                display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box;
            }
            .testi-card:hover { transform: translateY(-5px); background: #fff; box-shadow: 0 15px 40px rgba(0,0,0,0.05); border-color: transparent; }
            .testi-header { display: flex; align-items: center; gap: 12px; margin-bottom: 15px; }
            .testi-avatar {
                width: 45px; height: 45px; border-radius: 50%;
                background: linear-gradient(45deg, var(--brand-primary), var(--brand-accent));
                display: flex; align-items: center; justify-content: center;
                color: #fff; font-weight: 700; font-size: 16px;
            }
            .testi-info h4 { font-size: 15px; font-weight: 700; color: #1E293B; margin-bottom: 2px; }
            .testi-stars { color: #F59E0B; font-size: 12px; }
            .testi-text { font-size: 14px; color: #64748B; line-height: 1.6; font-style: italic; }

            /* ABOUT CARD DESKTOP LAYOUT */
            .about-section { margin: 60px auto 40px auto; padding: 0 50px; }

            /* Override for Desktop: Row Layout */
            .about-card {
                /* Reset mobile specifics */
                align-items: stretch;
                text-align: left;
            }

            .about-content {
                flex-direction: row;
                align-items: center;
                gap: 40px;
            }

            .about-logo { margin-bottom: 0; }

            .about-text { text-align: left; flex: 1; }
            .about-text h3 { margin-bottom: 10px; }
            .about-text p { max-width: 600px; margin-bottom: 25px; margin-left: 0; }

            .social-row { justify-content: flex-start; }
            .social-link { width: 45px; height: 45px; background: rgba(255,255,255,0.1); transition: all 0.3s; }
            .social-link:hover { background: #FFFFFF; color: var(--brand-primary); transform: translateY(-5px); }
            .social-link:active { transform: none; }

            /* FAQ */
            .faq-wrapper { gap: 20px; padding: 0 50px; }
            .faq-item { border: 1px solid #E2E8F0; box-shadow: none; margin-bottom: 0; background: #F8FAFC; }
            .faq-item:hover { border-color: #CBD5E1; background: #FFF; }
            .faq-header { font-size: 16px; padding: 20px 25px; }
            .faq-body { padding: 25px; font-size: 15px; }

            /* Footer Desktop */
            .footer-section { padding: 30px 50px 40px; }
        }
    </style>
</head>
<body>

    <div id="scroll-top-btn"><i class="fas fa-arrow-up"></i></div>

    <div id="app-container">

        <!-- ==================== HEADER ==================== -->
        <div class="header">
            <div class="header-top">
                <div class="user-info">
                    <h1>@auth Welcome, {{ Auth::user()->name }}&#x1F44B; @else Welcome to {{ config('app.name') }}&#x1F44B; @endauth</h1>
                    <p>{{ config('app.name') }} - Top Up Game Termurah</p>
                </div>
                <div class="action-icons">
                    @guest
                        <a href="{{ route('login') }}" class="auth-btn-mobile touch-effect bounce-trigger"><i class="fas fa-sign-in-alt"></i> Masuk</a>
                        <a href="{{ route('login') }}" class="auth-btn bounce-trigger"><i class="fas fa-sign-in-alt"></i> Login / Daftar</a>
                    @else
                        <a href="{{ route('dashboard.index') }}" class="auth-btn-mobile touch-effect bounce-trigger"><i class="fas fa-user"></i> Dashboard</a>
                        <a href="{{ route('dashboard.index') }}" class="auth-btn bounce-trigger"><i class="fas fa-user"></i> Dashboard</a>
                    @endguest
                </div>
            </div>

            <!-- Search & Filter -->
            <div class="search-row">
                <form action="{{ route('search') }}" method="GET" class="search-container" style="display:contents;">
                    <div class="search-container">
                        <input type="text" name="q" class="search-input" id="searchInput" placeholder="Temukan produk di {{ config('app.name') }}..." autocomplete="off">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                </form>
                <a href="{{ route('track.order') }}" class="filter-btn touch-effect bounce-trigger">
                    <i class="fas fa-history"></i>
                </a>
            </div>

            <!-- Category Pills -->
            <div class="category-wrapper">
                <div class="category-scroll">
                    <div class="cat-pill pill-all-products active bounce-trigger" onclick="handleCatClick('all', this)">Semua Produk</div>
                    <div class="cat-pill bounce-trigger" onclick="handleCatClick('sec-popular', this)">&#x1F525; Populer</div>
                    @php
                        $uniqueCategories = collect($categories ?? [])->pluck('category')->unique()->filter()->values()->all();
                    @endphp
                    @foreach($uniqueCategories as $cat)
                        <div class="cat-pill bounce-trigger" onclick="handleCatClick('sec-{{ Str::slug($cat) }}', this)">{{ $cat }}</div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- ==================== PRODUCT SECTIONS (DYNAMIC) ==================== -->
        @php
            $allGames = collect($categories ?? []);
            $popularGames = $allGames->filter(fn($g) => !empty($g['is_popular']) && $g['is_popular']);
            $gamesByCategory = $allGames->groupBy('category');
            $sectionIndex = 0;
        @endphp

        {{-- Popular Products Section --}}
        @if($popularGames->count() > 0)
        <div id="sec-popular" class="section-content filterable-section">
            <div class="section-header">
                <div class="section-title">Produk Populer</div>
                @if($popularGames->count() > 4)
                <div class="see-all-btn bounce-trigger" id="btn-popular">Lihat Semua</div>
                @endif
            </div>
            <div class="game-grid" id="grid-popular">
                @foreach($popularGames->values() as $index => $game)
                <a href="{{ route('category', $game['slug']) }}" class="game-card bounce-trigger">
                    <div class="game-icon-wrap">
                        @if($index < 8)
                        <img loading="lazy" src="{{ $game['image'] }}" alt="{{ $game['name'] }}" onerror="this.src='/images/default-game.png'">
                        @else
                        <img data-src="{{ $game['image'] }}" alt="{{ $game['name'] }}" onerror="this.src='/images/default-game.png'">
                        @endif
                    </div>
                    <div class="game-title">{{ $game['name'] }}</div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Category Sections (Dynamic from API) --}}
        @foreach($gamesByCategory as $categoryName => $games)
        @php $sectionId = Str::slug($categoryName ?: 'lainnya'); @endphp
        <div id="sec-{{ $sectionId }}" class="section-content filterable-section">
            <div class="section-header">
                <div class="section-title">{{ $categoryName ?: 'Lainnya' }}</div>
                @if(count($games) > 4)
                <div class="see-all-btn bounce-trigger" id="btn-{{ $sectionId }}">Lihat Semua</div>
                @endif
            </div>
            <div class="game-grid" id="grid-{{ $sectionId }}">
                @foreach($games as $index => $game)
                <a href="{{ route('category', $game['slug']) }}" class="game-card bounce-trigger">
                    <div class="game-icon-wrap">
                        @if($index < 8)
                        <img loading="lazy" src="{{ $game['image'] }}" alt="{{ $game['name'] }}" onerror="this.src='/images/default-game.png'">
                        @else
                        <img data-src="{{ $game['image'] }}" alt="{{ $game['name'] }}" onerror="this.src='/images/default-game.png'">
                        @endif
                    </div>
                    <div class="game-title">{{ $game['name'] }}</div>
                </a>
                @endforeach
            </div>
        </div>
        @endforeach

        <!-- ==================== MEDIA SECTION ==================== -->
        <div class="media-section">
            <div class="media-title">Telah Diliput Oleh</div>
            <div class="media-logos">
                @if(isset($medias) && $medias->count() > 0)
                    @foreach($medias as $media)
                        <a href="{{ $media->url }}" target="_blank" rel="noopener">
                            @if($media->logo)
                                <img loading="lazy" src="{{ asset('storage/' . $media->logo) }}" alt="{{ $media->media_name }}" class="media-logo-item bounce-trigger">
                            @else
                                <span style="font-size:13px;font-weight:600;color:#94A3B8;">{{ $media->media_name }}</span>
                            @endif
                        </a>
                    @endforeach
                @else
                    <!-- fallback hardcoded logos -->
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/51/Facebook_f_logo_%282019%29.svg/1200px-Facebook_f_logo_%282019%29.svg.png" alt="Facebook" class="media-logo-item">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Instagram_icon.png/2048px-Instagram_icon.png" alt="Instagram" class="media-logo-item">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/WhatsApp.svg/2044px-WhatsApp.svg.png" alt="WhatsApp" class="media-logo-item">
                @endif
            </div>
        </div>

        <!-- ==================== TESTIMONIALS ==================== -->
        <div class="testimonial-section">
            <div class="section-header"><div class="section-title">Apa Kata Mereka?</div></div>

            @php
                $reviewList = isset($reviews) && $reviews->count() > 0 ? $reviews : collect([
                    (object)['name' => 'Agus Santoso', 'comment' => 'Proses topup kilat banget, gak sampe 1 menit diamond ML langsung masuk. Mantap!', 'rating' => 5],
                    (object)['name' => 'Rizky Febrian', 'comment' => 'Harganya paling miring dibanding toko sebelah. Admin juga fast respon kalo ada kendala.', 'rating' => 5],
                    (object)['name' => 'Dinda Ayu', 'comment' => 'Suka banget beli voucher di sini, sering ada promo dadakan. Makin hemat deh main game.', 'rating' => 5],
                    (object)['name' => 'Budi Jaya', 'comment' => 'Website user friendly, gampang dipake. Gak ribet harus login sana sini. Recommended!', 'rating' => 5],
                    (object)['name' => 'Siti Aminah', 'comment' => 'Pelayanan oke, metode pembayaran lengkap banget bisa pake QRIS jadi praktis.', 'rating' => 4],
                ]);
            @endphp

            <!-- Desktop Testimonials (testi-mask) -->
            <div class="testi-mask desktop-only-section">
                <div class="testi-scroller">
                    @foreach($reviewList as $review)
                    <div class="testi-card">
                        <div class="testi-header">
                            <div class="testi-avatar">{{ strtoupper(substr($review->name, 0, 1)) . strtoupper(substr(explode(' ', $review->name)[1] ?? '', 0, 1)) }}</div>
                            <div class="testi-info">
                                <h4>{{ $review->name }}</h4>
                                <div class="testi-stars">{!! str_repeat('&#9733;', $review->rating) . str_repeat('&#9734;', 5 - $review->rating) !!}</div>
                            </div>
                        </div>
                        <div class="testi-text">"{{ $review->comment }}"</div>
                    </div>
                    @endforeach
                    {{-- Duplicate for seamless infinite scroll --}}
                    @foreach($reviewList as $review)
                    <div class="testi-card">
                        <div class="testi-header">
                            <div class="testi-avatar">{{ strtoupper(substr($review->name, 0, 1)) . strtoupper(substr(explode(' ', $review->name)[1] ?? '', 0, 1)) }}</div>
                            <div class="testi-info">
                                <h4>{{ $review->name }}</h4>
                                <div class="testi-stars">{!! str_repeat('&#9733;', $review->rating) . str_repeat('&#9734;', 5 - $review->rating) !!}</div>
                            </div>
                        </div>
                        <div class="testi-text">"{{ $review->comment }}"</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Mobile Testimonials -->
            <div class="testi-scroll-container-mobile">
                <div class="testi-track-mobile">
                    @foreach($reviewList as $review)
                    <div class="testi-card-mobile">
                        <div class="testi-header">
                            <div class="testi-avatar">{{ strtoupper(substr($review->name, 0, 1)) . strtoupper(substr(explode(' ', $review->name)[1] ?? '', 0, 1)) }}</div>
                            <div class="testi-info">
                                <h4>{{ $review->name }}</h4>
                                <div class="testi-stars">{!! str_repeat('&#9733;', $review->rating) . str_repeat('&#9734;', 5 - $review->rating) !!}</div>
                            </div>
                        </div>
                        <div class="testi-text">"{{ $review->comment }}"</div>
                    </div>
                    @endforeach
                    {{-- Duplicate for seamless infinite scroll --}}
                    @foreach($reviewList as $review)
                    <div class="testi-card-mobile">
                        <div class="testi-header">
                            <div class="testi-avatar">{{ strtoupper(substr($review->name, 0, 1)) . strtoupper(substr(explode(' ', $review->name)[1] ?? '', 0, 1)) }}</div>
                            <div class="testi-info">
                                <h4>{{ $review->name }}</h4>
                                <div class="testi-stars">{!! str_repeat('&#9733;', $review->rating) . str_repeat('&#9734;', 5 - $review->rating) !!}</div>
                            </div>
                        </div>
                        <div class="testi-text">"{{ $review->comment }}"</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- ==================== FAQ SECTION ==================== -->
        <div class="section-content">
            <div class="section-header"><div class="section-title">Pertanyaan Umum</div></div>
            <div class="faq-wrapper">
                @if(isset($faqs) && $faqs->count() > 0)
                    @foreach($faqs as $faq)
                    <div class="faq-item">
                        <div class="faq-header">{{ $faq->question }} <i class="fas fa-chevron-down"></i></div>
                        <div class="faq-body">{!! $faq->answer !!}</div>
                    </div>
                    @endforeach
                @else
                    <!-- fallback hardcoded FAQs -->
                    <div class="faq-item">
                        <div class="faq-header">Cara Top Up? <i class="fas fa-chevron-down"></i></div>
                        <div class="faq-body">1. Pilih Game<br>2. Masukkan User ID<br>3. Pilih Nominal<br>4. Bayar</div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-header">Metode Pembayaran? <i class="fas fa-chevron-down"></i></div>
                        <div class="faq-body">QRIS, BCA, Mandiri, BNI, BRI, Alfamart, Indomaret.</div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-header">Layanan Bantuan <i class="fas fa-chevron-down"></i></div>
                        <div class="faq-body">Hubungi CS kami via WhatsApp di tombol kontak profil.</div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-header">Berapa Lama Proses? <i class="fas fa-chevron-down"></i></div>
                        <div class="faq-body">Proses instan 1-5 detik setelah pembayaran berhasil dikonfirmasi sistem.</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- ==================== ABOUT SECTION ==================== -->
        <div class="about-section">
            <div class="about-card bounce-trigger">
                <div class="about-content">
                    <div class="about-logo">
                        <img loading="lazy" src="{{ asset('images/logo.png') }}" alt="Logo">
                    </div>
                    <div class="about-text">
                        <h3>PT Marspedia Digital Indonesia</h3>
                        <p>Marspedia adalah Website Penyedia Jasa Professional, Top Up Diamond All Games Online, Social Media Services & PPOB Termurah dan Terlengkap.</p>
                        <div class="social-row">
                            <a href="#" class="social-link bounce-trigger"><i class="fab fa-whatsapp"></i></a>
                            <a href="#" class="social-link bounce-trigger"><i class="fab fa-facebook"></i></a>
                            <a href="#" class="social-link bounce-trigger"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-link bounce-trigger"><i class="fa-brands fa-threads"></i></a>
                            <a href="#" class="social-link bounce-trigger"><i class="fab fa-tiktok"></i></a>
                            <a href="#" class="social-link bounce-trigger"><i class="fab fa-youtube"></i></a>
                            <a href="#" class="social-link bounce-trigger"><i class="fab fa-telegram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== FOOTER ==================== -->
        <div class="footer-section">
            <div class="footer-copyright">Copyrights &copy; {{ date('Y') }} {{ config('app.name', 'Marspedia') }}. All rights reserved</div>
            <div class="footer-text">{!! \App\Models\Setting::get('footer_text', 'By using our services you agree to our Terms of Services, Privacy Policy, Service Fees and its amendments in the future.') !!}</div>
            <div class="footer-links">
                @if(isset($pages) && $pages->count() > 0)
                    @foreach($pages as $page)
                        <a href="#" data-page-title="{{ $page->title }}" data-page-id="{{ $page->id }}" onclick="event.preventDefault(); showPageContent(this)">{{ $page->title }}</a>
                    @endforeach
                @else
                    <a href="#">Syarat dan Ketentuan</a>
                    <a href="#">Kebijakan Privasi</a>
                    <a href="#">Tentang Kami</a>
                @endif
            </div>
            @php
                $socialLinks = [
                    'whatsapp_url' => ['icon' => 'fab fa-whatsapp', 'label' => 'WhatsApp'],
                    'instagram' => ['icon' => 'fab fa-instagram', 'label' => 'Instagram'],
                    'facebook' => ['icon' => 'fab fa-facebook', 'label' => 'Facebook'],
                    'tiktok' => ['icon' => 'fab fa-tiktok', 'label' => 'TikTok'],
                    'telegram' => ['icon' => 'fab fa-telegram', 'label' => 'Telegram'],
                    'youtube' => ['icon' => 'fab fa-youtube', 'label' => 'YouTube'],
                ];
                $hasSocial = false;
                foreach ($socialLinks as $key => $info) {
                    if (\App\Models\Setting::get($key)) { $hasSocial = true; break; }
                }
            @endphp
            @if($hasSocial)
            <div style="display:flex; justify-content:center; gap:12px; margin-top:14px;">
                @foreach($socialLinks as $key => $info)
                    @php $url = \App\Models\Setting::get($key); @endphp
                    @if($url)
                    <a href="{{ $url }}" target="_blank" rel="noopener" style="width:36px; height:36px; background:rgba(0,51,170,0.08); border-radius:50%; display:flex; align-items:center; justify-content:center; color:var(--brand-primary); text-decoration:none; transition:all 0.3s; font-size:16px;" title="{{ $info['label'] }}">
                        <i class="{{ $info['icon'] }}"></i>
                    </a>
                    @endif
                @endforeach
            </div>
            @endif
        </div>

        <!-- Page Modal -->
        <div id="pageModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); justify-content:center; align-items:center; padding:20px;">
            <div style="background:#fff; border-radius:20px; width:100%; max-width:500px; max-height:80vh; overflow-y:auto; padding:30px 24px; position:relative; box-shadow:0 20px 60px rgba(0,0,0,0.2); animation:slideUpFade 0.3s ease;">
                <button onclick="closePageModal()" style="position:absolute; top:16px; right:16px; background:none; border:none; font-size:20px; color:#94A3B8; cursor:pointer; width:32px; height:32px; display:flex; align-items:center; justify-content:center; border-radius:8px; transition:all 0.2s;">
                    <i class="fas fa-times"></i>
                </button>
                <h3 id="pageModalTitle" style="font-size:18px; font-weight:700; color:#1E293B; margin-bottom:16px; padding-right:30px;"></h3>
                <div id="pageModalContent" style="font-size:14px; color:#64748B; line-height:1.7;"></div>
            </div>
        </div>

        <!-- ==================== BOTTOM NAVIGATION ==================== -->
        <div class="bottom-nav">
            <a href="{{ route('home') }}" class="nav-item active"><i class="fas fa-home"></i><span>Home</span></a>
            <a href="{{ route('track.order') }}" class="nav-item"><i class="fas fa-history"></i><span>Riwayat</span></a>
            <div class="nav-center-wrapper"><a href="{{ route('track.order') }}" class="nav-center-btn touch-effect"><i class="fas fa-qrcode"></i></a></div>
            <a href="#" class="nav-item"><i class="fas fa-tag"></i><span>Promo</span></a>
            @guest
                <a href="{{ route('login') }}" class="nav-item"><i class="far fa-user"></i><span>Akun</span></a>
            @else
                <a href="{{ route('dashboard.index') }}" class="nav-item"><i class="far fa-user"></i><span>Akun</span></a>
            @endguest
        </div>

    </div>

    <script>
        // ==========================================================================
        // JS 1: KATEGORI DRAG SCROLL
        // ==========================================================================
        const slider = document.querySelector('.category-scroll');
        let isDown = false, startX, scrollLeft;
        if(slider) {
            slider.addEventListener('mousedown', (e) => { isDown = true; startX = e.pageX - slider.offsetLeft; scrollLeft = slider.scrollLeft; });
            slider.addEventListener('mouseleave', () => { isDown = false; });
            slider.addEventListener('mouseup', () => { isDown = false; });
            slider.addEventListener('mousemove', (e) => { if(!isDown) return; e.preventDefault(); const x = e.pageX - slider.offsetLeft; const walk = (x - startX) * 2; slider.scrollLeft = scrollLeft - walk; });
        }

        // ==========================================================================
        // JS 2: UNIVERSAL BOUNCE & SELECTION
        // ==========================================================================
        document.querySelectorAll('.bounce-trigger').forEach(item => {
            item.addEventListener('click', function() {
                // Bounce Logic
                this.classList.remove('animate-bounce');
                void this.offsetWidth;
                this.classList.add('animate-bounce');
                setTimeout(() => {
                    this.classList.remove('animate-bounce');
                }, 400);

                // Selection Logic for Game Cards
                if(this.classList.contains('game-card')) {
                    const parent = this.parentElement;
                    const siblings = parent.querySelectorAll('.game-card');
                    siblings.forEach(sib => sib.classList.remove('selected'));
                    this.classList.add('selected');
                }
            });
        });

        // ==========================================================================
        // JS 3: FILTER CATEGORY LOGIC (DYNAMIC)
        // ==========================================================================
        function handleCatClick(sectionId, element) {
            filterCategory(sectionId, element);
        }

        function filterCategory(sectionId, clickedElement) {
            const allSections = document.querySelectorAll('.filterable-section');
            const pills = document.querySelectorAll('.cat-pill');

            pills.forEach(p => p.classList.remove('active'));
            if(clickedElement) {
                clickedElement.classList.add('active');
            }

            if (sectionId === 'all') {
                allSections.forEach(sec => {
                    sec.style.display = 'block';
                    sec.style.marginTop = (window.innerWidth >= 1024) ? '50px' : '0px';
                });
            } else {
                allSections.forEach(sec => {
                    if(sec.id === sectionId) {
                        sec.style.display = 'block';
                        sec.style.marginTop = (window.innerWidth >= 1024) ? '40px' : '20px';
                        if (window.innerWidth < 1024) {
                            const headerOffset = 200;
                            const y = sec.getBoundingClientRect().top + window.scrollY - headerOffset;
                            window.scrollTo({top: y, behavior: 'smooth'});
                        }
                    } else {
                        sec.style.display = 'none';
                    }
                });
            }
        }

        // ==========================================================================
        // JS 4: SMART ACCORDION & LAZY LOAD LOGIC (DYNAMIC)
        // ==========================================================================
        const expandedSections = {};

        function getLimitBasedOnGridId(gridId) {
            const isDesktop = window.innerWidth >= 1024;
            const cols = isDesktop ? 8 : 4;

            if (gridId === 'grid-popular') {
                // Popular: 2 rows
                return cols * 2;
            }
            // Category sections: 1 row
            return cols;
        }

        // Helper to collapse a section
        function collapseSection(gridId, btn) {
            const grid = document.getElementById(gridId);
            if(!grid) return;
            const items = grid.querySelectorAll('.game-card');
            const limit = getLimitBasedOnGridId(gridId);

            items.forEach((item, index) => {
                if (index >= limit) {
                    item.style.display = 'none';
                }
            });

            if(btn) btn.innerText = 'Lihat Semua';
            expandedSections[gridId] = false;
        }

        // Helper to expand a section (AND TRIGGER LAZY LOAD)
        function expandSection(gridId, btn) {
            const grid = document.getElementById(gridId);
            if(!grid) return;
            const items = grid.querySelectorAll('.game-card');

            items.forEach(item => {
                // CORE LOGIC for Fast Loading:
                // Swap data-src to src when expansion is requested
                const img = item.querySelector('img');
                if(img && img.hasAttribute('data-src')) {
                    img.setAttribute('src', img.getAttribute('data-src'));
                    img.removeAttribute('data-src');
                }
                item.style.display = 'block';
            });

            if(btn) btn.innerText = 'Tutup';
            expandedSections[gridId] = true;
        }

        function setupSectionLogic(gridId, btnId) {
            const grid = document.getElementById(gridId);
            const btn = document.getElementById(btnId);
            if(!grid) return;

            const limit = getLimitBasedOnGridId(gridId);
            const items = grid.querySelectorAll('.game-card');

            // Initial Hide > Limit
            items.forEach((item, index) => {
                if (index >= limit) {
                    item.style.display = 'none';
                } else {
                    item.style.display = 'block';
                }
            });

            // If items are few, hide button
            if (items.length <= limit) {
                if(btn) btn.style.display = 'none';
                return;
            } else {
                if(btn) btn.style.display = 'block';
            }

            // Replace button to clear old events
            if(!btn) return;
            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);

            // Click Event with Accordion Logic
            newBtn.addEventListener('click', () => {
                const isCurrentlyExpanded = expandedSections[gridId];

                if (!isCurrentlyExpanded) {
                    // 1. Close ALL other sections first
                    const allGrids = document.querySelectorAll('[id^="grid-"]');
                    allGrids.forEach(g => {
                        if (g.id !== gridId && expandedSections[g.id]) {
                            const otherBtnId = g.id.replace('grid-', 'btn-');
                            const otherBtn = document.getElementById(otherBtnId);
                            collapseSection(g.id, otherBtn);
                        }
                    });

                    // 2. Expand this section
                    expandSection(gridId, newBtn);

                } else {
                    // Just collapse this section
                    collapseSection(gridId, newBtn);

                    // Auto scroll to header for better UX
                    const sectionHeader = grid.previousElementSibling;
                    if(sectionHeader && window.innerWidth < 1024) sectionHeader.scrollIntoView({behavior: 'smooth', block: 'center'});
                }
            });
        }

        function initAllSections() {
            // Dynamically collect all grids and buttons
            const allGrids = document.querySelectorAll('[id^="grid-"]');
            allGrids.forEach(grid => {
                const gridId = grid.id;
                const btnId = gridId.replace('grid-', 'btn-');
                setupSectionLogic(gridId, btnId);
            });
        }

        // Run Initialization
        initAllSections();

        // Re-init on resize to adjust limits if needed
        let resizeTimer;
        window.addEventListener('resize', () => {
             clearTimeout(resizeTimer);
             resizeTimer = setTimeout(() => {
                 initAllSections();
             }, 200);
        });

        // ==========================================================================
        // JS 5: SEARCH LOGIC
        // ==========================================================================
        const searchInput = document.getElementById('searchInput');
        const allSeeAllBtns = document.querySelectorAll('.see-all-btn');

        if(searchInput) {
            searchInput.addEventListener('keyup', (e) => {
                const term = e.target.value.toLowerCase();
                const sections = document.querySelectorAll('.filterable-section');

                if (term.length > 0) {
                    sections.forEach(sec => {
                        let hasVisibleItem = false;
                        const items = sec.querySelectorAll('.game-card');

                        items.forEach(item => {
                            const titleEl = item.querySelector('.game-title');
                            if(!titleEl) return;
                            const title = titleEl.innerText.toLowerCase();
                            const img = item.querySelector('img');

                            if (title.includes(term)) {
                                item.style.display = 'block';
                                hasVisibleItem = true;
                                // Force load image if searched
                                if(img && img.hasAttribute('data-src')) {
                                    img.src = img.dataset.src;
                                    img.removeAttribute('data-src');
                                }
                            } else {
                                item.style.display = 'none';
                            }
                        });

                        if (hasVisibleItem) {
                            sec.style.display = 'block';
                            sec.style.marginTop = '20px';
                        } else {
                            sec.style.display = 'none';
                        }
                    });
                    // Hide buttons during search
                    allSeeAllBtns.forEach(btn => btn.style.display = 'none');
                } else {
                    // Reset to normal state
                    initAllSections();
                    const activePill = document.querySelector('.cat-pill.active');
                    if(activePill) {
                         if(activePill.classList.contains('pill-all-products')) {
                             sections.forEach(sec => sec.style.display = 'block');
                         }
                    }
                    allSeeAllBtns.forEach(btn => btn.style.display = 'block');
                }
            });

            // Intercept Enter key to submit search form
            searchInput.addEventListener('keydown', (e) => {
                if(e.key === 'Enter') {
                    const form = searchInput.closest('form');
                    if(form) form.submit();
                }
            });
        }

        // ==========================================================================
        // JS 6: FAQ TOGGLE
        // ==========================================================================
        const faqHeaders = document.querySelectorAll('.faq-header');
        faqHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const currentItem = this.parentElement;
                const allItems = document.querySelectorAll('.faq-item');
                const isAlreadyActive = currentItem.classList.contains('active');
                allItems.forEach(item => item.classList.remove('active'));
                if (!isAlreadyActive) {
                    currentItem.classList.add('active');
                }
            });
        });

        // ==========================================================================
        // JS 7: NAV ACTIVE STATE
        // ==========================================================================
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // ==========================================================================
        // JS 8: SCROLL TO TOP (FIXED VISIBILITY & POSITION)
        // ==========================================================================
        const scrollTopBtn = document.getElementById('scroll-top-btn');
        if (scrollTopBtn) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) {
                    scrollTopBtn.classList.add('visible');
                } else {
                    scrollTopBtn.classList.remove('visible');
                }
            });

            scrollTopBtn.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }

        // ==========================================================================
        // JS 9: PAGE MODAL (FOR FOOTER LINKS)
        // ==========================================================================
        const pageContents = {
            @if(isset($pages) && $pages->count() > 0)
                @foreach($pages as $page)
                    {{ $page->id }}: {!! json_encode($page->content) !!},
                @endforeach
            @endif
        };

        function showPageContent(el) {
            const title = el.dataset.pageTitle;
            const id = el.dataset.pageId;
            document.getElementById('pageModalTitle').textContent = title;
            document.getElementById('pageModalContent').innerHTML = pageContents[id] || '';
            document.getElementById('pageModal').style.display = 'flex';
        }

        function closePageModal() {
            document.getElementById('pageModal').style.display = 'none';
        }

        document.getElementById('pageModal').addEventListener('click', function(e) {
            if (e.target === this) closePageModal();
        });
    </script>
</body>
</html>
