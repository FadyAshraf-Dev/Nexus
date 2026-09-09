<?php

declare(strict_types=1);

namespace FadyAshrafDev\NexusSupport;

/**
 * Instantiable, dependency-free version of Nexus's response helper.
 *
 * Takes the admin/base URL prefixes and an optional 403 template
 * path via the constructor instead of calling Config::app(...) or
 * assuming a fixed __DIR__-relative folder layout internally. The
 * static app::class Response facade (app/Core/Response.php) wraps
 * one of these behind the original static API.
 */
class Response
{
    public function __construct(
        private readonly string $adminUrl,
        private readonly string $baseUrl,
        private readonly ?string $forbiddenTemplatePath = null
    ) {
    }

    private function redirect(string $url): never
    {
        header("Location: " . $url);
        exit();
    }

    public function redirectAdmin(string $url): never
    {
        $this->redirect($this->adminUrl . $url);
    }

    public function redirectBase(string $url): never
    {
        $this->redirect($this->baseUrl . $url);
    }

    /**
     * Shuts down execution and renders the 403 Forbidden UI.
     */
    public function forbidden(): never
    {
        http_response_code(403);

        if ($this->forbiddenTemplatePath !== null && file_exists($this->forbiddenTemplatePath)) {
            require_once $this->forbiddenTemplatePath;
        } else {
            echo "<h1>403 Forbidden</h1>";
            echo "<p>Your client does not have permission to get this page from the server.</p>";
        }

        exit();
    }

    public function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
        );

        exit;
    }
}
