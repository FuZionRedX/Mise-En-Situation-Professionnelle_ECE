<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Block;

// Create user
$identifier = 'u' . bin2hex(random_bytes(4));
$user = User::create([
    'name' => 'CIUser',
    'email' => $identifier . '@local',
    'identifier' => $identifier,
    'password' => bcrypt('secret123'),
]);
echo "Created user identifier: $identifier\n";

// Create block associated to user
$block = Block::create([
    'name' => 'AutoTestBlock',
    'identifier' => 'autotest_' . bin2hex(random_bytes(3)),
    'creator_identifier' => $identifier,
    'solid' => true,
    'destructible' => true,
    'resistance' => 3,
    'texture_path' => 'textures/test_placeholder.png',
    'geometry' => 'cube',
]);

echo "Block creator_identifier: " . Block::latest()->first()->creator_identifier . "\n";

return 0;
