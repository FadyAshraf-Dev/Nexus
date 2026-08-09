<?php
declare(strict_types=1);
class Navigation{
    /**
     * Returns the current script's filename (e.g. "order_management.php"),
     * as reported by the web server for this request.
     *
     * This deliberately reads $_SERVER['PHP_SELF'] rather than
     * $_GET/$_POST/$_REQUEST - those only hold request *parameters*
     * (?foo=bar, form fields), not which script is actually executing.
     * PHP_SELF is populated by the server itself and reflects the request
     * path regardless of what parameters (if any) came with it.
     */
    public static function currentPage(): string
    {
        return basename($_SERVER['PHP_SELF']);
    }

    /**
     * Returns ' active' if $page matches the currently-executing script,
     * or '' otherwise. Meant to be echoed straight into a class attribute:
     *
     *   <a class="nav-link<?= Navigation::activeIf('order_management.php') ?>">
     */
    public static function activeIf(string $page): string
    {
        return $page === self::currentPage() ? ' active' : '';
    }
}