<?php
// api/contactos/index.php

require_once '../config/cors.php';
require_once '../config/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$usuario_id = validarToken();
$db         = (new Database())->getConnection();

$base_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];

$stmt = $db->prepare(
    'SELECT id, nombre, apellido, telefono, email, direccion, notas, foto, fecha_creacion
     FROM contactos
     WHERE usuario_id = :uid
     ORDER BY nombre ASC'
);
$stmt->execute([':uid' => $usuario_id]);
$rows = $stmt->fetchAll();

$contactos = array_map(function ($c) use ($base_url) {
    return [
        'id'            => (int) $c['id'],
        'nombre'        => htmlspecialchars($c['nombre'],    ENT_QUOTES, 'UTF-8'),
        'apellido'      => htmlspecialchars($c['apellido']   ?? '', ENT_QUOTES, 'UTF-8'),
        'telefono'      => htmlspecialchars($c['telefono'],  ENT_QUOTES, 'UTF-8'),
        'email'         => htmlspecialchars($c['email']      ?? '', ENT_QUOTES, 'UTF-8'),
        'direccion'     => htmlspecialchars($c['direccion']  ?? '', ENT_QUOTES, 'UTF-8'),
        'notas'         => htmlspecialchars($c['notas']      ?? '', ENT_QUOTES, 'UTF-8'),
        'foto'          => $c['foto'] ? $base_url . '/' . ltrim($c['foto'], '/') : null,
        'fecha_creacion'=> $c['fecha_creacion'],
    ];
}, $rows);

echo json_encode([
    'success'   => true,
    'contactos' => $contactos,
]);
