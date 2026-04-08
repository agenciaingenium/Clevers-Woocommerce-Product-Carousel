#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * OpenClaw preflight checker.
 *
 * Detects broken Telegram extension references in ~/.openclaw/openclaw.json
 * that would cause status-all / runbook execution to fail with ENOENT.
 */

const EXIT_OK = 0;
const EXIT_WARNING = 1;
const EXIT_ERROR = 2;

function main(array $argv): int {
    $home = getenv('HOME');

    if (! is_string($home) || $home === '') {
        fwrite(STDERR, "ERROR: HOME is not set; cannot resolve ~/.openclaw/openclaw.json\n");
        return EXIT_ERROR;
    }

    $configPath = $home . '/.openclaw/openclaw.json';

    if (isset($argv[1]) && $argv[1] !== '') {
        $configPath = $argv[1];
    }

    if (! file_exists($configPath)) {
        fwrite(STDERR, "ERROR: OpenClaw config was not found at {$configPath}\n");
        return EXIT_ERROR;
    }

    $raw = file_get_contents($configPath);

    if ($raw === false) {
        fwrite(STDERR, "ERROR: Unable to read {$configPath}\n");
        return EXIT_ERROR;
    }

    $json = json_decode($raw, true);

    if (! is_array($json)) {
        fwrite(STDERR, "ERROR: Invalid JSON in {$configPath}\n");
        return EXIT_ERROR;
    }

    $baseDir = dirname($configPath);
    $missing = findMissingTelegramEntries($json, $baseDir);

    if ($missing === []) {
        fwrite(STDOUT, "OK: No broken Telegram extension path references were detected.\n");
        return EXIT_OK;
    }

    fwrite(STDERR, "WARNING: Broken Telegram extension path references detected:\n");

    foreach ($missing as $item) {
        fwrite(STDERR, "- {$item['key']} => {$item['value']}\n");
        fwrite(STDERR, "  resolved: {$item['resolved']}\n");
    }

    fwrite(STDERR, "\nSuggested remediations:\n");
    fwrite(STDERR, "1) Reinstall or rebuild the Telegram extension so channel.setup.js exists.\n");
    fwrite(STDERR, "2) Remove or fix stale Telegram path references in openclaw.json.\n");
    fwrite(STDERR, "3) Re-run this preflight before invoking runbooks.\n");

    return EXIT_WARNING;
}

/**
 * @param array<string, mixed> $json
 * @return array<int, array{key: string, value: string, resolved: string}>
 */
function findMissingTelegramEntries(array $json, string $baseDir): array {
    $flat = flattenJson($json);
    $missing = [];

    foreach ($flat as $key => $value) {
        if (! is_string($value)) {
            continue;
        }

        $isTelegramPath = strpos($value, 'telegram') !== false && strpos($value, 'channel.setup.js') !== false;

        if (! $isTelegramPath) {
            continue;
        }

        $resolved = resolvePath($value, $baseDir);

        if (! file_exists($resolved)) {
            $missing[] = [
                'key' => $key,
                'value' => $value,
                'resolved' => $resolved,
            ];
        }
    }

    return $missing;
}

/**
 * @param array<string, mixed> $input
 * @return array<string, mixed>
 */
function flattenJson(array $input, string $prefix = ''): array {
    $output = [];

    foreach ($input as $key => $value) {
        $composite = $prefix === '' ? (string) $key : $prefix . '.' . $key;

        if (is_array($value)) {
            $output = array_merge($output, flattenJson($value, $composite));
            continue;
        }

        $output[$composite] = $value;
    }

    return $output;
}

function resolvePath(string $rawPath, string $baseDir): string {
    if ($rawPath === '') {
        return $rawPath;
    }

    if ($rawPath[0] === '/') {
        return $rawPath;
    }

    return $baseDir . '/' . ltrim($rawPath, '/');
}

exit(main($argv));
