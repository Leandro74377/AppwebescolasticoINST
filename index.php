<?php
require_once __DIR__ . '/config/Database.php';
$db = Database::getConnection();

$alumnos = $db->query("SELECT * FROM alumnos ORDER BY apellido ASC")->fetchAll();
$asistencias = $db->query("SELECT a.*, al.nombre, al.apellido FROM asistencias a INNER JOIN alumnos al ON a.id_alumno = al.id_alumno ORDER BY a.id_asistencia DESC")->fetchAll();
$notas = $db->query("SELECT n.*, al.nombre, al.apellido FROM notas n INNER JOIN alumnos al ON n.id_alumno = al.id_alumno ORDER BY n.id_nota DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Control Escolar Integral</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">

    <div class="container mt-4">
        <div class="text-center mb-4">
            <h1 class="fw-bold text-dark">Plataforma de Gestión Académica</h1>
            <p class="text-muted">Módulos unificados de Alumnos, Asistencias y Calificaciones</p>
        </div>

        <ul class="nav nav-pills nav-justified mb-4 shadow-sm bg-white p-2 rounded" id="academicTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-bold" id="alumnos-tab" data-bs-toggle="tab" data-bs-target="#panel-alumnos" type="button" role="tab">📁 Registro de Alumnos</button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold" id="asistencias-tab" data-bs-toggle="tab" data-bs-target="#panel-asistencias" type="button" role="tab">⏱️ Control de Asistencias</button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold" id="notas-tab" data-bs-toggle="tab" data-bs-target="#panel-notas" type="button" role="tab">📝 Registro de Notas</button>
            </li>
        </ul>

        <div class="tab-content" id="academicTabsContent">

            <div class="tab-pane fade show active" id="panel-alumnos" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-primary text-white py-3"><h5 class="mb-0" id="t-alumno">Nuevo Estudiante</h5></div>
                            <div class="card-body p-4">
                                <form action="controllers/AsistenciaController.php" method="POST" id="form-alumno">
                                    <input type="hidden" name="modulo" value="alumno">
                                    <input type="hidden" name="accion" id="acc-alumno" value="crear">
                                    <input type="hidden" name="id_alumno" id="id_alumno">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Nombres</label>
                                        <input type="text" class="form-control" name="nombre" id="nom-alumno" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Apellidos</label>
                                        <input type="text" class="form-control" name="apellido" id="ape-alumno" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Cédula o Matrícula</label>
                                        <input type="text" class="form-control" name="cedula_matricula" id="ced-alumno" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100" id="btn-alumno">Registrar Alumno</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-0">
                                <table class="table table-hover align-middle mb-0 text-center">
                                    <thead class="table-dark">
                                        <tr><th>Matrícula / Cédula</th><th>Estudiante</th><th>Acciones</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($alumnos as $al): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($al['cedula_matricula']) ?></td>
                                            <td class="text-start ps-4"><?= htmlspecialchars($al['apellido'] . ' ' . $al['nombre']) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning text-white" onclick='edAlumno(<?= json_encode($al) ?>)'>Editar</button>
                                                <a href="controllers/AsistenciaController.php?eliminar=<?= $al['id_alumno'] ?>&tabla=alumnos" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar alumno y todo su historial?')">Borrar</a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="panel-asistencias" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-warning text-dark py-3"><h5 class="mb-0 fw-bold" id="t-asistencia">Tomar Asistencia</h5></div>
                            <div class="card-body p-4">
                                <form action="controllers/AsistenciaController.php" method="POST" id="form-asistencia">
                                    <input type="hidden" name="modulo" value="asistencia">
                                    <input type="hidden" name="accion" id="acc-asistencia" value="crear">
                                    <input type="hidden" name="id_asistencia" id="id_asistencia">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Alumno</label>
                                        <select class="form-select" name="id_alumno" id="sel-asistencia" required>
                                            <?php foreach ($alumnos as $al): ?><option value="<?= $al['id_alumno'] ?>"><?= htmlspecialchars($al['apellido'] . ' ' . $al['nombre']) ?></option><?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Materia</label>
                                        <input type="text" class="form-control" name="materia" id="mat-asistencia" required>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-6"><label class="form-label">Créditos</label><input type="number" class="form-control" name="creditos" id="cre-asistencia" required min="1"></div>
                                        <div class="col-6"><label class="form-label">H. Asistidas</label><input type="number" class="form-control" name="horas_asistidas" id="asi-asistencia" required min="0"></div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-6"><label class="form-label">H. Justificadas</label><input type="number" class="form-control" name="horas_justificadas" id="jus-asistencia" required min="0"></div>
                                        <div class="col-6"><label class="form-label">H. Injustificadas</label><input type="number" class="form-control" name="horas_injustificadas" id="inj-asistencia" required min="0"></div>
                                    </div>
                                    <button type="submit" class="btn btn-warning w-100 fw-bold" id="btn-asistencia">Guardar Asistencia</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-0">
                                <table class="table table-hover align-middle mb-0 text-center">
                                    <thead class="table-dark">
                                        <tr><th>Estudiante</th><th>Materia</th><th>Asistidas</th><th>Faltas</th><th>%</th><th>Acciones</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($asistencias as $as): ?>
                                        <tr>
                                            <td class="text-start ps-3"><?= htmlspecialchars($as['apellido'] . ' ' . $as['nombre']) ?></td>
                                            <td><?= htmlspecialchars($as['materia']) ?></td>
                                            <td><?= $as['horas_asistidas'] ?>h</td>
                                            <td><?= $as['horas_justificadas'] ?>J / <?= $as['horas_injustificadas'] ?>I</td>
                                            <td class="fw-bold <?= $as['porcentaje_asistencia'] >= 75 ? 'text-success' : 'text-danger' ?>"><?= $as['porcentaje_asistencia'] ?>%</td>
                                            <td>
                                                <button class="btn btn-sm btn-warning text-white" onclick='edAsistencia(<?= json_encode($as) ?>)'>Editar</button>
                                                <a href="controllers/AsistenciaController.php?eliminar=<?= $as['id_asistencia'] ?>&tabla=asistencias" class="btn btn-sm btn-danger">Borrar</a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="panel-notas" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-success text-white py-3"><h5 class="mb-0" id="t-nota">Calificar Materia</h5></div>
                            <div class="card-body p-4">
                                <form action="controllers/AsistenciaController.php" method="POST" id="form-nota">
                                    <input type="hidden" name="modulo" value="nota">
                                    <input type="hidden" name="accion" id="acc-nota" value="crear">
                                    <input type="hidden" name="id_nota" id="id_nota">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Alumno</label>
                                        <select class="form-select" name="id_alumno" id="sel-nota" required>
                                            <?php foreach ($alumnos as $al): ?><option value="<?= $al['id_alumno'] ?>"><?= htmlspecialchars($al['apellido'] . ' ' . $al['nombre']) ?></option><?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Materia</label>
                                        <input type="text" class="form-control" name="materia" id="mat-nota" required>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-6"><label class="form-label fw-semibold">Parcial 1</label><input type="number" step="0.01" class="form-control" name="nota_parcial1" id="p1-nota" required min="0" max="10"></div>
                                        <div class="col-6"><label class="form-label fw-semibold">Parcial 2</label><input type="number" step="0.01" class="form-control" name="nota_parcial2" id="p2-nota" required min="0" max="10"></div>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100" id="btn-nota">Guardar Calificación</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-0">
                                <table class="table table-hover align-middle mb-0 text-center">
                                    <thead class="table-dark">
                                        <tr><th>Estudiante</th><th>Materia</th><th>P1</th><th>P2</th><th>Promedio</th><th>Estado</th><th>Acciones</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($notas as $nt): ?>
                                        <tr>
                                            <td class="text-start ps-3"><?= htmlspecialchars($nt['apellido'] . ' ' . $nt['nombre']) ?></td>
                                            <td><?= htmlspecialchars($nt['materia']) ?></td>
                                            <td><?= $nt['nota_parcial1'] ?></td>
                                            <td><?= $nt['nota_parcial2'] ?></td>
                                            <td class="fw-bold"><?= $nt['nota_final'] ?></td>
                                            <td><span class="badge <?= $nt['estado'] === 'Aprobado' ? 'bg-success' : 'bg-danger' ?>"><?= $nt['estado'] ?></span></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning text-white" onclick='edNota(<?= json_encode($nt) ?>)'>Editar</button>
                                                <a href="controllers/AsistenciaController.php?eliminar=<?= $nt['id_nota'] ?>&tabla=notas" class="btn btn-sm btn-danger">Borrar</a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function edAlumno(d) {
            document.getElementById('t-alumno').innerText = "Modificar Alumno";
            document.getElementById('acc-alumno').value = "editar";
            document.getElementById('id_alumno').value = d.id_alumno;
            document.getElementById('nom-alumno').value = d.nombre;
            document.getElementById('ape-alumno').value = d.apellido;
            document.getElementById('ced-alumno').value = d.cedula_matricula;
            document.getElementById('btn-alumno').innerText = "Actualizar Cambios";
        }

        function edAsistencia(d) {
            document.getElementById('t-asistencia').innerText = "Modificar Asistencia";
            document.getElementById('acc-asistencia').value = "editar";
            document.getElementById('id_asistencia').value = d.id_asistencia;
            document.getElementById('sel-asistencia').value = d.id_alumno;
            document.getElementById('mat-asistencia').value = d.materia;
            document.getElementById('cre-asistencia').value = d.creditos;
            document.getElementById('asi-asistencia').value = d.horas_asistidas;
            document.getElementById('jus-asistencia').value = d.horas_justificadas;
            document.getElementById('inj-asistencia').value = d.horas_injustificadas;
            document.getElementById('btn-asistencia').innerText = "Actualizar Cambios";
        }

        function edNota(d) {
            document.getElementById('t-nota').innerText = "Modificar Nota";
            document.getElementById('acc-nota').value = "editar";
            document.getElementById('id_nota').value = d.id_nota;
            document.getElementById('sel-nota').value = d.id_alumno;
            document.getElementById('mat-nota').value = d.materia;
            document.getElementById('p1-nota').value = d.nota_parcial1;
            document.getElementById('p2-nota').value = d.nota_parcial2;
            document.getElementById('btn-nota').innerText = "Actualizar Cambios";
        }
    </script>
</body>
</html>