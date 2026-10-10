<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach(\App\Models\Booking::all() as $b) {
    if(!$b->uuid) {
        $b->uuid = (string) \Illuminate\Support\Str::uuid();
        $b->save();
    }
}
echo "Done";
