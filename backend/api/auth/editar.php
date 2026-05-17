<?php
// api/auth/editar.php

require_once '../config/cors.php';
require_once '../config/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$usuario_id   = validarToken();
$db           = (new Database())->getConnection();
$content_type = $_SERVER['CONTENT_TYPE'] ?? '';

if (str_contains($content_type, 'multipart/form-data')) {
    $nombre_de_usuario = trim($_POST['nombre_de_usuario'] ?? '');
    $nueva_password    = trim($_POST['password']          ?? '');
} else {
    $body              = json_decode(file_get_contents('php://input'), true);
    $nombre_de_usuario = trim($body['nombre_de_usuario'] ?? '');
    $nueva_password    = trim($body['password']          ?? '');
}

// Obtener usuario actual
$stmt = $db->prepare('SELECT foto FROM usuarios WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $usuario_id]);
$actual = $stmt->fetch();

$foto_path = $actual['foto'];

// Procesar nueva foto si viene
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
    $filename   = uniqid('usr_', true) . '.' . strtolower($ext);
    $upload_dir = __DIR__ . '/../uploads/usuarios/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $filename)) {
        // Eliminar foto anterior
        if ($foto_path) {
            $old = __DIR__ . '/../../' . $foto_path;
            if (file_exists($old)) unlink($old);
        }
        $foto_path = 'api/uploads/usuarios/' . $filename;
    }
}

// Construir SET dinámico
$sets   = [];
$params = [':id' => $usuario_id];

if ($nombre_de_usuario !== '') {
    // Verificar que no esté en uso
    $chk = $db->prepare('SELECT id FROM usuarios WHERE nombre_de_usuario = :u AND id != :id LIMIT 1');
    $chk->execute([':u' => $nombre_de_usuario, ':id' => $usuario_id]);
    if ($chk->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya está en uso.']);
        exit;
    }
    $sets[]               = 'nombre_de_usuario = :usuario';
    $params[':usuario']   = $nombre_de_usuario;
}

if ($nueva_password !== '') {
    if (strlen($nueva_password) < 6) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres.']);
        exit;
    }
    $sets[]           = 'password = :pass';
    $params[':pass']  = password_hash($nueva_password, PASSWORD_BCRYPT);
}

$sets[]           = 'foto = :foto';
$params[':foto']  = $foto_path;

$sql = 'UPDATE usuarios SET ' . implode(', ', $sets) . ' WHERE id = :id';
$upd = $db->prepare($sql);
$upd->execute($params);

echo json_encode([
    'success' => true,
    'message' => 'Perfil actualizado correctamente.',
]);
