<?php

class bdCategorias
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

    public function getCategorias()
    {
            $stmt = $this->pdo->query('SELECT * FROM categorias');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function insertCategoria($name)
    {
        try {
            $stmt = $this->pdo->prepare('INSERT INTO categorias (name) VALUES (:name)');
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->execute();

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }


    public function getCategoryByID($id)
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM categorias WHERE id = :id');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateCategoria($id, $name)
    {
        try {
            $stmt = $this->pdo->prepare('UPDATE categorias SET name = :name WHERE id = :id');

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);

            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }


        public function deleteCategoria($id)
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM categorias WHERE id = :id');

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }


}