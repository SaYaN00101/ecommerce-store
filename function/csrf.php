<?php
/**
 * CSRF Token helpers
 * Include this file wherever CSRF protection is needed.
 * Requires an active session.
 */

function generate_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_input(): void {
    $token = generate_csrf_token();
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

function verify_csrf_token(): bool {
    if (
        empty($_SESSION['csrf_token']) ||
        empty($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        return false;
    }
    // Rotate token after successful verification
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return true;
}
