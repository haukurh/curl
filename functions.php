<?php

function array_merge_preserve_keys(array ...$arrays): array
{
    $data = [];
    foreach ($arrays as $array) {
        foreach ($array as $key => $value) {
            $data[$key] = $value;
        }
    }
    return $data;
}
