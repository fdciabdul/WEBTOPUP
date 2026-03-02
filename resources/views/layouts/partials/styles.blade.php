    <style>
/* Variables & Theme */
:root {
    --brand-primary: #0033AA;
    --brand-secondary: #002288;
    --brand-accent: #3366FF;
    --ios-bg: #F2F2F7;
    --text-main: #1C1C1E;
    --text-sub: #8E8E93;
    --radius-icon: 22px;
    --radius-card: 20px;
    --app-width: 480px;
    --header-height: 225px;
    --safe-area-bottom: 34px;
    --section-spacing: 50px;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    -webkit-tap-highlight-color: transparent;
    outline: none;
}

body {
    font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif;
    background-color: #EEF1F5;
    color: var(--text-main);
    min-height: 100vh;
    padding: 0;
    margin: 0;
    overflow-x: hidden;
}

#app-container {
    max-width: var(--app-width);
    margin: 0 auto;
    background-color: var(--ios-bg);
    min-height: 100vh;
    position: relative;
    box-shadow: 0 0 50px rgba(0,0,0,0.1);
}

/* Header (Fixed) */
.header {
    position: fixed;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
    max-width: var(--app-width);
    z-index: 1000;
    background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
    padding: 20px 20px 12px 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    border-bottom: 0.5px solid rgba(255,255,255,0.2);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
}

.header-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.user-info h1 {
    font-size: 24px;
    font-weight: 800;
    color: #FFFFFF;
    margin-bottom: 2px;
    letter-spacing: -0.5px;
}

.user-info p {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.85);
    font-weight: 500;
}

.action-icons {
    display: flex;
    gap: 8px;
}

.auth-btn-mobile {
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 8px 14px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
    color: #FFFFFF;
    border: 0.5px solid rgba(255,255,255,0.3);
    transition: all 0.3s;
}

.auth-btn-mobile:active {
    transform: scale(0.95);
}

.search-row {
    display: flex;
    gap: 10px;
    align-items: center;
}

.search-container {
    flex: 1;
    position: relative;
}

.search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(0, 51, 170, 0.5);
    font-size: 16px;
    pointer-events: none;
}

.search-input {
    width: 100%;
    padding: 12px 16px 12px 42px;
    background: #FFFFFF;
    border: none;
    border-radius: 14px;
    font-size: 15px;
    font-family: inherit;
    color: var(--text-main);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s;
}

.search-input::placeholder {
    color: #B5B5B5;
}

.search-input:focus {
    box-shadow: 0 4px 16px rgba(0, 51, 170, 0.15);
}

.filter-btn {
    width: 44px;
    height: 44px;
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #FFFFFF;
    font-size: 18px;
    cursor: pointer;
    border: 0.5px solid rgba(255,255,255,0.3);
    transition: all 0.2s;
}

.filter-btn:active {
    transform: scale(0.9);
}

.category-wrapper {
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}

.category-wrapper::-webkit-scrollbar {
    display: none;
}

.category-scroll {
    display: flex;
    gap: 8px;
    padding-bottom: 8px;
    min-width: max-content;
}

.cat-pill {
    padding: 8px 16px;
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    color: #FFFFFF;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.3s;
    border: 0.5px solid rgba(255,255,255,0.3);
    display: flex;
    align-items: center;
}

.cat-pill.active {
    background: #FFFFFF;
    color: var(--brand-primary);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    border: 0.5px solid rgba(0, 51, 170, 0.2);
}

.cat-pill:active {
    transform: scale(0.95);
}

/* Desktop Navigation - Hidden on Mobile */
.desktop-nav {
    display: none;
}

/* Main Content */
main {
    padding-top: var(--header-height);
    padding-bottom: calc(80px + var(--safe-area-bottom));
}

.section-header {
    padding: 20px 20px 12px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.section-title {
    font-size: 22px;
    font-weight: 700;
    color: var(--text-main);
    letter-spacing: -0.02em;
}

.section-content {
    margin-bottom: var(--section-spacing);
    animation: fadeIn 0.3s ease;
}

/* Category Section */
.category-section {
    margin-bottom: 30px;
}

.category-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px 16px 20px;
    margin-top: 10px;
}

.category-name {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-main);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.category-name i {
    color: var(--brand-primary);
    font-size: 16px;
}

.category-count {
    font-size: 13px;
    color: var(--text-sub);
    font-weight: 500;
}

.category-separator {
    height: 1px;
    background: linear-gradient(to right, transparent, rgba(0, 0, 0, 0.08) 50%, transparent);
    margin: 40px 20px;
    position: relative;
}

.category-separator::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 40px;
    height: 4px;
    background: var(--brand-primary);
    border-radius: 2px;
    opacity: 0.3;
}

/* Game Grid */
.game-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 36px 12px;
    padding: 0 20px;
}

.game-card {
    background: transparent;
    text-align: center;
    cursor: pointer;
    position: relative;
    text-decoration: none;
}

.game-icon-wrap {
    width: 80px;
    height: 80px;
    margin: 0 auto 12px auto;
    border-radius: var(--radius-icon);
    overflow: hidden;
    background: #FFFFFF;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06), inset 0 0 0 1px rgba(0,0,0,0.04);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    position: relative;
    border: 2px solid transparent;
}

.game-icon-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: opacity 0.3s;
}

.game-title {
    font-size: 11px;
    font-weight: 500;
    color: #000000;
    line-height: 1.3;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.game-card:active .game-icon-wrap {
    transform: scale(0.92);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.game-card.hidden {
    display: none;
}

/* Media Section */
.media-section {
    padding: 0 20px;
    margin-bottom: 40px;
    text-align: center;
}

.media-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-sub);
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.media-logos {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 30px;
    flex-wrap: wrap;
}

.media-logo-item {
    width: 40px;
    height: 40px;
    object-fit: contain;
    opacity: 0.5;
    filter: grayscale(100%);
    transition: all 0.3s;
}

.media-logo-item:hover {
    opacity: 0.8;
    filter: grayscale(0%);
}

/* About Section */
.about-section {
    padding: 0 20px;
    margin-bottom: 40px;
}

.about-card {
    background: #FFFFFF;
    border-radius: var(--radius-card);
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}

.about-content {
    display: flex;
    flex-direction: column;
    gap: 20px;
    align-items: center;
    text-align: center;
}

.about-logo img {
    width: 80px;
    height: 80px;
    border-radius: 16px;
    object-fit: cover;
}

.about-text h3 {
    font-size: 20px;
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: 8px;
}

.about-text p {
    font-size: 14px;
    color: var(--text-sub);
    line-height: 1.6;
    margin-bottom: 16px;
}

.social-row {
    display: flex;
    gap: 12px;
    justify-content: center;
}

.social-link {
    width: 40px;
    height: 40px;
    background: var(--ios-bg);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--brand-primary);
    font-size: 18px;
    text-decoration: none;
    transition: all 0.3s;
}

.social-link:active {
    transform: scale(0.9);
    background: var(--brand-primary);
    color: #FFFFFF;
}

/* FAQ Section */
.faq-wrapper {
    padding: 0 20px;
    margin-bottom: 40px;
}

.faq-item {
    background: #FFFFFF;
    border-radius: 16px;
    margin-bottom: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.faq-header {
    padding: 18px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    font-weight: 600;
    font-size: 15px;
    color: var(--text-main);
}

.faq-header i {
    transition: transform 0.3s;
    color: var(--text-sub);
}

.faq-body {
    padding: 0 20px 18px 20px;
    font-size: 14px;
    color: var(--text-sub);
    line-height: 1.6;
    display: none;
}

.faq-item.active .faq-body {
    display: block;
}

.faq-item.active .faq-header i {
    transform: rotate(180deg);
    color: var(--brand-primary);
}

/* Footer */
.footer-section {
    padding: 30px 20px 40px 20px;
    text-align: center;
    color: #94A3B8;
    font-size: 14px;
    line-height: 1.6;
    background: transparent;
}

.footer-copyright {
    font-weight: 600;
    margin-bottom: 8px;
    color: #64748B;
    font-size: 14px;
}

.footer-links {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 10px;
    flex-wrap: wrap;
    font-size: 13px;
}

.footer-links a {
    color: var(--brand-primary);
    text-decoration: none;
    font-weight: 500;
}

/* Bottom Navigation */
.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
    max-width: var(--app-width);
    height: calc(65px + var(--safe-area-bottom));
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-top: 0.5px solid rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-around;
    align-items: flex-start;
    padding-top: 12px;
    z-index: 999;
}

.bottom-nav::after {
    content: '';
    position: absolute;
    bottom: 8px;
    left: 50%;
    transform: translateX(-50%);
    width: 130px;
    height: 5px;
    background: #000;
    border-radius: 10px;
    opacity: 0.2;
}

.nav-item {
    flex: 1;
    text-align: center;
    color: #999999;
    font-size: 10px;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    transition: all 0.3s;
}

.nav-item i {
    font-size: 24px;
    margin-bottom: 2px;
    transition: transform 0.2s;
}

.nav-item:active i {
    transform: scale(0.9);
}

.nav-item span {
    font-weight: 500;
}

.nav-item.active {
    color: var(--brand-primary);
}

.nav-center-wrapper {
    position: relative;
    flex: 1;
    display: flex;
    justify-content: center;
}

.nav-center-btn {
    position: absolute;
    top: -28px;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-accent));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(0, 51, 170, 0.4);
    color: #fff;
    font-size: 24px;
    cursor: pointer;
    transition: transform 0.1s;
    border: 4px solid var(--ios-bg);
    text-decoration: none;
}

.nav-center-btn:active {
    transform: scale(0.9);
}

/* Scroll to Top */
#scroll-top-btn {
    position: fixed;
    bottom: 100px;
    right: 20px;
    width: 50px;
    height: 50px;
    background: var(--brand-primary);
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 18px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 51, 170, 0.3);
    z-index: 998;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
}

#scroll-top-btn.visible {
    opacity: 1;
    visibility: visible;
}

#scroll-top-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(0, 51, 170, 0.5);
}

/* Touch Effect */
.touch-effect:active {
    opacity: 0.7;
}

/* Animations */
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

/* Responsive */
@media (max-width: 360px) {
    .game-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px 8px;
    }
    .header {
        padding: 16px 16px 10px 16px;
    }
}

@media (min-width: 768px) {
    :root {
        --app-width: 100%;
        --header-height: 120px;
    }

    #app-container {
        max-width: 100%;
        box-shadow: none;
    }

    body {
        background-color: var(--ios-bg);
    }

    .game-grid {
        grid-template-columns: repeat(6, 1fr);
        gap: 40px 20px;
        padding: 0 40px;
    }

    .game-icon-wrap {
        width: 90px;
        height: 90px;
    }

    .game-title {
        font-size: 12px;
    }

    /* Desktop Header - Two Rows */
    .header {
        flex-direction: column;
        align-items: stretch;
        padding: 0;
        gap: 0;
        height: auto;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border-bottom: none;
    }

    /* Top Navigation Bar */
    .header-top {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        padding: 12px 40px;
        gap: 24px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .user-info {
        flex: 0 0 auto;
    }

    .user-info h1 {
        font-size: 20px;
        margin-bottom: 0;
    }

    .user-info p {
        display: none;
    }

    /* Desktop Navigation */
    .desktop-nav {
        display: flex;
        gap: 8px;
        align-items: center;
        flex: 1;
        justify-content: center;
        max-width: 600px;
        margin: 0 auto;
    }

    .nav-link-desktop {
        color: #FFFFFF;
        text-decoration: none;
        padding: 8px 20px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s ease;
        background: rgba(255, 255, 255, 0.1);
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .nav-link-desktop:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-1px);
    }

    .nav-link-desktop.active {
        background: #FFFFFF;
        color: var(--brand-primary);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .nav-link-desktop i {
        font-size: 16px;
    }

    .action-icons {
        display: flex;
        align-items: center;
        flex: 0 0 auto;
    }

    .auth-btn-mobile {
        padding: 8px 16px;
    }

    .auth-btn-mobile span {
        display: inline;
    }

    /* Search Row */
    .search-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 10px 40px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .search-container {
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
    }

    .filter-btn {
        display: none;
    }

    /* Category Pills Below Search */
    .category-wrapper {
        padding: 10px 40px;
        overflow-x: auto;
        overflow-y: hidden;
        display: flex;
        justify-content: center;
    }

    .category-scroll {
        max-width: 1200px;
    }

    .category-wrapper::-webkit-scrollbar {
        height: 4px;
    }

    .category-wrapper::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 2px;
    }

    .category-wrapper::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 2px;
    }

    .category-wrapper::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    .category-scroll {
        justify-content: flex-start;
        padding-bottom: 0;
        gap: 8px;
    }

    .cat-pill {
        font-size: 12px;
        padding: 6px 14px;
        white-space: nowrap;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        transition: all 0.2s ease;
    }

    .cat-pill:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-1px);
    }

    .cat-pill.active {
        background: #FFFFFF;
        color: var(--brand-primary);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .category-header,
    .section-header {
        padding-left: 40px;
        padding-right: 40px;
    }

    .category-separator {
        margin: 40px 40px;
    }

    .media-section,
    .about-section,
    .faq-wrapper,
    .footer-section {
        padding-left: 40px;
        padding-right: 40px;
    }

    .bottom-nav {
        display: none;
    }

    main {
        padding-bottom: 40px;
    }

    #scroll-top-btn {
        bottom: 40px;
        right: 40px;
        width: 56px;
        height: 56px;
        font-size: 20px;
    }
}

@media (min-width: 1200px) {
    .game-grid {
        grid-template-columns: repeat(8, 1fr);
        gap: 40px 24px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .game-icon-wrap {
        width: 100px;
        height: 100px;
    }

    .game-title {
        font-size: 13px;
    }

    .header-top {
        padding: 14px 80px;
    }

    .user-info h1 {
        font-size: 22px;
    }

    .nav-link-desktop {
        font-size: 15px;
        padding: 9px 22px;
    }

    .search-row {
        padding: 12px 80px;
    }

    .search-container {
        max-width: 700px;
    }

    .category-wrapper {
        padding: 12px 80px;
    }

    .category-scroll {
        max-width: 1400px;
    }

    .cat-pill {
        font-size: 13px;
        padding: 7px 16px;
    }

    .section-header,
    .category-header {
        max-width: 1400px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 80px;
        padding-right: 80px;
    }

    .category-separator {
        max-width: 1400px;
        margin-left: auto;
        margin-right: auto;
    }

    .game-grid {
        padding: 0 80px;
    }

    .media-section,
    .about-section,
    .faq-wrapper,
    .footer-section {
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 80px;
        padding-right: 80px;
    }
}

@media (min-width: 1440px) {
    .header-top,
    .search-row,
    .category-wrapper {
        padding-left: 100px;
        padding-right: 100px;
    }

    .game-grid {
        padding: 0 100px;
        max-width: 1600px;
    }

    .section-header,
