<?php
$dir = new RecursiveDirectoryIterator('C:\laragon\www\vibe\resources\views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/.*\.blade\.php$/', RegexIterator::GET_MATCH);

foreach ($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    $original = $content;
    
    // Fix src="{{ $product->images[0] }}" to src="{{ asset($product->images[0]) }}"
    $content = preg_replace('/src="\{\{\s*(\$product->images\[\d+\])\s*\}\}"/', 'src="{{ asset($1) }}"', $content);
    
    // Fix src="{{ $img }}" to src="{{ asset($img) }}"
    $content = preg_replace('/src="\{\{\s*(\$img)\s*\}\}"/', 'src="{{ asset($1) }}"', $content);

    // Fix src="{{ $item['image'] }}" to src="{{ asset($item['image']) }}"
    $content = preg_replace('/src="\{\{\s*(\$item\[\'image\'\])\s*\}\}"/', 'src="{{ asset($1) }}"', $content);
    
    // Fix setMainImage('{{ $img }}') to setMainImage('{{ asset($img) }}')
    $content = preg_replace('/setMainImage\(\'\{\{\s*(\$img)\s*\}\}\'/', 'setMainImage(\'{{ asset($1) }}\'', $content);
    
    if ($content !== $original) {
        file_put_contents($path, $content);
        echo "Fixed asset() wrapping in " . basename($path) . "\n";
    }
}
echo "Done wrapping assets.\n";
