<?php
$secret = getenv('DOWNLOAD_SECRET') ?: '';
$encoded = isset($_GET['u']) ? (string)$_GET['u'] : '';
$exp = isset($_GET['exp']) ? (int)$_GET['exp'] : 0;
$sig = isset($_GET['sig']) ? (string)$_GET['sig'] : '';

if ($secret === '' || $encoded === '' || $exp <= 0 || $sig === '') {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['code' => 400, 'msg' => 'bad request']);
    exit;
}

$url = base64_decode($encoded, true);
if ($url === false || !preg_match('#^https?://#i', $url)) {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['code' => 400, 'msg' => 'invalid url']);
    exit;
}

if (time() > $exp) {
    http_response_code(410);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['code' => 410, 'msg' => 'link expired']);
    exit;
}

$expected = hash_hmac('sha256', $url . "\n" . $exp, $secret);
if (!hash_equals($expected, $sig)) {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['code' => 403, 'msg' => 'invalid signature']);
    exit;
}

$host = strtolower((string)parse_url($url, PHP_URL_HOST));
$allowed = [
    'douyin.com', 'douyinvod.com', 'douyinpic.com', 'bytecdn.cn', 'ixigua.com',
    'xiaohongshu.com', 'xhscdn.com', 'kuaishou.com', 'kuaishoup.com', 'kwimgs.com',
];
$ok = false;
foreach ($allowed as $domain) {
    if ($host === $domain || substr($host, -strlen('.' . $domain)) === '.' . $domain) {
        $ok = true;
        break;
    }
}
if (!$ok) {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['code' => 403, 'msg' => 'host not allowed']);
    exit;
}

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Location: ' . $url, true, 302);
exit;
