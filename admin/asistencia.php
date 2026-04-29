<form action="procesar_asistencia.php" method="POST" enctype="multipart/form-data" class="card p-4">
    <div class="mb-3">
        <label>Seleccionar Curso</label>
        <select name="curso_id" class="form-select" required>
            </select>
    </div>
    <div class="mb-3">
        <label>Subir Lista (CSV delimitado por comas)</label>
        <input type="file" name="archivo_lista" class="form-control" accept=".csv" required>
    </div>
    <button type="submit" class="btn btn-success">Cargar Asistencia</button>
</form>