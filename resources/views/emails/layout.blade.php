<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <style>
        body { margin: 0; padding: 0; background-color: #F2F2F7; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; text-align: center; color: #1D1D1F; }
        table { border-collapse: separate; border-spacing: 0; }
        img { border: 0; outline: none; text-decoration: none; }
        a { text-decoration: none; }
        p { margin: 0; }
        .wrapper { width: 100%; max-width: 500px; margin: 0 auto; background-color: #F2F2F7; padding-bottom: 60px; }
        .card { background-color: #FFFFFF; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.05); margin-bottom: 20px; width: 100%; }
        .card-padding { padding: 30px 25px; }
        .card-center { padding: 30px 25px; text-align: center; }

        .bg-done { background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%); padding: 35px 20px; color: #fff; }
        .bg-cancel { background: linear-gradient(135deg, #cb2d3e 0%, #ef473a 100%); padding: 35px 20px; color: #fff; }
        .bg-pending { background: linear-gradient(135deg, #FF8008 0%, #FFC837 100%); padding: 35px 20px; color: #fff; }
        .bg-success { background: linear-gradient(135deg, #00C6FF 0%, #0072FF 100%); padding: 35px 20px; color: #fff; }
        .bg-otp { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 35px 20px; color: #fff; }
        .bg-reset { background: linear-gradient(135deg, #FF416C 0%, #FF4B2B 100%); padding: 35px 20px; color: #fff; }
        .bg-welcome { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); padding: 35px 20px; color: #fff; }
        .bg-delivered { background: linear-gradient(135deg, #0072FF 0%, #00C6FF 100%); padding: 35px 20px; color: #fff; }

        .company-name { font-size: 18px; font-weight: 800; color: #1D1D1F; margin-top: 15px; letter-spacing: -0.5px; }
        .header-title { font-size: 22px; font-weight: 800; margin: 15px 0 8px 0; text-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header-desc { font-size: 14px; opacity: 0.95; line-height: 1.5; margin-bottom: 10px; }
        .section-label { font-size: 12px; font-weight: 700; color: #9CA3AF; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 15px; text-align: center; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-row td { border-bottom: 1px solid #F3F4F6; padding: 12px 0; }
        .data-row:last-child td { border-bottom: none; }
        .label { font-size: 13px; color: #6B7280; text-align: left; vertical-align: middle; }
        .value { font-size: 14px; font-weight: 600; color: #1F2937; text-align: right; vertical-align: middle; }
        .highlight-price { font-size: 18px; font-weight: 800; color: #0072FF; text-align: right; }
        .pin-value { font-family: monospace; font-size: 16px; letter-spacing: 1px; background: #F3F4F6; padding: 4px 8px; border-radius: 6px; color: #0072FF; }
        .btn { display: inline-block; padding: 12px 30px; border-radius: 50px; font-weight: 700; font-size: 14px; text-decoration: none; margin-top: 15px; }
        .btn-white { background-color: #FFFFFF; color: #333; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .btn-download { display: inline-block; margin-top: 15px; font-weight: 600; padding: 10px 25px; border-radius: 50px; font-size: 13px; box-shadow: 0 4px 10px rgba(0,114,255,0.2); text-decoration: none; background-color: #0072FF; color: white; }
        .icon-hero { width: 72px; height: 72px; margin-bottom: 5px; display: block; margin-left: auto; margin-right: auto; }
        .icon-circle { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto; }
        .circle-yellow { background-color: #FFF9C4; }
        .circle-blue { background-color: #E3F2FD; }
        .widget-icon { width: 28px; height: 28px; display: block; margin: 11px auto; }
        .widget-title { font-size: 16px; font-weight: 700; color: #1D1D1F; margin-bottom: 5px; }
        .widget-desc { font-size: 13px; color: #86868b; line-height: 1.4; }
        .social-icon { width: 32px; height: 32px; display: block; }
    </style>
</head>
<body style="padding: 40px 10px;">
    <div class="wrapper">
        <!-- Logo -->
        <div style="margin-bottom: 30px;">
            <img src="https://marspedia.id/storage/assets/logo/e9f5dd334125ee48029ede0de303b548.png" alt="Marspedia" width="90" style="border-radius: 22px; box-shadow: 0 8px 25px rgba(0,0,0,0.08); display: block; margin: 0 auto;">
            <div class="company-name">PT Marspedia Digital Indonesia</div>
            <p style="font-size: 13px; color: #6B7280; margin-top: 5px;">Your Trusted Digital Partner</p>
        </div>

        @yield('content')

        <!-- Footer -->
        <div style="margin-top: 40px; border-top: 1px solid #E5E7EB; padding-top: 30px;">
            <p style="font-size: 11px; font-weight: 700; color: #9CA3AF; margin-bottom: 20px; letter-spacing: 1px;">IKUTI KAMI</p>
            <table border="0" cellpadding="0" cellspacing="0" align="center">
                <tr>
                    <td style="padding: 0 5px;"><a href="mailto:finance@marspedia.id"><img src="https://img.icons8.com/fluency/48/mail.png" class="social-icon" alt="Email"></a></td>
                    <td style="padding: 0 5px;"><a href="https://wa.me/6282210109289"><img src="https://img.icons8.com/color/48/whatsapp--v1.png" class="social-icon" alt="WhatsApp"></a></td>
                    <td style="padding: 0 5px;"><a href="#"><img src="https://img.icons8.com/color/48/facebook-new.png" class="social-icon" alt="Facebook"></a></td>
                    <td style="padding: 0 5px;"><a href="#"><img src="https://img.icons8.com/fluency/48/instagram-new.png" class="social-icon" alt="Instagram"></a></td>
                    <td style="padding: 0 5px;"><a href="#"><img src="https://img.icons8.com/color/48/tiktok--v1.png" class="social-icon" alt="TikTok"></a></td>
                    <td style="padding: 0 5px;"><a href="#"><img src="https://img.icons8.com/color/48/youtube-play.png" class="social-icon" alt="YouTube"></a></td>
                    <td style="padding: 0 5px;"><a href="#"><img src="https://img.icons8.com/color/48/telegram-app.png" class="social-icon" alt="Telegram"></a></td>
                </tr>
            </table>
            <div style="font-size: 11px; color: #9CA3AF; line-height: 1.8; margin-top: 20px;">
                <strong>PT MARSPEDIA DIGITAL INDONESIA</strong><br>
                Menara Mandiri, Jl. Jend. Sudirman No.Kav.54-55<br>
                Jakarta Selatan, Daerah Khusus Ibukota Jakarta, 12190
            </div>
            <p style="font-size: 10px; color: #D1D5DB; margin-top: 20px;">
                &copy; {{ date('Y') }} PT Marspedia Digital Indonesia. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
