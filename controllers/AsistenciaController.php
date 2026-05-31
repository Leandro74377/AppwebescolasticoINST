<?php
require_once __DIR__ . '/../config/Database.php';

$db = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modulo = $_POST['modulo'] ?? '';
    $accion = $_POST['accion'] ?? 'crear';

    if ($modulo === 'alumno') {
        if ($accion === 'crear') {
            $stmt = $db->prepare("INSERT INTO alumnos (nombre, apellido, cedula_matricula) VALUES (:nombre, :apellido, :cedula_matricula)");
            $stmt->execute([':nombre' => $_POST['nombre'], ':apellido' => $_POST['apellido'], ':cedula_matricula' => $_POST['cedula_matricula']]);
        } elseif ($accion === 'editar') {
            $stmt = $db->prepare("UPDATE alumnos SET nombre = :nombre, apellido = :apellido, cedula_matricula = :cedula_matricula WHERE id_alumno = :id_alumno");
            $stmt->execute([':nombre' => $_POST['nombre'], ':apellido' => $_POST['apellido'], ':cedula_matricula' => $_POST['cedula_matricula'], ':id_alumno' => $_POST['id_alumno']]);
        }
    } 
    
    elseif ($modulo === 'asistencia') {
        $total = intval($_POST['horas_asistidas']) + intval($_POST['horas_justificadas']) + intval($_POST['horas_injustificadas']);
        $porcentaje = $total > 0 ? (intval($_POST['horas_asistidas']) / $total) * 100 : 0;

        if ($accion === 'crear') {
            $stmt = $db->prepare("INSERT INTO asistencias (id_alumno, materia, creditos, horas_asistidas, horas_justificadas, horas_injustificadas, porcentaje_asistencia) VALUES (:id_alumno, :materia, :creditos, :horas_asistidas, :horas_justificadas, :horas_injustificadas, :porcentaje)");
            $stmt->execute([
                ':id_alumno' => $_POST['id_alumno'], ':materia' => $_POST['materia'], ':creditos' => $_POST['creditos'],
                ':horas_asistidas' => $_POST['horas_asistidas'], ':horas_justificadas' => $_POST['horas_justificadas'],
                ':horas_injustificadas' => $_POST['horas_injustificadas'], ':porcentaje' => $porcentaje
            ]);
        } elseif ($accion === 'editar') {
            $stmt = $db->prepare("UPDATE asistencias SET id_alumno = :id_alumno, materia = :materia, creditos = :creditos, horas_asistidas = :horas_asistidas, horas_justificadas = :horas_justificadas, horas_injustificadas = :horas_injustificadas, porcentaje_asistencia = :porcentaje WHERE id_asistencia = :id_asistencia");
            $stmt->execute([
                ':id_alumno' => $_POST['id_alumno'], ':materia' => $_POST['materia'], ':creditos' => $_POST['creditos'],
                ':horas_asistidas' => $_POST['horas_asistidas'], ':horas_justificadas' => $_POST['horas_justificadas'],
                ':horas_injustificadas' => $_POST['horas_injustificadas'], ':porcentaje' => $porcentaje, ':id_asistencia' => $_POST['id_asistencia']
            ]);
        }
    } 
    
    elseif ($modulo === 'nota') {
        $p1 = floatval($_POST['nota_parcial1']);
        $p2 = floatval($_POST['nota_parcial2']);
        $final = ($p1 + $p2) / 2;
        $estado = $final >= 7 ? 'Aprobado' : 'Reprobado';

        if ($accion === 'crear') {
            $stmt = $db->prepare("INSERT INTO notas (id_alumno, materia, nota_parcial1, nota_parcial2, nota_final, estado) VALUES (:id_alumno, :materia, :p1, :p2, :final, :estado)");
            $stmt->execute([':id_alumno' => $_POST['id_alumno'], ':materia' => $_POST['materia'], ':p1' => $p1, ':p2' => $p2, ':final' => $final, ':estado' => $estado]);
        } elseif ($accion === 'editar') {
            $stmt = $db->prepare("UPDATE notas SET id_alumno = :id_alumno, materia = :materia, nota_parcial1 = :p1, nota_parcial2 = :p2, nota_final = :final, estado = :estado WHERE id_nota = :id_nota");
            $stmt->execute([':id_alumno' => $_POST['id_alumno'], ':materia' => $_POST['materia'], ':p1' => $p1, ':p2' => $p2, ':final' => $final, ':estado' => $estado, ':id_nota' => $_POST['id_nota']]);
        }
    }

    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['eliminar'])) {
    $tabla = $_GET['tabla'] ?? '';
    $id = $_GET['eliminar'];

    if ($tabla === 'alumnos') {
        $stmt = $db->prepare("DELETE FROM alumnos WHERE id_alumno = ?");
    } elseif ($tabla === 'asistencias') {
        $stmt = $db->prepare("DELETE FROM asistencias WHERE id_asistencia = ?");
    } elseif ($tabla === 'notas') {
        $stmt = $db->prepare("DELETE FROM notas WHERE id_nota = ?");
    }

    if (isset($stmt)) {
        $stmt->execute([$id]);
    }

    header('Location: ../index.php');
    exit();
}