<?php

class bdProductos
{
    private $pdo; // Variable per a la connexió PDO

    // Constructor: inicializa la conexió a la bbdd
    public function __construct()
    {
        // Inclou l'arxiu de configuració
        $config = include 'bdConf.php';

        try {
            $this->pdo = new PDO( "mysql:host={$config['db_host']};dbname={$config['db_name']}",
                                   $config['db_user'],
                                   $config['db_pass']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    // Retorna tots els Saludos
    public function getProductos()
    {
            $stmt = $this->pdo->query('SELECT * FROM productos');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Retorna los productos con el catid igual a $id
    public function getProductoByID($id)
    {
    // Preparar la consulta SQL para obtener los productos por catid
    $stmt = $this->pdo->prepare('SELECT * FROM productos WHERE id = :id');
    
    // Asociar el parámetro con bindParam
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    // Ejecutar la consulta
    $stmt->execute();
    
    // Retornar los resultados como un array asociativo
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


    // Retorna tots els Productos filtrados por nombre
    public function getProductosFiltradoPorNombre($name)
{
    // Convertir el valor de $name a minúsculas
    $name = strtolower($name);
    
    // Preparar la consulta SQL para buscar por nombre
    $stmt = $this->pdo->prepare('
        SELECT * FROM productos 
        WHERE LOWER(name) LIKE :name
    ');
    
    // Asociar el parámetro con bindParam
    $stmt->bindParam(':name', $nameParam, PDO::PARAM_STR);
    
    // Agregar los comodines para buscar parcialmente
    $nameParam = '%' . $name . '%';
    
    // Ejecutar la consulta
    $stmt->execute();
    
    // Retornar los resultados como un array asociativo
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function getProductosFiltradoPorCat($idCategoria) {
    // Preparar la consulta SQL para buscar productos por categoría
    $stmt = $this->pdo->prepare('
        SELECT * FROM productos 
        WHERE catid = :idCategoria
    ');
    
    // Asociar el parámetro con bindParam
    $stmt->bindParam(':idCategoria', $idCategoria, PDO::PARAM_INT);
    
    // Ejecutar la consulta
    $stmt->execute();
    
    // Retornar los resultados como un array asociativo
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function insertProducte($catid, $name, $pvp, $img_url, $descripcion, $codref) {
    try {
        // Preparar la consulta SQL con los nuevos campos
        $stmt = $this->pdo->prepare('
            INSERT INTO productos (catid, name, pvp, img_url, descripcion, codref) 
            VALUES (:catid, :name, :pvp, :img_url, :descripcion, :codref)
        ');

        // Asignar valores a los parámetros
        $stmt->bindParam(':catid', $catid, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':pvp', $pvp, PDO::PARAM_STR); // PVP en formato decimal
        $stmt->bindParam(':img_url', $img_url, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':codref', $codref, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Retornar el id del producto insertado
        return $this->pdo->lastInsertId();
    } catch (PDOException $e) {
        // Manejar errores
        echo json_encode([
            "successBD" => false,
            "errorBD" => "Error al insertar el producto: " . $e->getMessage()
        ]);
        return false;
    }
}



public function updateProducte($id, $catid, $name, $pvp, $img_url, $descripcion, $codref) {
    try {
        // Preparar la consulta SQL para actualizar un producto, incluyendo descripcion y codref
        $stmt = $this->pdo->prepare('
            UPDATE productos 
            SET catid = :catid, name = :name, pvp = :pvp, img_url = :img_url, descripcion = :descripcion, codref = :codref
            WHERE id = :id
        ');

        // Asignar valores a los parámetros
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':catid', $catid, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':pvp', $pvp, PDO::PARAM_STR);
        $stmt->bindParam(':img_url', $img_url, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':codref', $codref, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Retornar verdadero si se actualizó al menos una fila, de lo contrario retorna falso
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        // Manejar errores
        return false;
    }
}


public function deleteProducto($id)
{
    try {
        // Preparar la consulta SQL
        $stmt = $this->pdo->prepare('DELETE FROM productos WHERE id = :id');

        // Vincular el parámetro :id
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Retornar true si se ejecuta correctamente
        return true;
    } catch (PDOException $e) {
        // Manejo de errores
        return false;
    }
}


public function getProductesFullFiltered($textNameSearch, $categorySelected, $min, $max)
{   
    try {
        // Construir la consulta SQL dinámica
        $query = 'SELECT * FROM productos WHERE 1=1'; // Siempre cierto
        $params = [];

        // Si se especifica un nombre, añadir filtro por nombre (case-insensitive)
        if (!empty($textNameSearch)) {
            $query .= ' AND LOWER(name) LIKE LOWER(:textNameSearch)';
            $params[':textNameSearch'] = '%' . $textNameSearch . '%';
        }

        // Si se especifica una categoría distinta de 0, filtrar por categoría
        if ($categorySelected != 0) {
            $query .= ' AND catid = :categorySelected';
            $params[':categorySelected'] = $categorySelected;
        }

        // Si se especifica un precio mínimo, añadir filtro
        if (!is_null($min)) {
            $query .= ' AND pvp >= :min';
            $params[':min'] = $min;
        }

        // Si se especifica un precio máximo, añadir filtro
        if (!is_null($max)) {
            $query .= ' AND pvp <= :max';
            $params[':max'] = $max;
        }

        // Preparar y ejecutar la consulta
        $stmt = $this->pdo->prepare($query);

        // Vincular parámetros dinámicos
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        // Retornar los resultados
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Manejar errores y registrar
        error_log('Error al obtener los productos: ' . $e->getMessage());
        return false;
    }
}



public function getAllCodrefs() {
    try {
        // Seleccionar únicamente el campo 'codref' de la tabla 'productos'
        $stmt = $this->pdo->query('SELECT codref FROM productos');
        
        // Obtener los resultados como un arreglo asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {

        return false;
    }
}







}