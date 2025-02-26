<?php
// Directorio donde se encuentran las imágenes
$target_dir = "uploads/";

// Verificar si se ha pasado el parámetro 'imageNameId' en la URL
if (isset($_GET['imageNameId'])) {
    $imageNameId = $_GET['imageNameId'];

    // Buscar cualquier archivo con el nombre proporcionado, sin importar la extensión
    $files = glob($target_dir . $imageNameId . ".*");

    if (count($files) > 0) {
        // Eliminar todos los archivos que coincidan
        foreach ($files as $file) {
            if (unlink($file)) {
                echo json_encode([
                    "success" => true,
                    "message" => "File " . basename($file) . " has been deleted."
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "error" => "Failed to delete file: " . basename($file)
                ]);
            }
        }
    } else {
        // No se encontró ningún archivo con el nombre especificado
        echo json_encode([
            "success" => false,
            "error" => "No files found with the name: " . htmlspecialchars($imageNameId)
        ]);
    }
} else {
    // No se proporcionó el parámetro 'imageNameId'
    echo json_encode([
        "success" => false,
        "error" => "Missing parameter 'imageNameId'."
    ]);
}
?>
