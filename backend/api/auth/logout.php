<?php
// api/auth/logout.php

require_once '../config/cors.php';
require_once '../config/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$usuario_id = validarToken();
$db         = (new Database())->getConnection();

$stmt = $db->prepare('UPDATE usuarios SET token = NULL, token_expiracion = NULL WHERE id = :id');
$stmt->execute([':id' => $usuario_id]);

echo json_encode([
    'success' => true,
    'message' => 'Sesión cerrada correctamente.',
]);
