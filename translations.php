<?php

$source = __DIR__ . '/lang/de.json';
$target = __DIR__ . '/lang/en.json';

$items = json_decode(file_get_contents($source), true);
ksort($items, SORT_NATURAL);

file_put_contents($source, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
file_put_contents(
    $target,
    json_encode(
        array_combine(array_keys($items), array_keys($items)),
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    )
);

// Todo Translation Progress
