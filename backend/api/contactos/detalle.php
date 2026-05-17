<?php
// api/contactos/detalle.php?id=X

require_once '../config/cors.php';
require_once '../config/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$usuario_id  = validarToken();
$contacto_id = (int) ($_GET['id'] ?? 0);

if ($contacto_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de contacto inválido.']);
    exit;
}

$db   = (new Database())->getConnection();
$stmt = $db->prepare(
    'SELECT id, nombre, apellido, telefono, email, direccion, notas, foto, fecha_creacion
     FROM contactos
     WHERE id = :id AND usuario_id = :uid
     LIMIT 1'
);
$stmt->execute([':id' => $contacto_id, ':uid' => $usuario_id]);
$c = $stmt->fetch();

if (!$c) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Contacto no encontrado.']);
    exit;
}

$base_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];

echo json_encode([
    'success'  => true,
    'contacto' => [
        'id'            => (int) $c['id'],
        'nombre'        => htmlspecialchars($c['nombre'],    ENT_QUOTES, 'UTF-8'),
        'apellido'      => htmlspecialchars($c['apellido']   ?? '', ENT_QUOTES, 'UTF-8'),
        'telefono'      => htmlspecialchars($c['telefono'],  ENT_QUOTES, 'UTF-8'),
        'email'         => htmlspecialchars($c['email']      ?? '', ENT_QUOTES, 'UTF-8'),
        'direccion'     => htmlspecialchars($c['direccion']  ?? '', ENT_QUOTES, 'UTF-8'),
        'notas'         => htmlspecialchars($c['notas']      ?? '', ENT_QUOTES, 'UTF-8'),
        'foto'          => $c['foto'] ? $base_url . '/' . ltrim($c['foto'], '/') : null,
        'fecha_creacion'=> $c['fecha_creacion'],
    ],
]);
