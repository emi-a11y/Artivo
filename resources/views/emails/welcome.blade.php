@php $first = \Illuminate\Support\Str::before($user->name, ' '); @endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to Artivo</title>
</head>

<body style="margin:0;padding:0;background-color:#f6f3ec;">
    {{-- Email clients ignore most CSS files, so every style below is inline on purpose. --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background-color:#f6f3ec;padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:560px;background-color:#ffffff;border-radius:16px;overflow:hidden;font-family:Arial,Helvetica,sans-serif;color:#1a1a1a;">
                    <tr>
                        <td bgcolor="#FF6200" style="background-color:#FF6200;padding:28px 32px;">
                            <span style="font-size:26px;font-weight:800;letter-spacing:4px;color:#ffffff;">ARTIVO</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:36px 32px 8px;">
                            <h1 style="margin:0 0 12px;font-size:26px;line-height:1.25;color:#1a1a1a;">Welcome,
                                {{ $first }}!</h1>
                            <p style="margin:0 0 20px;font-size:16px;line-height:1.6;color:#4d4d4d;">
                                Your Artivo account is ready. We're glad to have you here. Artivo is a home for digital
                                art
                                made by independent artists, and now you can collect it.
                            </p>
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 24px;">
                                <tr>
                                    <td style="padding:6px 0;font-size:15px;line-height:1.5;color:#1a1a1a;"><span
                                            style="color:#FF6200;font-weight:700;">&#9679;</span>&nbsp; Browse artwork
                                        by category, genre and price</td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0;font-size:15px;line-height:1.5;color:#1a1a1a;"><span
                                            style="color:#FF6200;font-weight:700;">&#9679;</span>&nbsp; Save favourites
                                        to your wishlist</td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0;font-size:15px;line-height:1.5;color:#1a1a1a;"><span
                                            style="color:#FF6200;font-weight:700;">&#9679;</span>&nbsp; Choose a license
                                        and check out in a few clicks</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 32px 36px;">
                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td bgcolor="#FF6200" style="background-color:#FF6200;border-radius:10px;">
                                        <a href="{{ route('home') }}"
                                            style="display:inline-block;padding:14px 28px;font-size:16px;font-weight:700;color:#ffffff;text-decoration:none;">Start
                                            browsing</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td
                            style="padding:20px 32px;background-color:#faf6ee;font-size:12px;line-height:1.6;color:#6b6b6b;">
                            You are receiving this because an Artivo account was created with {{ $user->email }}.
                            If this wasn't you, you can ignore this email.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>