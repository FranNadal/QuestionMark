<?php

class Database
{

    private $conn;

    function __construct($servername, $username, $dbname, $password)
    {
        $this->conn = new Mysqli($servername, $username, $password, $dbname) or die("Error de conexion " . mysqli_connect_error());
    }

    public function query(string $sql, array $params = []) {
        $stmt = $this->prepareAndBind($sql, $params);
        $stmt->execute();
        return $stmt->get_result(); // Devolver resultado crudo para uso externo
    }

    public function fetchOne(string $sql, array $params = []) {
        $result = $this->query($sql, $params);
        return $result->fetch_assoc();
    }

    public function fetchAll(string $sql, array $params = []) {
        $result = $this->query($sql, $params);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function execute(string $sql, array $params = []) {
        $stmt = $this->prepareAndBind($sql, $params);
        return $stmt->execute();
    }

    private function prepareAndBind(string $sql, array $params) {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Error en prepare: " . $this->conn->error);
        }

        if (!empty($params)) {
            $types = $this->getParamTypes($params);
            $stmt->bind_param($types, ...$params);
        }

        return $stmt;
    }

    private function getParamTypes(array $params): string {
        return implode('', array_map(function ($param) {
            return match (gettype($param)) {
                'integer' => 'i',
                'double'  => 'd',
                'string'  => 's',
                default   => 's', // predeterminado como string
            };
        }, $params));
    }





    function __destruct()
    {
        $this->conn->close();
    }
}