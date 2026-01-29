# Setting Module

This module centralises every piece of configuration that powers the dashboard, frontend shells, and transactional services.

## What lives here?

- **General identity:** Application name, description, slogan, hero copy – all synced with `config('app.*')`.
- **Localization:** Supported locales, default/fallback locale, RTL metadata. When only one locale is enabled the language switcher disappears automatically.
- **Contact sheet:** Multilingual addresses plus inbox/support/WhatsApp numbers injected into layouts and emails.
- **Mail stack:** SMTP driver, encryption, credentials, and “from” identity. These values override the global `mail` config at runtime.
- **Branding assets:** Main logo, white logo, favicon, and footer copy. Uploads are stored under `storage/app/public/branding` and automatically resized/optimized via Intervention Image.
- **Social links:** Structured links for Facebook, Twitter/X, Instagram, YouTube, Snapchat, and TikTok.
- **Custom code:** Raw CSS/JS slots (`<head>` CSS, JS before `</head>`, JS before `</body>`) for rapid snippets or tracking pixels.

All values are cached via `SettingStore`. When any setting changes we flush and repopulate the cache so downstream helpers (`setting('app_name')`, `available_locales()`, etc.) pick up the updates instantly.

## Upload pipeline

`Modules\Setting\Services\Media\MediaUploader` handles every upload:

1. Validates and renames the file with a UUID.
2. Runs the Intervention Image pipeline (resize + re-encode) for images.
3. Stores the file on the configured disk (defaults to `public`).
4. Returns an `UploadedMedia` DTO containing the path, URL, mime, and size.
5. Supports optional chunked uploads via `appendChunk()` for Dropzone/FilePond style clients.

You can reuse the uploader anywhere else in the app:

```php
$upload = app(MediaUploader::class)->upload($request->file('avatar'), 'users/avatars', ['max_width' => 512]);
User::update(['avatar' => $upload->path()]);
```

## Adding new settings

1. Add your default values to `config/setting.php`.
2. Expose the field in `UpdateSettingsRequest` to validate user input.
3. Persist the value through `SettingStore` (call `set` or `setMany`).
4. If the setting should update runtime config (mail/services/etc.), extend `applyRuntimeConfiguration()` within `SettingServiceProvider`.
5. Surface the control inside `resources/views/setting/index.blade.php` (create a new tab or extend an existing card).

Because the dashboard form uses standard Blade inputs, `old()` + error messages work automatically and success messages show through the global alerts partial.
