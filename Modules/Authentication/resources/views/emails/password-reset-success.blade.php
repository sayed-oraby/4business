<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 32px 24px; background: #ffffff; border-radius: 12px; }
        .panel { border: 1px solid #e2e8f0; padding: 16px; border-radius: 8px; background: #f8fafc; font-weight: 600; }
    </style>
</head>
<body>
<div class="wrapper">
    <h1 style="margin:0 0 12px; color:#0f172a;">
        {{ __('authentication::messages.emails.success.subject', ['app' => config('app.name')]) }}
    </h1>
    <p style="margin:0 0 24px; color:#475569;">
        {{ __('authentication::messages.emails.success.intro') }}
    </p>
    <div class="panel">{{ $user->email }}</div>
    <p style="margin-top:24px; color:#475569;">
        {{ __('authentication::messages.emails.success.outro') }}
    </p>
</div>
</body>
</html>
