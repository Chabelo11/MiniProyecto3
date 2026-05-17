<?php
// api/contactos/actualizar.php

require_once '../config/cors.php';
require_once '../config/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$usuario_id  = validarToken();
$db          = (new Database())->getConnection();
$contacto_id = (int) ($_POST['id'] ?? 0);

if ($contacto_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de contacto inválido.']);
    exit;
}

// Verificar que pertenece al usuario autenticado
$chk = $db->prepare('SELECT id, foto FROM contactos WHERE id = :id AND usuario_id = :uid LIMIT 1');
$chk->execute([':id' => $contacto_id, ':uid' => $usuario_id]);
$actual = $chk->fetch();

if (!$actual) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Contacto no encontrado o sin permiso.']);
    exit;
}

$nombre    = trim($_POST['nombre']    ?? '');
$apellido  = trim($_POST['apellido']  ?? '');
$telefono  = trim($_POST['telefono']  ?? '');
$email     = trim($_POST['email']     ?? '');
$direccion = trim($_POST['direccion'] ?? '');
$notas     = trim($_POST['notas']     ?? '');

if ($nombre === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El nombre es obligatorio.']);
    exit;
}
if ($telefono === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El teléfono es obligatorio.']);
    exit;
}

$foto_path = $actual['foto'];

// Nueva foto (opcional en actualización)
if (!empty($_FILES['foto']['tmp_name'])) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    $finfo         = finfo_open(FILEINFO_MIME_TYPE);
    $mime          = finfo_file($finfo, $_FILES['foto']['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed_types)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Formato de imagen no permitido.']);
        exit;
    }

    $ext        = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $filename   = uniqid('cnt_', true) . '.' . strtolower($ext);
    $upload_dir = __DIR__ . '/../uploads/contactos/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $filename)) {
        // Eliminar foto anterior
        if ($foto_path) {
            $old = __DIR__ . '/../../' . $foto_path;
            if (file_exists($old)) unlink($old);
        }
        $foto_path = 'api/uploads/contactos/' . $filename;
    }
}

$upd = $db->prepare(
    'UPDATE contactos
     SET nombre = :nombre, apellido = :apellido, telefono = :telefono,
         email = :email, direccion = :direccion, notas = :notas, foto = :foto
     WHERE id = :id AND usuario_id = :uid'
);
$upd->execute([
    ':nombre'   => $nombre,
    ':apellido' => $apellido ?: null,
    ':telefono' => $telefono,
    ':email'    => $email    ?: null,
    ':direccion'=> $direccion?: null,
    ':notas'    => $notas    ?: null,
    ':foto'     => $foto_path,
    ':id'       => $contacto_id,
    ':uid'      => $usuario_id,
]);

echo json_encode([
    'success' => true,
    'message' => 'Contacto actualizado correctamente.',
]);
