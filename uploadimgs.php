<?php
$productID = $_POST['productID'];
$target_dir = "uploads/";

// Obtener la extensión del archivo original
$imageFileType = strtolower(pathinfo($_FILES["uploadImage"]["name"], PATHINFO_EXTENSION));

// Crear el nuevo nombre del archivo basado en el productID
$newFileName = $productID . "." . $imageFileType;

// Crear la ruta completa del archivo con el nuevo nombre
$target_file = $target_dir . $newFileName;
$uploadOk = 1;

// Eliminar cualquier archivo con el mismo nombre sin importar la extensión
foreach (glob($target_dir . $productID . ".*") as $existingFile) {
    unlink($existingFile); // Eliminar el archivo existente
}

// Verificar si el archivo es una imagen válida
if (isset($_POST["submit"])) {
    $check = getimagesize($_FILES["uploadImage"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo json_encode([
            "success" => false,
            "error" => "Sorry, file is not an image."
        ]);
        $uploadOk = 0;
    }
}

// Verificar el tamaño del archivo
if ($_FILES["uploadImage"]["size"] > 500000) {
    echo json_encode([
        "success" => false,
        "error" => "Sorry, your file is too large."
    ]);
    $uploadOk = 0;
}

// Permitir ciertos formatos de archivo
if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
    echo json_encode([
        "success" => false,
        "error" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."
    ]);
    $uploadOk = 0;
}

// Verificar si $uploadOk está en 0 por algún error
if ($uploadOk == 0) {
    echo json_encode([
        "success" => false,
        "error" => "Sorry, your file was not uploaded."
    ]);
} else {
    // Subir el archivo
    if (move_uploaded_file($_FILES["uploadImage"]["tmp_name"], $target_file)) {
        echo json_encode([
            "success" => true,
            "message" => "The file " . htmlspecialchars($newFileName) . " has been uploaded.",
            "id" => $productID
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "error" => "Sorry, your file was not uploaded."
        ]);
    }
}
?>
