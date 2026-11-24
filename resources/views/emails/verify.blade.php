<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Confirm your email</title></head>
<body style="margin:0;padding:0;background-color:#f6f3ec;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f6f3ec;padding:32px 12px;">
        <tr><td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background-color:#ffffff;border-radius:16px;overflow:hidden;font-family:Arial,Helvetica,sans-serif;color:#1a1a1a;">
                <tr><td bgcolor="#FF6200" style="background-color:#FF6200;padding:26px 32px;"><span style="font-size:24px;font-weight:800;letter-spacing:4px;color:#ffffff;">ARTIVO</span></td></tr>
                <tr><td style="padding:32px;">
                    <h1 style="margin:0 0 12px;font-size:22px;color:#1a1a1a;">Confirm your email, {{ \Illuminate\Support\Str::before($user->name, ' ') }}</h1>
                    <p style="margin:0 0 22px;font-size:15px;line-height:1.6;color:#4d4d4d;">Press the button to confirm that this inbox is yours. After that, the receipt of every paid order is sent here automatically.</p>
                    <p style="margin:0 0 22px;text-align:center;"><a href="{{ $url }}" style="display:inline-block;background-color:#FF6200;color:#ffffff;text-decoration:none;font-weight:bold;font-size:15px;padding:13px 28px;border-radius:10px;">Confirm my email</a></p>
                    <p style="margin:0;font-size:12px;line-height:1.6;color:#8a8a8a;">The link works for one hour. If you did not create an Artivo account, you can ignore this email.</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
