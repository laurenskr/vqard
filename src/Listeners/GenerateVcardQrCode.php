<?php

namespace YourName\StatamicVcard\Listeners;

use Statamic\Events\EntrySaved;
use Statamic\Facades\AssetContainer;
use Statamic\Assets\Asset;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;

class GenerateVcardQrCode
{
    public function handle(EntrySaved $event): void
    {
        $entry = $event->entry;

        if ($entry->collection()->handle() !== config('vcard.collection', 'vcards')) {
            return;
        }

        $slug      = $entry->slug();
        $url       = url($entry->url());
        $container = config('vcard.asset_container', 'assets');
        $folder    = config('vcard.asset_folder', 'qrcodes');
        $size      = config('vcard.qr_size', 400);
        $margin    = config('vcard.qr_margin', 10);
        [$r, $g, $b] = config('vcard.qr_color', [26, 35, 126]);

        $qrCode = QrCode::create($url)
            ->setSize($size)
            ->setMargin($margin)
            ->setForegroundColor(new Color($r, $g, $b))
            ->setBackgroundColor(new Color(255, 255, 255));

        $result   = (new PngWriter())->write($qrCode);
        $filename = "{$folder}/{$slug}.png";

        Storage::disk($container)->put($filename, $result->getString());

        $assetContainer = AssetContainer::find($container);
        $asset = $assetContainer->asset($filename)
            ?? (new Asset)->container($assetContainer)->path($filename);
        $asset->save();

        $qrField = config('vcard.fields.qr_code', 'qr_code');
        $entry->set($qrField, $asset->id())->saveQuietly();
    }
}
