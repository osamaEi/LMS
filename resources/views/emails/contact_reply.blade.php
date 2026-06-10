<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>رد على رسالتك</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:'Segoe UI',Tahoma,Arial,sans-serif;direction:rtl;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 16px;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

        {{-- ── Header / Logo ── --}}
        <tr>
          <td style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 60%,#0f2744 100%);border-radius:16px 16px 0 0;padding:32px 40px;text-align:center;">
            <img src="{{ $logoUrl }}" alt="Logo" style="height:52px;max-width:180px;object-fit:contain;display:inline-block;">
            <p style="margin:14px 0 0;color:#94a3b8;font-size:13px;letter-spacing:.04em;">منصة التدريب والتطوير</p>
          </td>
        </tr>

        {{-- ── Body ── --}}
        <tr>
          <td style="background:#ffffff;padding:40px;">

            {{-- Greeting --}}
            <p style="margin:0 0 8px;font-size:22px;font-weight:700;color:#1e293b;">
              مرحباً {{ $recipientName }}،
            </p>
            <p style="margin:0 0 28px;font-size:14px;color:#64748b;line-height:1.7;">
              شكراً لتواصلك معنا. يسعدنا الرد على رسالتك التي أرسلتها بخصوص:
              <strong style="color:#1e293b;">"{{ $subject }}"</strong>
            </p>

            {{-- Divider --}}
            <div style="height:1px;background:linear-gradient(90deg,transparent,#e2e8f0,transparent);margin-bottom:28px;"></div>

            {{-- Reply box --}}
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-right:4px solid #0071AA;border-radius:12px;padding:24px 28px;margin-bottom:28px;">
              <p style="margin:0 0 10px;font-size:11px;font-weight:700;color:#0071AA;text-transform:uppercase;letter-spacing:.08em;">رد فريق الدعم</p>
              <p style="margin:0;font-size:15px;color:#374151;line-height:1.85;white-space:pre-line;">{{ $replyText }}</p>
            </div>

            {{-- Divider --}}
            <div style="height:1px;background:#f1f5f9;margin-bottom:28px;"></div>

            {{-- Original message reference --}}
            <p style="margin:0 0 10px;font-size:12px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;">رسالتك الأصلية</p>
            <div style="background:#fafafa;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;margin-bottom:32px;">
              <p style="margin:0;font-size:13px;color:#94a3b8;line-height:1.8;white-space:pre-line;">{{ $originalMessage }}</p>
            </div>

            {{-- CTA --}}
            <div style="text-align:center;margin-bottom:32px;">
              <a href="{{ config('app.url') }}"
                 style="display:inline-block;background:#0071AA;color:#ffffff;text-decoration:none;padding:14px 36px;border-radius:10px;font-size:14px;font-weight:700;letter-spacing:.02em;">
                زيارة المنصة
              </a>
            </div>

            <p style="margin:0;font-size:13px;color:#64748b;line-height:1.7;text-align:center;">
              إذا كان لديك أي استفسار إضافي، لا تتردد في التواصل معنا مجدداً.<br>
              نحن هنا لمساعدتك دائماً.
            </p>

          </td>
        </tr>

        {{-- ── Footer ── --}}
        <tr>
          <td style="background:#f8fafc;border:1px solid #e2e8f0;border-top:none;border-radius:0 0 16px 16px;padding:24px 40px;text-align:center;">
            <p style="margin:0 0 6px;font-size:13px;font-weight:700;color:#1e293b;">{{ config('app.name') }}</p>
            <p style="margin:0;font-size:12px;color:#94a3b8;">
              هذه رسالة آلية رداً على تواصلك معنا · لا ترد على هذا البريد مباشرة
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
