<?php

use Illuminate\Support\Facades\Route;
use Qabana\StatamicVcard\Http\Controllers\VcardDownloadController;

Route::get(config('vcard.route_prefix', 'vcards') . '/{slug}.vcf', VcardDownloadController::class)
    ->name('vcard.download');
