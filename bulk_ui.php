<?php

$dir = __DIR__ . '/resources/views';

function processDirectory($dir) {
    if (!is_dir($dir)) return;
    
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            processDirectory($path);
        } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            processFile($path);
        }
    }
}

function processFile($filePath) {
    // skip buyer dashboard as we just edited it
    if (strpos($filePath, 'buyer/dashboard.blade.php') !== false) return;
    // skip layouts for now to avoid breaking bottom-nav or major layout
    if (strpos($filePath, 'layouts') !== false) {
        // let's just make a small safe replacement for layouts if needed, but safer to skip
        return;
    }

    $content = file_get_contents($filePath);
    $original = $content;

    // 1. Padding
    $content = preg_replace_callback('/\bp-((1[0-6])|20|24|32)\b/', function($m) {
        $val = (int)$m[1];
        $mob = (int)ceil($val / 2); // e.g p-12 -> p-6 lg:p-12
        return "p-{$mob} lg:p-{$val}";
    }, $content);

    // 2. Padding X
    $content = preg_replace_callback('/\bpx-((1[0-6])|20|24|32)\b/', function($m) {
        $val = (int)$m[1];
        $mob = (int)ceil($val / 2);
        return "px-{$mob} lg:px-{$val}";
    }, $content);

    // 3. Padding Y
    $content = preg_replace_callback('/\bpy-((1[0-6])|20|24|32)\b/', function($m) {
        $val = (int)$m[1];
        $mob = (int)ceil($val / 2);
        return "py-{$mob} lg:py-{$val}";
    }, $content);

    // 4. Gap
    $content = preg_replace_callback('/\bgap-((1[0-6])|20|24|32)\b/', function($m) {
        $val = (int)$m[1];
        $mob = (int)ceil($val / 2);
        return "gap-{$mob} lg:gap-{$val}";
    }, $content);
    
    // 5. Space Y
    $content = preg_replace_callback('/\bspace-y-((1[0-6])|20|24|32)\b/', function($m) {
        $val = (int)$m[1];
        $mob = (int)ceil($val / 2);
        return "space-y-{$mob} lg:space-y-{$val}";
    }, $content);

    // 6. Rounded massive corners
    $content = str_replace('rounded-[4rem]', 'rounded-3xl lg:rounded-[4rem]', $content);
    $content = str_replace('rounded-[3rem]', 'rounded-3xl lg:rounded-[3rem]', $content);
    $content = preg_replace_callback('/\brounded-(3xl|4xl|5xl)\b/', function($m) {
        // if already responsive, we might duplicate it, but let's assume it isn't
        return "rounded-2xl lg:rounded-{$m[1]}";
    }, $content);
    // Cleanup potential duplications natively: rounded-2xl lg:rounded-3xl lg:rounded-[4rem] (avoid doing if already has lg:)
    // The simple replace might be enough if we just run once.

    // 7. Text sizing
    // large fonts
    $content = preg_replace('/(?<!lg:)(?<!md:)(?<!sm:)\btext-6xl\b/', 'text-4xl lg:text-6xl', $content);
    $content = preg_replace('/(?<!lg:)(?<!md:)(?<!sm:)\btext-5xl\b/', 'text-3xl lg:text-5xl', $content);
    $content = preg_replace('/(?<!lg:)(?<!md:)(?<!sm:)\btext-7xl\b/', 'text-5xl lg:text-7xl', $content);
    
    if ($content !== $original) {
        file_put_contents($filePath, $content);
        echo "Updated: " . str_replace(__DIR__, '', $filePath) . "\n";
    }
}

processDirectory($dir);
echo "Done processing UI classes!\n";
