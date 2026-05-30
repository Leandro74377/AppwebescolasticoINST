<?php
require_once __DIR__ . '/models/Asistencia.php';
$asistenciaModel = new Asistencia();
$registros = $asistenciaModel->obtenerTodos();

require_once __DIR__ . '/config/Database.php';
$db = Database::getConnection();
$alumnos = $db->query("SELECT id_alumno, nombre, apellido FROM alumnos")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Control de Asistencias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="row">
            <div class="col-12 text-center mb-4">
                <h1 class="fw-bold text-primary">Sistema de Control Escolar - Asistencias</h1>
                <p class="text-muted">Registro dinámico utilizando arquitectura MVC y PDO</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="card-title mb-0" id="form-title">Registrar Asistencia</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="controllers/AsistenciaController.php" method="POST" id="asistencia-form">
                            <input type="hidden" name="id_asistencia" id="id_asistencia">
                            <input type="hidden" name="accion" id="accion" value="crear">

                            <div class="mb-3">
                                <label for="id_alumno" class="form-label fw-semibold">Seleccionar Alumno</label>
                                <select class="form-select" name="id_alumno" id="id_alumno" required>
                                    <option value="">-- Seleccione un estudiante --</option>
                                    <?php foreach ($alumnos as $alumno): ?>
                                        <option value="<?= $alumno['id_alumno'] ?>"><?= htmlspecialchars($alumno['apellido'] . ' ' . $alumno['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="materia" class="form-label fw-semibold">Materia / Asignatura</label>
                                <input type="text" class="form-control" name="materia" id="materia" required placeholder="Ej. Programación PHP">
                            </div>

                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label for="creditos" class="form-label fw-semibold">Créditos</label>
                                    <input type="number" class="form-control" name="creditos" id="creditos" required min="1">
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="horas_asistidas" class="form-label fw-semibold">H. Asistidas</label>
                                    <input type="number" class="form-control" name="horas_asistidas" id="horas_asistidas" required min="0" oninput="calcularPorcentaje()">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label for="horas_justificadas" class="form-label fw-semibold">H. Justificadas</label>
                                    <input type="number" class="form-control" name="horas_justificadas" id="horas_justificadas" required min="0" oninput="calcularPorcentaje()">
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="horas_injustificadas" class="form-label fw-semibold">H. Injustificadas</label>
                                    <input type="number" class="form-control" name="horas_injustificadas" id="horas_injustificadas" required min="0" oninput="calcularPorcentaje()">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="porcentaje_asistencia" class="form-label fw-semibold text-success">Porcentaje de Asistencia (%)</label>
                                <input type="number" step="0.01" class="form-control bg-light fw-bold text-success" name="porcentaje_asistencia" id="porcentaje_asistencia" readonly value="0.00">
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" id="btn-submit">Guardar Registro</button>
                                <button type="button" class="btn btn-secondary d-none" id="btn-cancelar" onclick="limpiarFormulario()">Cancelar Edición</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white py-3">
                        <h5 class="card-title mb-0">Registros de Asistencias Clases</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>Estudiante</th>
                                        <th>Materia</th>
                                        <th>Créditos</th>
                                        <th>Asistidas</th>
                                        <th>Faltas (J/I)</th>
                                        <th>Porcentaje</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($registros)): ?>
                                        <tr>
                                            <td colspan="7" class="text-muted py-4">No hay asistencias registradas en este periodo.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($registros as $row): ?>
                                            <tr>
                                                <td class="text-start ps-3 fw-semibold"><?= htmlspecialchars($row['apellido'] . ' ' . $row['nombre']) ?></td>
                                                <td><?= htmlspecialchars($row['materia']) ?></td>
                                                <td><span class="badge bg-secondary"><?= $row['creditos'] ?></span></td>
                                                <td><?= $row['horas_asistidas'] ?> h</td>
                                                <td>
                                                    <span class="text-success"><?= $row['horas_justificadas'] ?></span> / 
                                                    <span class="text-danger"><?= $row['horas_injustificadas'] ?></span>
                                                </td>
                                                <td class="fw-bold <?= $row['porcentaje_asistencia'] >= 75 ? 'text-success' : 'text-danger' ?>">
                                                    <?= $row['porcentaje_asistencia'] ?>%
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-warning text-white" onclick='cargarDatosEditar(<?= json_encode($row) ?>)'>Editar</button>
                                                        <a href="controllers/AsistenciaController.php?eliminar=<?= $row['id_asistencia'] ?>" class="btn btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este registro?')">Eliminar</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function calcularPorcentaje() {
            const asistidas = parseInt(document.getElementById('horas_asistidas').value) || 0;
            const justificadas = parseInt(document.getElementById('horas_justificadas').value) || 0;
            const injustificadas = parseInt(document.getElementById('horas_injustificadas').value) || 0;
            
            const totalHoras = asistidas + justificadas + injustificadas;
            
            if (totalHoras > 0) {
                const porcentaje = (asistidas / totalHoras) * 100;
                document.getElementById('porcentaje_asistencia').value = porcentaje.toFixed(2);
            } else {
                document.getElementById('porcentaje_asistencia').value = "0.00";
            }
        }

        function cargarDatosEditar(data) {
            document.getElementById('form-title').innerText = "Modificar Asistencia";
            document.getElementById('accion').value = "editar";
            document.getElementById('id_asistencia').value = data.id_asistencia;
            document.getElementById('id_alumno').value = data.id_alumno;
            document.getElementById('materia').value = data.materia;
            document.getElementById('creditos').value = data.creditos;
            document.getElementById('horas_asistidas').value = data.horas_asistidas;
            document.getElementById('horas_justificadas').value = data.horas_justificadas;
            document.getElementById('horas_injustificadas').value = data.horas_injustificadas;
            document.getElementById('porcentaje_asistencia').value = data.porcentaje_asistencia;
            
            document.getElementById('btn-submit').innerText = "Actualizar Registro";
            document.getElementById('btn-submit').classList.replace('btn-primary', 'btn-success');
            document.getElementById('btn-cancelar').classList.remove('d-none');
        }

        function limpiarFormulario() {
            document.getElementById('asistencia-form').reset();
            document.getElementById('form-title').innerText = "Registrar Asistencia";
            document.getElementById('accion').value = "crear";
            document.getElementById('id_asistencia').value = "";
            
            document.getElementById('btn-submit').innerText = "Guardar Registro";
            document.getElementById('btn-submit').classList.replace('btn-success', 'btn-primary');
            document.getElementById('btn-cancelar').classList.add('d-none');
        }
    </script>
</body>
</html>