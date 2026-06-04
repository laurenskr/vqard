<?php

namespace YourName\StatamicVcard\Http\Controllers;

use Illuminate\Http\Response;
use Statamic\Facades\Entry;

class VcardDownloadController
{
    public function __invoke(string $slug): Response
    {
        $collection = config('vcard.collection', 'vcards');
        $fields     = config('vcard.fields', []);

        $entry = Entry::query()
            ->where('collection', $collection)
            ->where('slug', $slug)
            ->first();

        abort_if(!$entry, 404);

        $f = fn(string $key) => $entry->get($fields[$key] ?? $key);

        $lines = [
            'BEGIN:VCARD',
            'VERSION:3.0',
            'FN:'    . $f('name'),
            'TITLE:' . $f('role'),
        ];

        if ($mobile = $f('mobile')) {
            $lines[] = 'TEL;TYPE=CELL:' . $mobile;
        }
        if ($phone = $f('phone')) {
            $lines[] = 'TEL;TYPE=WORK:' . $phone;
        }
        if ($email = $f('email')) {
            $lines[] = 'EMAIL:' . $email;
        }
        if ($website = $f('website')) {
            $lines[] = 'URL:' . $website;
        }
        if ($org = $f('org')) {
            $lines[] = 'ORG:' . $org;
        }
        if ($address = $f('address')) {
            $lines[] = 'ADR;TYPE=WORK:;;' . str_replace(["\r\n", "\n"], ' ', $address);
        }

        $lines[] = 'END:VCARD';

        $vcf = implode("\r\n", $lines);

        return response($vcf, 200, [
            'Content-Type'        => 'text/vcard; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $slug . '.vcf"',
        ]);
    }
}
