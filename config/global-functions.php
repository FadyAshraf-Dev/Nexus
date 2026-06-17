<?php
// DRY Function: Absolute cleaner for inputs to stop SQL execution injections
function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

// DRY Function: Handles instant clean routing redirection
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// DRY Function: E-commerce price helper to format decimals beautifully
function formatCurrency($amount) {
    return "$" . number_format($amount, 2);
}