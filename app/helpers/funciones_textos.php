<?php
function normalizeDescription(string $value): string
{
    // 1. Elimina espacios al inicio y final
    $value = trim($value);

    // 2. Reemplaza múltiples espacios (incluye tabs, saltos de línea) por un solo espacio
    $value = preg_replace('/\s+/', ' ', $value);

    return $value;
}
