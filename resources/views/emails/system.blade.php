
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
</head>
<body style="margin:0;padding:32px 16px;background:#F7F3EA;font-family:Arial,sans-serif;color:#222;">
    <div style="max-width:600px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:32px;">
        <div style="font-size:22px;font-weight:700;letter-spacing:1px;margin-bottom:28px;">
            ACROVOY
        </div>

        <h1 style="font-size:22px;line-height:1.4;margin:0 0 20px;">
            {{ $title }}
        </h1>

        <p style="font-size:15px;line-height:1.7;white-space:pre-line;margin:0 0 24px;">
            {{ $messageText }}
        </p>

        @if ($actionUrl && $actionLabel)
            <p style="margin:28px 0;">
                <a href="{{ $actionUrl }}"
                   style="display:inline-block;padding:12px 20px;background:#111827;color:#fff;text-decoration:none;border-radius:8px;font-size:14px;">
                    {{ $actionLabel }}
                </a>
            </p>
        @endif

        <div style="border-top:1px solid #e5e7eb;padding-top:20px;margin-top:28px;font-size:12px;line-height:1.6;color:#6b7280;">
            This is an automated email from ACROVOY.
        </div>
    </div>
</body>
</html>
