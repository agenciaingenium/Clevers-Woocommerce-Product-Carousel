<?php
// bin/bump-version.php

if ($argc < 2) {
    fwrite(STDERR, "Uso: php bin/bump-version.php 1.0.2\n");
    exit(1);
}

$version = $argv[1];

// Valida formato X.Y.Z
if (!preg_match('/^\d+\.\d+\.\d+$/', $version)) {
    fwrite(STDERR, "Formato de versión inválido: {$version}. Usa algo como 1.0.2\n");
    exit(1);
}

$files = [
    'clevers-product-carousel.php',
    'readme.txt',
];

foreach ($files as $file) {
    if (!file_exists($file)) {
        fwrite(STDERR, "Aviso: no encontré {$file}, lo salto.\n");
        continue;
    }

    $contents = file_get_contents($file);
    if ($contents === false) {
        fwrite(STDERR, "Error al leer {$file}\n");
        continue;
    }

    $original = $contents;

    if ($file === 'clevers-product-carousel.php') {
        // Línea del header: Version: X.Y.Z
        $contents = preg_replace(
            '/^(\s*\*\s*Version:\s*)(\d+(\.\d+){1,3})/mi',
            '$1' . $version,
            $contents,
            1
        );
    }

    if ($file === 'readme.txt') {
        // Línea: Stable tag: X.Y.Z
        $contents = preg_replace(
            '/^(Stable tag:\s*)(\d+(\.\d+){1,3})/mi',
            '$1' . $version,
            $contents,
            1
        );
    }

    if ($contents !== $original) {
        file_put_contents($file, $contents);
        echo "Actualizado {$file} a versión {$version}\n";
    } else {
        echo "No se detectó patrón de versión en {$file}, nada cambiado.\n";
    }
}

echo "Listo. Revisa los cambios con: git diff\n";