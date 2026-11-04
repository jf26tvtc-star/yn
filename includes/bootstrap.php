<?php
// يُستدعى في أول كل صفحة: يحمّل إعداد قاعدة البيانات والدوال المشتركة ويبدأ الجلسة.
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}
