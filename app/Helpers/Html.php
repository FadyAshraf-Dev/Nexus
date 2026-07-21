<?php
declare(strict_types=1);
class Html{
    /**
     * Context-aware HTML Escaping for frontend rendering output contexts (Defeats XSS)
     */
    public static function escape(string $data) {
        if ($data === null) return '';
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    public static function slug(string $text):string
    {
        $text = mb_strtolower($text);
        $text = preg_replace('/\s+/', '-', $text);
        $text = preg_replace('/[^\p{L}\p{N}-]+/u', '', $text);
        $text = preg_replace('/-+/', '-', $text);
        return trim($text, '-');
    }
}