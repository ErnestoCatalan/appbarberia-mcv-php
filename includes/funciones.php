<?php

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

function esUltimo(string $actual, string $proximo): bool {
    if($actual !== $proximo) {
        return true;
    } 
    return false;
}

// Función que revisa que el usuario esta autenticado
function isAuth() : void {
    if(!isset($_SESSION['login'])) {
        header('Location: /');
    }
}

function isAdmin() : void {
    if(!isset($_SESSION['admin'])) {
        header('Location: /');
    }
}

// Función que revisa que el usuario sea superadmin
function isSuperAdmin() : void {
    if(!isset($_SESSION['login']) || $_SESSION['tipo'] !== 'superadmin') {
        header('Location: /');
    }
}

// Función que revisa que el usuario sea admin de barbería
function isAdminBarberia() : void {
    if(!isset($_SESSION['login']) || ($_SESSION['tipo'] !== 'admin_barberia' && $_SESSION['tipo'] !== 'superadmin')) {
        header('Location: /');
    }
}

// Obtener ID de barbería del usuario actual
function obtenerBarberiaId() {
    return $_SESSION['barberia_id'] ?? null;
}