<?php

class AppConfig {
    public static function get($key, $default = null) {
        $env = getenv($key);
        if ($env !== false && $env !== null && $env !== '') return $env;
        // fallback to local overrides
        $overrides = [
            'OCR_SPACE_API_KEY' => 'K87406060788957',
            'SCHOOL_NAME_PRIMARY' => 'Laguna State Polytechnic University',
            'SCHOOL_NAME_ALIASES' => 'LSPU;Laguna State Polytechnic Univ;Laguna State Polytechnic University;Santa Cruz Campus;LSPU Santa Cruz;Republic of the Philippines;Province of Laguna',
            
            // SMTP Configuration - SET YOUR GMAIL APP PASSWORD HERE
            'smtp_username' => 'roswelljamesvitaliz@gmail.com',
            'smtp_password' => 'cfjciegpekyxocoy',
        ];
        return array_key_exists($key, $overrides) ? $overrides[$key] : $default;
    }
    
    public static function getAll() {
        return [
            'smtp_username' => self::get('smtp_username'),
            'smtp_password' => self::get('smtp_password'),
        ];
    }
}

?>


