<?php

/**
 * Entfernt totes Theme-CSS (hero-stage / emblem-plate / values-bar / bb-cta-strip / bb-icon)
 * aus Layout-Dateien. Nur CSS ohne Markup.
 */

$root = dirname(__DIR__);
$files = [
    $root.'/themes/bluebyte/views/layouts/app.blade.php',
    $root.'/resources/theme-skeletons/bluebyte/views/layouts/app.blade.php',
    $root.'/themes/minecraft/views/layouts/app.blade.php',
    $root.'/themes/palworld/views/layouts/app.blade.php',
    $root.'/themes/pax-dei/views/layouts/app.blade.php',
    $root.'/themes/satisfactory/views/layouts/app.blade.php',
    $root.'/themes/seven-days/views/layouts/app.blade.php',
];

foreach ($files as $file) {
    if (! is_file($file)) {
        echo "skip missing: $file\n";
        continue;
    }
    $css = file_get_contents($file);
    $orig = $css;

    // BlueByte-spezifische Blöcke
    $css = preg_replace('/\s*\.theme-bluebyte \.hero-stage\s*\{.*?\}\s*@keyframes bb-grid\s*\{.*?\}/s', "\n", $css);
    $css = preg_replace('/\s*\.theme-bluebyte \.emblem-plate\s*\{.*?\}\s*@keyframes bb-rise\s*\{.*?\}/s', "\n", $css);
    $css = preg_replace('/\s*\.theme-bluebyte \.values-bar\s*\{.*?\.theme-bluebyte \.values-bar \.v-item:hover\s*\{.*?\}/s', "\n", $css);
    $css = preg_replace('/\s*\.theme-bluebyte \.bb-icon\s*\{.*?\}/s', "\n", $css);
    $css = preg_replace('/\s*\.theme-bluebyte \.bb-cta-strip\s*\{.*?\}/s', "\n", $css);

    // Game-Themes: hero-stage + keyframes + emblem + values-bar
    $css = preg_replace('/\s*\.theme-[a-z0-9\-]+ \.hero-stage\s*\{.*?\}\s*@keyframes theme-[a-z0-9\-]+-glow\s*\{.*?\}/s', "\n", $css);
    $css = preg_replace('/\s*\.theme-[a-z0-9\-]+ \.emblem-plate\s*\{.*?\}\s*@keyframes theme-[a-z0-9\-]+-rise\s*\{.*?\}/s', "\n", $css);
    $css = preg_replace('/\s*\.theme-[a-z0-9\-]+ \.values-bar\s*\{.*?\.theme-[a-z0-9\-]+ \.values-bar \.v-item:last-child\s*\{.*?\}/s', "\n", $css);

    if ($css === $orig) {
        echo "unchanged: $file\n";
    } else {
        file_put_contents($file, $css);
        echo "cleaned: $file\n";
    }
}
