<?php
// api/contactos/crear.php

require_once '../config/cors.php';
require_once '../config/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$usuario_id = validarToken();
$db         = (new Database())->getConnection();

// Campos del formulario multipart
$nombre    = trim($_POST['nombre']    ?? '');
$apellido  = trim($_POST['apellido']  ?? '');
$telefono  = trim($_POST['telefono']  ?? '');
$email     = trim($_POST['email']     ?? '');
$direccion = trim($_POST['direccion'] ?? '');
$notas     = trim($_POST['notas']     ?? '');

// Validaciones obligatorias
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

// Foto obligatoria
$foto_path = null;
if (!empty($_FILES['foto']['tmp_name'])) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    $finfo         = finfo_open(FILEINFO_MIME_TYPE);
    $mime          = finfo_file($finfo, $_FILES['foto']['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed_types)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Formato de imagen no permitido. Usa JPG, PNG o WEBP.']);
        exit;
    }

    $ext        = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $filename   = uniqid('cnt_', true) . '.' . strtolower($ext);
    $upload_dir = __DIR__ . '/../uploads/contactos/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $filename)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al guardar la imagen.']);
        exit;
    }
    $foto_path = 'api/uploads/contactos/' . $filename;
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'La foto del contacto es obligatoria.']);
    exit;
}

$ins = $db->prepare(
    'INSERT INTO contactos (usuario_id, nombre, apellido, telefono, email, direccion, notas, foto)
     VALUES (:uid, :nombre, :apellido, :telefono, :email, :direccion, :notas, :foto)'
);
$ins->execute([
    ':uid'      => $usuario_id,
    ':nombre'   => $nombre,
    ':apellido' => $apellido ?: null,
    ':telefono' => $telefono,
    ':email'    => $email    ?: null,
    ':direccion'=> $direccion?: null,
    ':notas'    => $notas    ?: null,
    ':foto'     => $foto_path,
]);

echo json_encode([
    'success' => true,
    'message' => 'Contacto creado correctamente.',
    'id'      => (int) $db->lastInsertId(),
]);
