<?php
require_once __DIR__ . '/../config/Database.php';

class Asistencia
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function crear($data)
    {
        $sql = "INSERT INTO asistencias (id_alumno, materia, creditos, horas_asistidas, horas_justificadas, horas_injustificadas, porcentaje_asistencia)
                VALUES (:id_alumno, :materia, :creditos, :horas_asistidas, :horas_justificadas, :horas_injustificadas, :porcentaje_asistencia)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id_alumno' => $data['id_alumno'] ?? null,
            ':materia' => $data['materia'] ?? null,
            ':creditos' => $data['creditos'] ?? null,
            ':horas_asistidas' => $data['horas_asistidas'] ?? null,
            ':horas_justificadas' => $data['horas_justificadas'] ?? null,
            ':horas_injustificadas' => $data['horas_injustificadas'] ?? null,
            ':porcentaje_asistencia' => $data['porcentaje_asistencia'] ?? null,
        ]);
    }

    public function obtenerTodos()
    {
        $sql = "SELECT a.*, al.nombre, al.apellido
                FROM asistencias a
                INNER JOIN alumnos al ON a.id_alumno = al.id_alumno
                ORDER BY a.id_asistencia DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM asistencias WHERE id_asistencia = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    public function eliminar($id)
    {
        $sql = "DELETE FROM asistencias WHERE id_asistencia = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([':id' => $id]);
    }

    public function actualizar($data)
    {
        $sql = "UPDATE asistencias SET
                    id_alumno = :id_alumno,
                    materia = :materia,
                    creditos = :creditos,
                    horas_asistidas = :horas_asistidas,
                    horas_justificadas = :horas_justificadas,
                    horas_injustificadas = :horas_injustificadas,
                    porcentaje_asistencia = :porcentaje_asistencia
                WHERE id_asistencia = :id_asistencia";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id_alumno' => $data['id_alumno'] ?? null,
            ':materia' => $data['materia'] ?? null,
            ':creditos' => $data['creditos'] ?? null,
            ':horas_asistidas' => $data['horas_asistidas'] ?? null,
            ':horas_justificadas' => $data['horas_justificadas'] ?? null,
            ':horas_injustificadas' => $data['horas_injustificadas'] ?? null,
            ':porcentaje_asistencia' => $data['porcentaje_asistencia'] ?? null,
            ':id_asistencia' => $data['id_asistencia'] ?? $data['id'] ?? null,
        ]);
    }
}
