<?php

class bdPaginaInicio
{
    private $pdo; 

    public function __construct()
    {
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
    public function getSaludos()
    {
            $stmt = $this->pdo->query('SELECT * FROM paginaInicio');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}