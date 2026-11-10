<?php
class User {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($nickname, $email, $age) {
        $query = "INSERT INTO " . $this->table_name . " (nickname, email, age) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$nickname, $email, $age]);
    }

    public function update($id, $nickname, $email, $age) {
        $query = "UPDATE " . $this->table_name . " SET nickname = ?, email = ?, age = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$nickname, $email, $age, $id]);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}