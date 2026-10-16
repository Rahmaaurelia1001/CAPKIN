<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = App\Models\User::select('id','name','email','role','unit_id')->get();
foreach ($users as $u) {
    echo $u->id.': '.$u->name.' ('.$u->role.') - '.$u->email.PHP_EOL;
}
