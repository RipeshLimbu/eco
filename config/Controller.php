<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Database.php';

class Controller {
    protected $db;
    protected $user;

    public function __construct() {
        $this->db = new Database();
        $this->initSession();
    }

    protected function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id'])) {
            $this->user = [
                'id' => $_SESSION['user_id'],
                'role' => $_SESSION['role'],
                'name' => $_SESSION['full_name']
            ];
        }
    }

    protected function requireLogin() {
        if (!isset($this->user)) {
            header("Location: " . BASE_URL . "/login.php");
            exit();
        }
    }

    protected function requireRole($role) {
        $this->requireLogin();
        if ($this->user['role'] !== $role) {
            header("Location: " . BASE_URL . "/login.php");
            exit();
        }
    }

    protected function redirect($path, $message = null, $type = 'success') {
        if ($message) {
            $_SESSION['flash'] = [
                'message' => $message,
                'type' => $type
            ];
        }
        header("Location: " . BASE_URL . $path);
        exit();
    }
} 