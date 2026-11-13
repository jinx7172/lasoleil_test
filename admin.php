<?php
require 'includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $categoria = $_POST['categoria'];
    $stock = $_POST['stock']; // NUEVO CAMPO
    
    $imagen_nombre = 'default.jpg';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $directorio = __DIR__ . "/assets/img/";
        $nombre_archivo = preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES['imagen']['name']));
        $imagen_nombre = time() . "_" . $nombre_archivo; 
        move_uploaded_file($_FILES['imagen']['tmp_name'], $directorio . $imagen_nombre);
    }

    $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, categoria, stock, imagen) VALUES (?, ?, ?, ?, ?, ?)");
    // "ssdsis" significa: string, string, double, string, integer, string
    $stmt->bind_param("ssdsis", $nombre, $descripcion, $precio, $categoria, $stock, $imagen_nombre);

    if ($stmt->execute()) {
        $mensaje = "¡Producto agregado con éxito!";
    } else {
        $error = "Error al agregar: " . $conn->error;
    }
}
require 'includes/header.php';
?>

<div class="form-container">
    <h2 style="color: var(--rosa-oscuro); text-align: center;">Agregar Producto</h2>
    <?php if(isset($mensaje)) echo "<p style='color:green; font-weight:bold;'>$mensaje</p>"; ?>
    <?php if(isset($error)) echo "<p style='color:red; font-weight:bold;'>$error</p>"; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label>Nombre:</label>
            <input type="text" name="nombre" required>
        </div>
        <div class="form-group">
            <label>Descripción:</label>
            <textarea name="descripcion" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label>Precio (Bs):</label>
            <input type="number" step="0.01" name="precio" required>
        </div>
        <div class="form-group">
            <label>Categoría:</label>
            <select name="categoria">
                <option value="Panes">Panes</option>
                <option value="Masitas">Masitas</option>
                <option value="Galletas">Galletas</option>
                <option value="Postres">Postres</option>
                <option value="Bebidas">Bebidas</option>
            </select>
        </div>
        <!-- NUEVO CAMPO STOCK -->
        <div class="form-group">
            <label>Existencias (Stock):</label>
            <input type="number" name="stock" value="10" required>
        </div>
        <div class="form-group">
            <label>Imagen del producto:</label>
            <input type="file" name="imagen" accept="image/*">
        </div>
        <button type="submit" class="btn" style="width: 100%;">Guardar Producto</button>
    </form>
</div>

<?php require 'includes/footer.php'; ?>