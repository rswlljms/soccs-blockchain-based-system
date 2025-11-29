<?php

class AppConfig {
    public static function get($key, $default = null) {
        $env = getenv($key);
        if ($env !== false && $env !== null && $env !== '') return $env;
        // fallback to local overrides
        $overrides = [
            'OCR_SPACE_API_KEY' => 'K87406060788957', // set API key via environment variable for production
            'SCHOOL_NAME_PRIMARY' => 'Laguna State Polytechnic University',
            'SCHOOL_NAME_ALIASES' => 'LSPU;Laguna State Polytechnic Univ;Laguna State Polytechnic University;Santa Cruz Campus;LSPU Santa Cruz;Republic of the Philippines;Province of Laguna',
        ];
        return array_key_exists($key, $overrides) ? $overrides[$key] : $default;
    }
}

?>


