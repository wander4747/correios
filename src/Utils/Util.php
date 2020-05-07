<?php

namespace Correios\Utils;

class Util
{
    static public function cleanHtml(string $html = null): string
    {
        if (is_string($html) && strlen($html) > 0) {
            $html = str_replace(["\t", "\r\n", "\n", "\r"], '', $html);
            while (strrpos($html, '  ') || strrpos($html, '   ')) {
                $html = str_replace(["   ", "  "], ' ', $html);
            }
            $html = str_replace(["> <", "> ", " <"], ['><', '>', '<'], $html);
            $html = str_replace('\\u00a0', "",$html);
        }
        
        return $html;
    }

    static public function cleanAccent(string $html = null): string
    {
        $html = Util::cleanHtml($html);
        $html = htmlentities($html, ENT_QUOTES | ENT_IGNORE, "UTF-8");
        return $html;
    }
}