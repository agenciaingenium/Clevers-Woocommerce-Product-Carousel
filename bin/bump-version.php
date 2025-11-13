<?php
/**
 * Pequeño script para hacer bump de versión en:
 * - clevers-product-carousel.php (header Version)
 * - readme.txt (Stable tag)
 *
 * Uso:
 *   php bin/bump-version.php 1.0.3
 */

if ($argc < 2) {
    fwrite(STDERR, "Uso: php bin/bump-version.php X.Y.Z\n");
    exit(1);
}

$newVersion = $argv[1];

// Validación muy básica X.Y.Z
if (!preg_match('/^[0-9]+\.[0-9]+\.[0-9]+$/', $newVersion)) {
    fwrite(STDERR, "Versión inválida: {$newVersion}. Usa formato X.Y.Z\n");
    exit(1);
}

$root = dirname(__DIR__);

// Archivos a modificar
$targets = [
    $root . '/clevers-product-carousel.php' => [
        'label'   => 'Plugin main file',
        // Matchea líneas tipo: " * Version: 1.0.2"
        'pattern' => '/^(\s*\*\s*Version:\s*)([0-9]+\.[0-9]+\.[0-9]+)(\s*)$/mi',
        'replace' => '$1' . $newVersion . '$3',
    ],
    $root . '/readme.txt' => [
        'label'   => 'readme.txt (Stable tag)',
        // Matchea líneas tipo: "Stable tag: 1.0.2"
        'pattern' => '/^(Stable tag:\s*)([0-9]+\.[0-9]+\.[0-9]+)(\s*)$/mi',
        'replace' => '$1' . $newVersion . '$3',
    ],
];

foreach ($targets as $file => $config) {
    if (!file_exists($file)) {
        echo "Aviso: archivo no encontrado: {$file}\n";
        continue;
    }

    $contents = file_get_contents($file);
    if ($contents === false) {
        echo "Error: no pude leer {$file}\n";
        continue;
    }

    $newContents = preg_replace(
        $config['pattern'],
        $config['replace'],
        $contents,
        -1,
        $count
    );

    if ($newContents === null) {
        echo "Error en preg_replace para {$config['label']} ({$file})\n";
        continue;
    }

    if ($count === 0) {
        echo "No se detectó patrón de versión en {$config['label']} ({$file}), nada cambiado.\n";
        continue;
    }

    if (file_put_contents($file, $newContents) === false) {
        echo "Error escribiendo {$file}\n";
        continue;
    }

    echo "Actualizado {$config['label']} ({$file}) a versión {$newVersion} ({$count} reemplazo(s)).\n";
}

echo "Listo. Revisa los cambios con: git diff\n";