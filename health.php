<?php
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'status' => 'ok',
    'service' => 'short_videos',
    'time' => date(DATE_ATOM),
], JSON_UNESCAPED_UNICODE);
