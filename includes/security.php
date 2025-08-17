<?php
class Security {
    
    // Validar y sanitizar entrada
    public static function sanitizarEntrada($data, $tipo = 'string') {
        switch ($tipo) {
            case 'email':
                return filter_var(trim($data), FILTER_SANITIZE_EMAIL);
            case 'int':
                return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
            case 'float':
                return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            case 'url':
                return filter_var(trim($data), FILTER_SANITIZE_URL);
            default:
                return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
        }
    }
    
    
    // Verificar timeout de sesión (1 hora)
    public static function verificarTimeoutSesion($timeout = 3600) {
        if (isset($_SESSION['tiempo_login'])) {
            if (time() - $_SESSION['tiempo_login'] > $timeout) {
                session_unset();
                session_destroy();
                return false;
            }
            // Actualizar tiempo de última actividad
            $_SESSION['ultima_actividad'] = time();
        }
        return true;
    }
}
?>
