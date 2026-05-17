<?php
// api/auth/registrar.php

require_once '../config/cors.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$db = (new Database())->getConnection();

// Puede venir como JSON o como multipart/form-data (con foto)
$content_type = $_SERVER['CONTENT_TYPE'] ?? '';

if (str_contains($content_type, 'multipart/form-data')) {
    $nombre_de_usuario = trim($_POST['nombre_de_usuario'] ?? '');
    $password          = trim($_POST['password']          ?? '');
} else {
    $body              = json_decode(file_get_contents('php://input'), true);
    $nombre_de_usuario = trim($body['nombre_de_usuario'] ?? '');
    $password          = trim($body['password']          ?? '');
}

// Validaciones
if ($nombre_de_usuario === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Usuario y contraseña son requeridos.']);
    exit;
}
if (strlen($nombre_de_usuario) < 3 || strlen($nombre_de_usuario) > 50) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El nombre de usuario debe tener entre 3 y 50 caracteres.']);
    exit;
}
if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres.']);
    exit;
}

// Verificar duplicado
$check = $db->prepare('SELECT id FROM usuarios WHERE nombre_de_usuario = :usuario LIMIT 1');
$check->execute([':usuario' => $nombre_de_usuario]);
if ($check->fetch()) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya está en uso.']);
    exit;
}

// Manejar foto de perfil (opcional en registro)
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
    $filename   = uniqid('usr_', true) . '.' . strtolower($ext);
    $upload_dir = __DIR__ . '/../uploads/usuarios/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $filename)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al guardar la imagen.']);
        exit;
    }
    $foto_path = 'api/uploads/usuarios/' . $filename;
}

// Insertar usuario
$hash = password_hash($password, PASSWORD_BCRYPT);
$ins  = $db->prepare(
    'INSERT INTO usuarios (nombre_de_usuario, password, foto)
     VALUES (:usuario, :pass, :foto)'
);
$ins->execute([
    ':usuario' => $nombre_de_usuario,
    ':pass'    => $hash,
    ':foto'    => $foto_path,
]);

echo json_encode([
    'success' => true,
    'message' => 'Usuario registrado correctamente.',
    'id'      => (int) $db->lastInsertId(),
]);
