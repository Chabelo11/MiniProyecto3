<?php
// api/contactos/eliminar.php

require_once '../config/cors.php';
require_once '../config/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$usuario_id = validarToken();
$db         = (new Database())->getConnection();

$body        = json_decode(file_get_contents('php://input'), true);
$contacto_id = (int) ($body['id'] ?? $_POST['id'] ?? 0);

if ($contacto_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de contacto inválido.']);
    exit;
}

// Verificar propiedad
$chk = $db->prepare('SELECT id, foto FROM contactos WHERE id = :id AND usuario_id = :uid LIMIT 1');
$chk->execute([':id' => $contacto_id, ':uid' => $usuario_id]);
$contacto = $chk->fetch();

if (!$contacto) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Contacto no encontrado o sin permiso.']);
    exit;
}

// Eliminar foto del disco
if ($contacto['foto']) {
    $file_path = __DIR__ . '/../../' . $contacto['foto'];
    if (file_exists($file_path)) unlink($file_path);
}

$del = $db->prepare('DELETE FROM contactos WHERE id = :id AND usuario_id = :uid');
$del->execute([':id' => $contacto_id, ':uid' => $usuario_id]);

echo json_encode([
    'success' => true,
    'message' => 'Contacto eliminado correctamente.',
]);
