<?php
// api/auth/perfil.php

require_once '../config/cors.php';
require_once '../config/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$usuario_id = validarToken();
$db         = (new Database())->getConnection();

$stmt = $db->prepare(
    'SELECT id, nombre_de_usuario, foto, fecha_registro
     FROM usuarios WHERE id = :id LIMIT 1'
);
$stmt->execute([':id' => $usuario_id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
    exit;
}

$base_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$foto_url = $usuario['foto']
    ? $base_url . '/' . ltrim($usuario['foto'], '/')
    : null;

echo json_encode([
    'success' => true,
    'usuario' => [
        'id'               => (int) $usuario['id'],
        'nombre_de_usuario'=> htmlspecialchars($usuario['nombre_de_usuario'], ENT_QUOTES, 'UTF-8'),
        'foto'             => $foto_url,
        'fecha_registro'   => $usuario['fecha_registro'],
    ],
]);
