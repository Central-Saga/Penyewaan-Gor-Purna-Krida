<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Kontak</title>
</head>
<body style="margin:0; padding:0; background:#f1f5f9; font-family: Arial, Helvetica, sans-serif; color:#1e293b;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
                    <tr>
                        <td style="background:#0d6efd; padding:20px 28px;">
                            <h1 style="margin:0; font-size:18px; color:#ffffff;">{{ config('app.name') }}</h1>
                            <p style="margin:4px 0 0; font-size:12px; color:#cfe2ff;">Pesan baru dari formulir kontak publik</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;">
                                <tr>
                                    <td style="padding:6px 0; color:#64748b; width:140px;">Nama</td>
                                    <td style="padding:6px 0; font-weight:bold;">{{ $nama }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0; color:#64748b;">Email</td>
                                    <td style="padding:6px 0;"><a href="mailto:{{ $email }}" style="color:#0d6efd;">{{ $email }}</a></td>
                                </tr>
                                @if (filled($subjek))
                                    <tr>
                                        <td style="padding:6px 0; color:#64748b;">Subjek</td>
                                        <td style="padding:6px 0;">{{ $subjek }}</td>
                                    </tr>
                                @endif
                            </table>

                            <hr style="border:none; border-top:1px solid #e2e8f0; margin:20px 0;">

                            <p style="margin:0 0 8px; font-size:13px; color:#64748b;">Pesan:</p>
                            <div style="font-size:14px; line-height:1.6; white-space:pre-line;">{{ $pesan }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 28px; background:#f8fafc; font-size:11px; color:#94a3b8; text-align:center;">
                            Dikirim otomatis oleh sistem {{ config('app.name') }} — DISDIKPORA Kabupaten Badung.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
