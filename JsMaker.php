<?php
class JsMaker {
   
    protected static function parseSelector($selector) {
        return match (true) {
            str_starts_with($selector, '!p5') => ['parent.parent.parent.parent.parent', substr($selector, 3)],
            str_starts_with($selector, '!p4') => ['parent.parent.parent.parent', substr($selector, 3)],
            str_starts_with($selector, '!p3') => ['parent.parent.parent', substr($selector, 3)],
            str_starts_with($selector, '!p2') => ['parent.parent', substr($selector, 3)],
            str_starts_with($selector, '!p')  => ['parent', substr($selector, 2)],
            str_starts_with($selector, '!t')  => ['top', substr($selector, 2)],
            (bool) preg_match('/^!<(.+)>/', $selector, $m) => [$m[1], substr($selector, strlen($m[0]))],
            default => ['', $selector]
        };
    }
   
    protected static function encodeJsValue($val) {
        return match (true) {
            is_int($val) || is_float($val) => $val,
            is_string($val) => json_encode($val, JSON_UNESCAPED_UNICODE),
            is_bool($val) => $val ? 'true' : 'false',
            is_array($val) || is_object($val) => json_encode($val, JSON_UNESCAPED_UNICODE),
            default => throw new \Exception('不支持的类型'),
        };
    }
    
    //rb: RAW BASE
    public static function rbChange($base, $attr, $val) {
        return ($base ? $base . '.' : '') . $attr . '=' . self::encodeJsValue($val) . ';';
    }
    
    public static function rbCall($base, $func, ...$args) {
        return ($base ? $base . '.' : '') . $func . '(' . implode(',', array_map(fn($arg) => self::encodeJsValue($arg), $args))  . ');';
    }
    
    public static function change($selector, $attr, $val) {
        [$base,] = self::parseSelector($selector);
        return self::rbChange($base, $attr, $val);
    }
    
    public static function call($selector, $func, ...$args) {
        [$base,] = self::parseSelector($selector);
        return self::rbCall($base, $func, ...$args);
    }
    
    protected static function makeQuerySelector($cssSelector) {
        return 'document.querySelector(' . self::encodeJsValue($cssSelector) . ')';
    }
    
    public static function eChange($selector, $attr, $val) {
        [$base, $cssSelector] = self::parseSelector($selector);
        return self::rbChange($base, self::makeQuerySelector($cssSelector) . '.' . $attr, $val);
    }
    
    public static function eCall($selector, $func, ...$args) {
        [$base, $cssSelector] = self::parseSelector($selector);
        return self::rbCall($base, self::makeQuerySelector($cssSelector) . '.' . $func, ...$args);
    }
   
    public static function setTimeout($js, $s) {
        return 'setTimeout(() => {' . $js . '},' . $s * 1000 . ');';
    }
   
    public static function reload($selector = '!t') {
        return self::call($selector, 'location.reload');
    }
   
    public static function redirect($url, $selector = '!t') {
        return self::change($selector, 'location.href', $url);
    }
}
