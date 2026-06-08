<?php
require_once __DIR__ . '/../fpdf/fpdf.php';
require_once __DIR__ . '/../config/Database.php';

class PDF_Reporte extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', '15');
        $this->SetTextColor(33, 37, 41);
        $this->Cell(0, 10, utf8_decode('SISTEMA DE CONTROL ESCOLAR INTEGRAL'), 0, 1, 'C');
        $this->SetFont('Arial', '', '11');
        $this->SetTextColor(108, 117, 125);
        $this->Cell(0, 6, utf8_decode('Reporte Consolidado Académico, Notas y Asistencias'), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(108, 117, 125);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$db = Database::getConnection();

$sql = "SELECT 
            al.cedula_matricula,
            al.apellido,
            al.nombre,
            asi.materia,
            asi.porcentaje_asistencia,
            n.nota_final,
            n.estado
        FROM alumnos al
        LEFT JOIN asistencias i ON al.id_alumno = asi.id_alumno
        LEFT JOIN notas n ON al.id_alumno = n.id_alumno AND asi.materia = n.materia
        ORDER BY al.apellido ASC, asi.materia ASC";

$stmt = $db->query($sql);
$registros = $stmt->fetchAll();

$pdf = new PDF_Reporte('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(33, 37, 41);
$pdf->SetTextColor(255, 255, 255);

$pdf->Cell(35, 8, utf8_decode('Cédula/Matrícula'), 1, 0, 'C', true);
$pdf->Cell(65, 8, utf8_decode('Estudiante'), 1, 0, 'C', true);
$pdf->Cell(65, 8, utf8_decode('Materia / Asignatura'), 1, 0, 'C', true);
$pdf->Cell(35, 8, utf8_decode('% Asistencia'), 1, 0, 'C', true);
$pdf->Cell(35, 8, utf8_decode('Nota Final'), 1, 0, 'C', true);
$pdf->Cell(42, 8, utf8_decode('Estado'), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(0, 0, 0);

if (empty($registros)) {
    $pdf->Cell(277, 10, utf8_decode('No existen registros académicos en el sistema.'), 1, 1, 'C');
} else {
    $fill = false;
    foreach ($registros as $row) {
        $pdf->SetFillColor(248, 249, 250);
        
        $estudiante = $row['apellido'] . ' ' . $row['nombre'];
        $materia = $row['materia'] ?? 'Sin Asignar';
        $asistencia = isset($row['porcentaje_asistencia']) ? $row['porcentaje_asistencia'] . '%' : 'N/A';
        $nota = isset($row['nota_final']) ? $row['nota_final'] : 'N/A';
        $estado = $row['estado'] ?? 'N/A';

        $pdf->Cell(35, 8, utf8_decode($row['cedula_matricula']), 1, 0, 'C', $fill);
        $pdf->Cell(65, 8, utf8_decode($estudiante), 1, 0, 'L', $fill);
        $pdf->Cell(65, 8, utf8_decode($materia), 1, 0, 'L', $fill);
        $pdf->Cell(35, 8, utf8_decode($asistencia), 1, 0, 'C', $fill);
        $pdf->Cell(35, 8, utf8_decode($nota), 1, 0, 'C', $fill);
        
        if ($estado === 'Aprobado') {
            $pdf->SetTextColor(25, 135, 84);
        } elseif ($estado === 'Reprobado') {
            $pdf->SetTextColor(220, 53, 69);
        }
        
        $pdf->Cell(42, 8, utf8_decode($estado), 1, 1, 'C', $fill);
        $pdf->SetTextColor(0, 0, 0);
        
        $fill = !$fill;
    }
}

$pdf->Output('I', 'Reporte_Academico_General.pdf');