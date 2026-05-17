<?php
// api/config/auth.php
// Valida el token Bearer en cada petición protegida.
// Uso: require_once '../config/auth.php';  → devuelve $usuario_id

require_once __DIR__ . '/database.php';

/**
 * Lee el header Authorization, valida el token y
 * retorna el ID del usuario autenticado.
 * Si el token es inválido o expiró, responde con error y termina.
 */
function validarToken(): int {
    $headers = getallheaders();

    if (empty($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Token requerido.'
        ]);
        exit;
    }

    $token = str_replace('Bearer ', '', $headers['Authorization']);
    $token = trim($token);

    $db  = (new Database())->getConnection();
    $sql = 'SELECT id, token_expiracion FROM usuarios
            WHERE token = :token
            LIMIT 1';

    $stmt = $db->prepare($sql);
    $stmt->execute([':token' => $token]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Token inválido.'
        ]);
        exit;
    }

    // Verificar expiración
    if (new DateTime() > new DateTime($usuario['token_expiracion'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Token expirado. Inicia sesión nuevamente.'
        ]);
        exit;
    }

    return (int) $usuario['id'];
}
