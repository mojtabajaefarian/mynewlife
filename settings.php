<?php
/* ===== FILE: settings.php | لودر تنظیمات از JSON ===== */
declare(strict_types=1);

function get_app_settings(): array {
    $file = __DIR__ . '/settings.json';
    if (!is_file($file)) {
        return ['anchors' => [], 'blocks' => [], 'exercises' => [], 'workouts' => [],
                'phases' => [], 'rules' => [], 'protocols' => [], 'tests' => [],
                'quotes' => [], 'config' => ['start_date' => date('Y-m-d')]];
    }
    $raw = file_get_contents($file);
    $data = json_decode($raw, true);
    if (!is_array($data)) return [];
    return $data;
}

function save_app_settings(array $data): bool {
    $file = __DIR__ . '/settings.json';
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    if ($json === false) return false;
    return @file_put_contents($file, $json, LOCK_EX) !== false;
}