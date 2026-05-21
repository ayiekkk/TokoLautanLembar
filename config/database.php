<?php

class Database {
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try{
            $this->conn = new mysqli("localhost", "ojokerro_tokolautanlembar", 'LautanLembar123', "ojokerro_lautanlembardb");

            if($this->conn->connect_error) {
                die("conn failed: " . $this->conn->connect_error);
            }

            $this->conn->set_charset("utf8");
            return $this->conn;
        } catch (Exception $e) {
            echo "conn failed : " . $e->getMessage();
        }
    }
}