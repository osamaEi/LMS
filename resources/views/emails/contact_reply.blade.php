<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>رد على رسالتك</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:'Cairo',Arial,sans-serif;direction:rtl;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 16px;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;font-family:'Cairo',Arial,sans-serif;">

        {{-- ── Header ── --}}
        <tr>
          <td style="background:linear-gradient(135deg,#0a1628 0%,#0f2744 50%,#0a1f3d 100%);border-radius:16px 16px 0 0;padding:36px 40px;text-align:center;">

            {{-- Logo --}}
            <img src="https://www.alertiqa.edu.sa/images/nav.png"
                 alt="{{ config('app.name') }}"
                 width="180"
                 style="display:inline-block;height:auto;max-height:64px;object-fit:contain;filter:brightness(0) invert(1);">

            {{-- Divider --}}
            <div style="width:48px;height:3px;background:linear-gradient(90deg,#0071AA,#38bdf8);border-radius:99px;margin:20px auto 0;"></div>
          </td>
        </tr>

        {{-- ── Body ── --}}
        <tr>
          <td style="background:#ffffff;padding:40px 44px;font-family:'Cairo',Arial,sans-serif;">

            {{-- Greeting --}}
            <h1 style="margin:0 0 6px;font-size:24px;font-weight:800;color:#0f172a;font-family:'Cairo',Arial,sans-serif;">
              مرحباً {{ $recipientName }} 👋
            </h1>
            <p style="margin:0 0 28px;font-size:14px;color:#64748b;line-height:1.8;font-family:'Cairo',Arial,sans-serif;">
              شكراً لتواصلك معنا. يسعدنا الرد على رسالتك بخصوص
              <strong style="color:#0f172a;">"{{ $subject }}"</strong>
            </p>

            {{-- Separator --}}
            <div style="height:1px;background:#e2e8f0;margin-bottom:28px;"></div>

            {{-- Reply Box --}}
            <div style="background:#f0f7ff;border:1px solid #bfdbfe;border-right:5px solid #0071AA;border-radius:12px;padding:24px 28px;margin-bottom:28px;">
              <p style="margin:0 0 12px;font-size:11px;font-weight:700;color:#0071AA;text-transform:uppercase;letter-spacing:.1em;font-family:'Cairo',Arial,sans-serif;">
                ✉️ رد فريق الدعم
              </p>
              <p style="margin:0;font-size:15px;color:#1e293b;line-height:2;white-space:pre-line;font-family:'Cairo',Arial,sans-serif;">{{ $replyText }}</p>
            </div>

            {{-- Original Message --}}
            <p style="margin:0 0 10px;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;font-family:'Cairo',Arial,sans-serif;">
              رسالتك الأصلية
            </p>
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:18px 22px;margin-bottom:32px;">
              <p style="margin:0;font-size:13px;color:#94a3b8;line-height:1.9;white-space:pre-line;font-family:'Cairo',Arial,sans-serif;">{{ $originalMessage }}</p>
            </div>

            {{-- Separator --}}
            <div style="height:1px;background:#e2e8f0;margin-bottom:28px;"></div>

            {{-- CTA --}}
            <div style="text-align:center;margin-bottom:32px;">
              <a href="{{ config('app.url') }}"
                 style="display:inline-block;background:#0071AA;color:#ffffff;text-decoration:none;padding:14px 40px;border-radius:10px;font-size:15px;font-weight:700;letter-spacing:.02em;font-family:'Cairo',Arial,sans-serif;">
                زيارة المنصة
              </a>
            </div>

            <p style="margin:0;font-size:13px;color:#94a3b8;line-height:1.9;text-align:center;font-family:'Cairo',Arial,sans-serif;">
              لديك استفسار آخر؟ لا تتردد في التواصل معنا مجدداً.<br>
              نحن هنا لمساعدتك دائماً 💙
            </p>

          </td>
        </tr>

        {{-- ── Footer ── --}}
        <tr>
          <td style="background:#f8fafc;border:1px solid #e2e8f0;border-top:none;border-radius:0 0 16px 16px;padding:24px 40px;text-align:center;font-family:'Cairo',Arial,sans-serif;">
            <img src="https://www.alertiqa.edu.sa/images/nav.png"
                 alt="{{ config('app.name') }}"
                 width="80"
                 style="display:inline-block;height:auto;max-height:30px;object-fit:contain;opacity:.5;margin-bottom:10px;filter:brightness(0);">
            <p style="margin:0 0 4px;font-size:13px;font-weight:700;color:#475569;font-family:'Cairo',Arial,sans-serif;">
              {{ config('app.name') }}
            </p>
            <p style="margin:0;font-size:11px;color:#94a3b8;line-height:1.7;font-family:'Cairo',Arial,sans-serif;">
              هذه رسالة آلية · لا ترد على هذا البريد مباشرة
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
