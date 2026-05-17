<?php
// api/auth/login.php

require_once '../config/cors.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

// Leer body JSON
$body = json_decode(file_get_contents('php://input'), true);

$nombre_de_usuario = trim($body['nombre_de_usuario'] ?? '');
$password          = trim($body['password']          ?? '');

// Validaciones básicas
if ($nombre_de_usuario === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Usuario y contraseña son requeridos.']);
    exit;
}

$db = (new Database())->getConnection();

// Buscar usuario
$sql  = 'SELECT id, nombre_de_usuario, password, foto
         FROM usuarios
         WHERE nombre_de_usuario = :usuario
         LIMIT 1';
$stmt = $db->prepare($sql);
$stmt->execute([':usuario' => $nombre_de_usuario]);
$usuario = $stmt->fetch();

if (!$usuario || !password_verify($password, $usuario['password'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas.']);
    exit;
}

// Generar token y expiración (8 horas)
$token            = bin2hex(random_bytes(32));
$token_expiracion = (new DateTime())->modify('+8 hours')->format('Y-m-d H:i:s');

$upd = $db->prepare('UPDATE usuarios SET token = :token, token_expiracion = :exp WHERE id = :id');
$upd->execute([
    ':token' => $token,
    ':exp'   => $token_expiracion,
    ':id'    => $usuario['id'],
]);

// URL completa de la foto
$base_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$foto_url = $usuario['foto']
    ? $base_url . '/' . ltrim($usuario['foto'], '/')
    : null;

echo json_encode([
    'success' => true,
    'token'   => $token,
    'usuario' => [
        'id'               => (int) $usuario['id'],
        'nombre_de_usuario'=> htmlspecialchars($usuario['nombre_de_usuario'], ENT_QUOTES, 'UTF-8'),
        'foto'             => $foto_url,
    ],
]);
