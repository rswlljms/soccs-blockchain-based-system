<?php

class AppConfig {
    private static $config = null;
    
    private static function loadEnv() {
        if (self::$config !== null) return;
        
        self::$config = [];
        $envFile = __DIR__ . '/../.env';
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                if (strpos($line, '=') === false) continue;
                
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value, " \t\n\r\0\x0B\"'");
                self::$config[$key] = $value;
            }
        }
    }
    
    public static function get($key, $default = null) {
        $env = getenv($key);
        if ($env !== false && $env !== null && $env !== '') return $env;
        
        self::loadEnv();
        if (isset(self::$config[$key])) return self::$config[$key];
        
        $defaults = [
            'SCHOOL_NAME_PRIMARY' => 'Laguna State Polytechnic University',
            'SCHOOL_NAME_ALIASES' => 'LSPU;Laguna State Polytechnic Univ;Laguna State Polytechnic University;Santa Cruz Campus;LSPU Santa Cruz;Republic of the Philippines;Province of Laguna',
            'BLOCKCHAIN_URL' => 'http://localhost:3001',
        ];
        
        return array_key_exists($key, $defaults) ? $defaults[$key] : $default;
    }
    
    public static function getAll() {
        return [
            'smtp_username' => self::get('smtp_username'),
            'smtp_password' => self::get('smtp_password'),
        ];
    }
}

?>


