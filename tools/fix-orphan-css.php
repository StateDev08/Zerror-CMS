<?php

$files = glob(__DIR__.'/../themes/*/views/layouts/app.blade.php') ?: [];
$files[] = __DIR__.'/../resources/theme-skeletons/bluebyte/views/layouts/app.blade.php';

foreach ($files as $file) {
    if (! is_file($file)) {
        continue;
    }
    $c = file_get_contents($file);
    $n = preg_replace('/\R+\s*to \{ filter: saturate\(1\.15\) brightness\(1\.05\); \}\s*\}\s*to \{ opacity: 1; transform: none; \}\s*\}/s', "\n", $c);
    $n = preg_replace('/\R+\s*to \{ transform: translateY\(48px\); \}\s*\}\s*to \{ opacity: 1; transform: none; \}\s*\}/s', "\n", $n);
    if ($n !== $c) {
        file_put_contents($file, $n);
        echo "fixed {$file}\n";
    } else {
        echo "ok {$file}\n";
    }
}
