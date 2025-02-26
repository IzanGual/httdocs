<?php
header('Allow: GET, POST, PUT, DELETE');

// Incloem la classe d'accés a dades
require 'bd/bdProductos.php';


// Instància de la classe DbEstudiants
$db = new bdProductos();

// Verificar el método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];

function getListFullFitered() {
   
    //echo $_GET['textNameSearch']. $_GET['categorySelected'].  $_GET['min'] . $_GET['max'];
        try
        {   
            $db = new bdProductos();
            $response = $db->getProductesFullFiltered($_GET['textNameSearch'], $_GET['categorySelected'], $_GET['min'], $_GET['max']);
            if ($response) {
                
                // Respuesta en caso de éxito
                echo json_encode([
                    "success" => true,
                    "message" => "Filtro aplicado con exito",
                    "list" => $response 
                ]);
            } else {
                
                // Respuesta en caso de error
                //http_response_code(500); // Código de error
                echo json_encode([
                    "success" => false,
                    "error" => "No se ha encontrado ningun producto con estos filtros"
                ]);
            }
        } catch (Exception $e) {
            
            // Manejar errores y retornar JSON
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode([
                "success" => false,
                "error" => "Error insertando producto: " . $e->getMessage()
            ]);
        }
}

// Manejar las operaciones según el método
switch ($method) {
    case 'GET':
        if(isset($_GET['giveCodRefs'])){
            $codrefs = $db->getAllCodrefs();
            if ($codrefs){
                echo json_encode($codrefs); // Ho passem a JSON
            }
            
        }else if (isset($_GET['textNameSearch']) || isset($_GET['categorySelected']) || isset($_GET['min']) || isset($_GET['max'])) {
                getListFullFitered();
            }else if(isset($_GET['id'])){
                $productoconID =$db->getProductoByID($_GET['id']);
                echo json_encode($productoconID);
            }else if(isset($_GET['name'])){
                $productosFiltrados =$db->getProductosFiltradoPorNombre($_GET['name']);
                echo json_encode($productosFiltrados);
            }
            else if(isset($_GET['categoria'])){
                $productosFiltradosPorCat =$db->getProductosFiltradoPorCat($_GET['categoria']);
                echo json_encode($productosFiltradosPorCat);
            }else{
                $productos = $db->getProductos(); // Obtenim les dades a travès de la capa d'accés a dades
                echo json_encode($productos); // Ho passem a JSON
            }

            
        break;

    case 'POST':
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
      
        // Insertar un nou estudiant
        // A data  tindrem un array associatiu amb els paràmetres que rebem per POST
        // Haurem de rebre les dades diferents si ens ve en format JSON, o per formulari POST
        // Ens haurem d'assegurar d'indicar bé el content type si cridem desde JS

         // TO-DEBUG -- PER A COMPROVAR QUE PASSA SI NO HO AGAFEM COM TOCA
        //echo json_encode($contentType);
        //$data = json_decode(file_get_contents('php://input'), true);
        //parse_str(file_get_contents('php://input'), $data);
        //echo json_encode($data);
        

        // Amb este if ens assegurem que rebem $data com toca depenent del contentType
        // Sempre que el contentType sigue correcte i els paràmetres s'envien codificats com toca

        
        if (strpos($contentType, 'application/json') !== false) {
            // Si el Content-Type es JSON, usamos json_decode
            $data = json_decode(file_get_contents('php://input'), true);
        } elseif (strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
            // Si el Content-Type es form-urlencoded, usamos parse_str
            parse_str(file_get_contents('php://input'), $data);
        } else {
            // Si el Content-Type no coincide con ninguno, enviamos un error
            header('HTTP/1.1 400 Bad Request');
            //echo json_encode(['error' => 'Unsupported Content-Type']);
            exit;
        }

        ////////////////////////////////// AQUI MIRAMOS COMO HACER PARA QUE SE SUBA LA IMAGEN

                
        
        
        ////////////////////////////////// AQUI MIRAMOS COMO HACER PARA QUE SE SUBA LA IMAGEN

            
       
        // Insertem a través de la capa d'accés i tornem en JSON el resultat
        try
        {
            $response = $db->insertProducte($data['catid'], $data['name'], $data['pvp'], $data['img_url'], $data['descripcion'], $data['codref']);
            if ($response) {
                // Respuesta en caso de éxito
                echo json_encode([
                    "success" => true,
                    "message" => "Producto insertado con exito",
                    "id" => $response // Devuelve el ID del producto insertado
                ]);
            } else {
                // Respuesta en caso de error
                http_response_code(500); // Código de error
            }
        } catch (Exception $e) {
            // Manejar errores y retornar JSON
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode([
                "success" => false,
                "error" => "Error insertando producto: " . $e->getMessage()
            ]);
        }

        break;
    case 'PUT':

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
      
        // Insertar un nou estudiant
        // A data  tindrem un array associatiu amb els paràmetres que rebem per POST
        // Haurem de rebre les dades diferents si ens ve en format JSON, o per formulari POST
        // Ens haurem d'assegurar d'indicar bé el content type si cridem desde JS

         // TO-DEBUG -- PER A COMPROVAR QUE PASSA SI NO HO AGAFEM COM TOCA
        //echo json_encode($contentType);
        //$data = json_decode(file_get_contents('php://input'), true);
        //parse_str(file_get_contents('php://input'), $data);
        //echo json_encode($data);
        

        // Amb este if ens assegurem que rebem $data com toca depenent del contentType
        // Sempre que el contentType sigue correcte i els paràmetres s'envien codificats com toca

        
        if (strpos($contentType, 'application/json') !== false) {
            // Si el Content-Type es JSON, usamos json_decode
            $data = json_decode(file_get_contents('php://input'), true);
        } elseif (strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
            // Si el Content-Type es form-urlencoded, usamos parse_str
            parse_str(file_get_contents('php://input'), $data);
        } else {
            // Si el Content-Type no coincide con ninguno, enviamos un error
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Unsupported Content-Type']);
            exit;
        }
            
       
        // Insertem a través de la capa d'accés i tornem en JSON el resultat
        try
        {
            $response = $db->updateProducte($data['id'], $data['catid'], $data['name'], $data['pvp'], $data['img_url'], $data['descripcion'], $data['codref']);
            if ($response) {
                // Si la actualización fue exitosa, devolver la respuesta en formato JSON
                echo json_encode([
                    "success" => true,
                    "message" => "Producto actualizado correctamente",
                    "id" => $response // Devolver el ID del producto actualizado
                ]);
            } else {
                // Si hubo un error, devolver un mensaje de error en formato JSON
                http_response_code(500); // Código de error del servidor
                echo json_encode([
                    "success" => false,
                    "error" => "Error al actualizar el producto"
                ]);
            }
        } catch (Exception $e) {
            // Manejar excepciones de forma genérica
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['error' => 'Error fatal al actualizar el producto: ' . $e->getMessage()]);
        }










        break;

    case 'DELETE':
        
        

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
      
        // Insertar un nou estudiant
        // A data  tindrem un array associatiu amb els paràmetres que rebem per POST
        // Haurem de rebre les dades diferents si ens ve en format JSON, o per formulari POST
        // Ens haurem d'assegurar d'indicar bé el content type si cridem desde JS

         // TO-DEBUG -- PER A COMPROVAR QUE PASSA SI NO HO AGAFEM COM TOCA
        //echo json_encode($contentType);
        //$data = json_decode(file_get_contents('php://input'), true);
        //parse_str(file_get_contents('php://input'), $data);
        //echo json_encode($data);
        

        // Amb este if ens assegurem que rebem $data com toca depenent del contentType
        // Sempre que el contentType sigue correcte i els paràmetres s'envien codificats com toca

        
        if (strpos($contentType, 'application/json') !== false) {
            // Si el Content-Type es JSON, usamos json_decode
            $data = json_decode(file_get_contents('php://input'), true);
        } elseif (strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
            // Si el Content-Type es form-urlencoded, usamos parse_str
            parse_str(file_get_contents('php://input'), $data);
        } else {
            // Si el Content-Type no coincide con ninguno, enviamos un error
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Unsupporteddddd Content-Type']);
            exit;
        }
            
       
        // Insertem a través de la capa d'accés i tornem en JSON el resultat
        try
        {
            $response = $db->deleteProducto($data['id']);
            
            if ($response) {
                // Respuesta en caso de éxito
                echo json_encode([
                    "message" => "Producto eliminado con exito"
                ]);
            } else {
                // Respuesta en caso de error
                http_response_code(500); // Código de error
                echo json_encode([
                    "error" => "Error al eliminar el Producto"
                ]);
            }
            

        } catch (Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            print json_encode(['error' => 'Error eliminandoi producte']);
       }


       break;



    default:
        header('HTTP/1.1 405 Method Not Allowed');
        header('Allow: GET, POST, PUT, DELETE');
        break;
}
?>
