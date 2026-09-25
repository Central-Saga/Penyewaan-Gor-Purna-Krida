<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('judul', config('app.name'))</title>
</head>
<body style="margin:0; padding:0; background:#f1f5f9; font-family: Arial, Helvetica, sans-serif; color:#1e293b;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
                    <tr>
                        <td style="background:@yield('warna', '#0d6efd'); padding:20px 28px;">
                            <h1 style="margin:0; font-size:18px; color:#ffffff;">{{ config('app.name') }}</h1>
                            <p style="margin:4px 0 0; font-size:12px; color:#ffffff; opacity:0.85;">@yield('subjudul')</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            @yield('konten')
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 28px; background:#f8fafc; font-size:11px; color:#94a3b8; text-align:center;">
                            Email otomatis dari sistem {{ config('app.name') }} — DISDIKPORA Kabupaten Badung. Mohon tidak membalas email ini.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
