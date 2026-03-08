<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Modules\Library\Models\LibraryResource;

foreach (LibraryResource::all() as $r) {
    echo "ID: {$r->id} | TYPE: {$r->content_type} | URL: {$r->url}\n";
}
