<?php
// api/config/database.php

class Database {
    // ── Ajusta estos valores en tu servidor de hosting ──
    private $host = "mysql-aleajndro.alwaysdata.net";
    private $db_name = "aleajndro_agenda_db";
    private $username = "aleajndro";
    private $password = "cabezas11";
    public $conn;

    private ?PDO $connection = null;

    /**
     * Devuelve la conexión PDO (singleton).
     */
    public function getConnection(): PDO {
        if ($this->connection !== null) {
            return $this->connection;
        }

        $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            // En producción NO exponer el mensaje real
            echo json_encode([
                'success' => false,
                'message' => 'Error de conexión a la base de datos.'
            ]);
            exit;
        }

        return $this->connection;
    }
}
