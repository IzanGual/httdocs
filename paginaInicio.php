<?php
header('Allow: GET, POST, PUT, DELETE');

// Incloem la classe d'accés a dades
require 'bd/bdPaginaInicio.php';


// Instància de la classe DbEstudiants
$db = new bdPaginaInicio();

// Verificar el método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];

// Manejar las operaciones según el método
switch ($method) {
    case 'GET':
            $frases = $db->getSaludos(); // Obtenim les dades a travès de la capa d'accés a dades
            echo json_encode($frases); // Ho passem a JSON
        break;

    case 'POST':
        // Es un copia pega de el GET No deberia ser asi
        $frases = $db->getSaludos(); // Obtenim les dades a travès de la capa d'accés a dades
            echo json_encode($frases); // Ho passem a JSON
        break;
    case 'PUT':
         // Es un copia pega de el GET No deberia ser asi
        $frases = $db->getSaludos(); // Obtenim les dades a travès de la capa d'accés a dades
        echo json_encode($frases); // Ho passem a JSON
        break;

    case 'DELETE':
         // Es un copia pega de el GET No deberia ser asi
        $frases = $db->getSaludos(); // Obtenim les dades a travès de la capa d'accés a dades
            echo json_encode($frases); // Ho passem a JSON
        break;

    default:
        header('HTTP/1.1 405 Method Not Allowed');
        header('Allow: GET, POST, PUT, DELETE');
        break;
}
?>
