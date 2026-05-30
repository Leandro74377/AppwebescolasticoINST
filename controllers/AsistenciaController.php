<?php
require_once __DIR__ . '/../models/Asistencia.php';

$asistencia = new Asistencia();

// Manejo de POST: crear o actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion']) && $_POST['accion'] === 'editar') {
        $asistencia->actualizar($_POST);
    } else {
        $asistencia->crear($_POST);
    }

    header('Location: ../index.php');
    exit();
}

// Manejo de GET para eliminar
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $asistencia->eliminar($id);

    header('Location: ../index.php');
    exit();
}
