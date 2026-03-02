@php
    $footerText = \App\Models\Setting::get('footer_text', 'Platform top up game dan pulsa terpercaya di Indonesia');
    $socialLinks = [
        'whatsapp_url' => ['icon' => 'fab fa-whatsapp', 'label' => 'WhatsApp'],
        'instagram' => ['icon' => 'fab fa-instagram', 'label' => 'Instagram'],
        'facebook' => ['icon' => 'fab fa-facebook', 'label' => 'Facebook'],
        'tiktok' => ['icon' => 'fab fa-tiktok', 'label' => 'TikTok'],
        'telegram' => ['icon' => 'fab fa-telegram', 'label' => 'Telegram'],
        'youtube' => ['icon' => 'fab fa-youtube', 'label' => 'YouTube'],
    ];
    $contactEmail = \App\Models\Setting::get('contact_email');
@endphp

<footer class="site-footer">
    <div class="footer-content">
        <div class="footer-section">
            <h3>{{ config('app.name') }}</h3>
            <p>{!! $footerText !!}</p>
        </div>

        <div class="footer-section">
            <h4>Layanan</h4>
            <ul>
                <li><a href="{{ route('home') }}">Top Up Game</a></li>
                <li><a href="{{ route('home') }}">Pulsa & Paket Data</a></li>
                <li><a href="{{ route('home') }}">E-Wallet</a></li>
                <li><a href="{{ route('home') }}">Voucher</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Bantuan</h4>
            <ul>
                <li><a href="{{ route('track.order') }}">Cek Pesanan</a></li>
                <li><a href="{{ route('home') }}#faq-section">FAQ</a></li>
                <li><a href="{{ route('home') }}">Cara Order</a></li>
                @if($contactEmail)
                    <li><a href="mailto:{{ $contactEmail }}">Hubungi Kami</a></li>
                @endif
            </ul>
        </div>

        <div class="footer-section">
            <h4>Ikuti Kami</h4>
            <div class="social-links">
                @foreach($socialLinks as $key => $info)
                    @php $url = \App\Models\Setting::get($key); @endphp
                    @if($url)
                    <a href="{{ $url }}" target="_blank" rel="noopener" class="social-link" title="{{ $info['label'] }}">
                        <i class="{{ $info['icon'] }}"></i>
                    </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</footer>

<style>
.site-footer {
    background: #1E293B;
    color: white;
    padding: 60px 40px 20px;
    margin-top: 80px;
}
.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 40px;
    max-width: 1200px;
    margin: 0 auto 40px;
}
.footer-section h3 {
    font-size: 24px;
    margin-bottom: 16px;
    color: #0033AA;
}
.footer-section h4 {
    font-size: 18px;
    margin-bottom: 16px;
}
.footer-section p {
    color: #CBD5E1;
    line-height: 1.6;
}
.footer-section ul {
    list-style: none;
    padding: 0;
}
.footer-section ul li {
    margin-bottom: 10px;
}
.footer-section ul li a {
    color: #CBD5E1;
    text-decoration: none;
    transition: color 0.3s;
}
.footer-section ul li a:hover {
    color: #3366CC;
}
.social-links {
    display: flex;
    gap: 12px;
}
.social-link {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: all 0.3s;
}
.social-link:hover {
    background: #0033AA;
    transform: translateY(-3px);
}
.footer-bottom {
    text-align: center;
    padding-top: 30px;
    border-top: 1px solid rgba(255,255,255,0.1);
    color: #94A3B8;
}
@media (max-width: 768px) {
    .site-footer { padding: 40px 20px 15px; }
    .footer-content { gap: 30px; }
}
</style>
