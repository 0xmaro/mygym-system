<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? null;
$current = basename($_SERVER['PHP_SELF']);


$allowed_pages = [
    'admin' => [
        'admin.php',
        'stacadmin.php',
        'index.php',
        'coach.php',
        'wcoach.php',
        'renew.php',
        'massagemen.php',
        'add_member.php',
        'add_session.php',
        'reneww.php',
        'massagew.php',
        'add_wmember.php',
        'wadd_session.php'
    ],
    'coach' => ['coach.php', 'renew.php', 'massagemen.php', 'add_member.php', 'add_session.php'],
    'wcoach' => ['wcoach.php', 'reneww.php', 'massagew.php', 'add_wmember.php', 'wadd_session.php']
];

if (!$role) {
    header("Location: index.php");
    exit();
}


if (isset($allowed_pages[$role])) {
    if (!in_array($current, $allowed_pages[$role])) {

        $redirect = $role . '.php'; // : admin.php أو coach.php أو wcoach.php
        header("Location: $redirect");
        exit();
    }
} else {

    header("Location: index.php");
    exit();
}
?>