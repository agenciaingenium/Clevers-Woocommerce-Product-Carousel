<?php
/**
 * Bump version en:
 * - clevers-product-carousel.php (header Version)
 * - readme.txt (Stable tag)
 *
 * Uso:
 *   php bin/bump-version.php 1.1.0
 */

if ( 'cli' === PHP_SAPI && ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $argc < 2 ) {
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite,WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI utility output.
	fwrite( STDERR, "Uso: php bin/bump-version.php X.Y.Z\n" );
	exit( 1 );
}

$clevers_product_carousel_new_version = $argv[1];

if ( ! preg_match( '/^[0-9]+\.[0-9]+\.[0-9]+$/', $clevers_product_carousel_new_version ) ) {
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite,WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI utility output.
	fwrite( STDERR, "Versión inválida: {$clevers_product_carousel_new_version}. Usa formato X.Y.Z\n" );
	exit( 1 );
}

$clevers_product_carousel_root = dirname( __DIR__ );

/**
 * Actualiza la línea " * Version: X.Y.Z" en el header del plugin.
 */
function clevers_product_carousel_bump_plugin_header_version( string $file, string $new_version ): void {
	if ( ! file_exists( $file ) ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI utility output.
		echo "Archivo no encontrado: {$file}\n";
		return;
	}

	$lines   = file( $file );
	$changed = 0;

	foreach ( $lines as &$line ) {
		// Cualquier línea que empiece con "* Version" (soporta "Version:" o "Version :").
		if ( preg_match( '/^\s*\*\s*Version\b/i', $line ) ) {
			if ( preg_match( '/^(\s*\*\s*)Version\b/i', $line, $matches ) ) {
				$prefix = $matches[1];
			} else {
				$prefix = ' * ';
			}

			$line = $prefix . 'Version: ' . $new_version . "\n";
			++$changed;
		}
	}
	unset( $line );

	if ( $changed > 0 ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- CLI utility.
		file_put_contents( $file, implode( '', $lines ) );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI utility output.
		echo "Actualizado header del plugin ({$file}) a versión {$new_version} ({$changed} linea(s)).\n";
	} else {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI utility output.
		echo "No se encontro linea 'Version' en {$file}\n";
	}
}

/**
 * Actualiza la línea "Stable tag: X.Y.Z" en readme.txt.
 */
function clevers_product_carousel_bump_readme_stable_tag( string $file, string $new_version ): void {
	if ( ! file_exists( $file ) ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI utility output.
		echo "Archivo no encontrado: {$file}\n";
		return;
	}

	$lines   = file( $file );
	$changed = 0;

	foreach ( $lines as &$line ) {
		if ( preg_match( '/^Stable tag:/i', $line ) ) {
			$line = "Stable tag: {$new_version}\n";
			++$changed;
		}
	}
	unset( $line );

	if ( $changed > 0 ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- CLI utility.
		file_put_contents( $file, implode( '', $lines ) );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI utility output.
		echo "Actualizado readme.txt (Stable tag) a versión {$new_version} ({$changed} linea(s)).\n";
	} else {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI utility output.
		echo "No se encontro linea 'Stable tag:' en {$file}\n";
	}
}

// Ejecutar updates
clevers_product_carousel_bump_plugin_header_version( $clevers_product_carousel_root . '/clevers-product-carousel.php', $clevers_product_carousel_new_version );
clevers_product_carousel_bump_readme_stable_tag( $clevers_product_carousel_root . '/readme.txt', $clevers_product_carousel_new_version );

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI utility output.
echo "\nListo. Revisa los cambios con: git diff\n";
