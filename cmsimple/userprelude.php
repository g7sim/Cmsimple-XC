<?php
// Path to the CMS Cache-folder
$cacheDir = __DIR__ . '/_cache/login_locks';

if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0700, true);
}

$htaccessFile = $cacheDir . '/.htaccess';
if (!file_exists($htaccessFile)) {
    file_put_contents($htaccessFile, "Deny from all\n");
}

$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// Only protect the actual login endpoint
if ($method !== 'POST' || strtolower($uri) !== '/login') {
    return;
}

$key = md5($ip . '|login');
$lockFile = $cacheDir . '/' . $key . '.json';

$limit  = 5;
$window = 5;

$fp = fopen($lockFile, 'c+');
if (!$fp) {
    die('Cannot open lock file.');
}

flock($fp, LOCK_EX);

rewind($fp);
$raw = stream_get_contents($fp);
$data = json_decode($raw, true);

if (!is_array($data) || !isset($data['attempts']) || !is_array($data['attempts'])) {
    $data = ['attempts' => []];
}

$now = time();

$data['attempts'] = array_values(array_filter($data['attempts'], function ($ts) use ($now, $window) {
    return ($now - $ts) < $window;
}));

if (count($data['attempts']) >= $limit) {
    flock($fp, LOCK_UN);
    fclose($fp);

    header('HTTP/1.1 429 Too Many Requests');
    header('Retry-After: 5');
    die('Too many login attempts. Please wait a moment.');
}

// Replace this with your real login check
$loginWasWrong = true;

if ($loginWasWrong) {
    $data['attempts'][] = $now;
} else {
    $data['attempts'] = [];
}

ftruncate($fp, 0);
rewind($fp);
fwrite($fp, json_encode($data));
fflush($fp);

flock($fp, LOCK_UN);
fclose($fp);
