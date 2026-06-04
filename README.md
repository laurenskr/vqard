# Statamic vCard

A Statamic addon that adds digital business cards to any Statamic site — complete with a styled front-end template, `.vcf` download route, and auto-generated QR codes on entry save.

## Features

- **Collection + blueprint** — ready-made vCard collection with all standard contact fields
- **Styled template** — mobile-first card design, fully publishable and customisable
- **`.vcf` download** — automatic route at `/vcards/{slug}.vcf`, no setup required
- **QR code generation** — PNG QR code generated and attached as an asset every time an entry is saved
- **Configurable** — brand colours, field handles, route prefix, QR size/colour all in one config file

## Requirements

- PHP 8.1+
- Statamic 5.x
- GD extension (bundled with PHP, no Imagick needed)

## Installation

```bash
composer require yourname/statamic-vcard
```

Publish the config, blueprint, and template:

```bash
php artisan vendor:publish --tag=vcard
```

Create the collection:

```bash
php please make:collection vcards
```

Then assign the `vcard` template to the collection in the Statamic CP or in `content/collections/vcards.yaml`:

```yaml
template: vendor/vcard/vcard
```

## Configuration

After publishing, edit `config/vcard.php`:

```php
'brand' => [
    'name'         => 'My Company',    // first word gets a strikethrough in the logo
    'tagline'      => 'My tagline',
    'color_bg'     => '#b3d4f5',
    'color_dark'   => '#1a237e',
    'color_bright' => '#1e90ff',
    'color_mid'    => '#1565c0',
],

'qr_color' => [26, 35, 126],           // RGB foreground colour for QR codes

'fields' => [
    'name'    => 'title',              // map to your actual blueprint field handles
    'role'    => 'job_description',
    'mobile'  => 'mobile_phone',
    'phone'   => 'phone_number',
    'email'   => 'email',
    'website' => 'website',
    'address' => 'address',
    'org'     => 'org',
    'qr_code' => 'qr_code',
],
```

## Customising the template

The template is published to `resources/views/vendor/vcard/vcard.blade.php`. Edit it freely — the addon checks for a local override automatically, so your changes won't be overwritten on update.

## Asset container

The addon stores QR codes in your default `assets` container under a `qrcodes/` folder. To use a different container, update `config/vcard.php`:

```php
'asset_container' => 'your_container_handle',
'asset_folder'    => 'your_folder',
```

## Changelog

### 1.0.0
- Initial release

## License

MIT
