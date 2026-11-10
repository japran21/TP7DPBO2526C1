<?php
class Story {
    private $conn;
    private $table_name = "stories";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT s.id, s.title, s.content, s.status, s.user_id, s.counselor_id, 
                         u.nickname as user_nickname, 
                         c.name as counselor_name 
                  FROM " . $this->table_name . " s 
                  JOIN users u ON s.user_id = u.id 
                  JOIN counselors c ON s.counselor_id = c.id 
                  ORDER BY s.id DESC";
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

    public function create($title, $content, $user_id, $counselor_id) {
        $query = "INSERT INTO " . $this->table_name . " (title, content, user_id, counselor_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$title, $content, $user_id, $counselor_id]);
    }

    public function update($id, $title, $content, $user_id, $counselor_id, $status) {
        $query = "UPDATE " . $this->table_name . " SET title = ?, content = ?, user_id = ?, counselor_id = ?, status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$title, $content, $user_id, $counselor_id, $status, $id]);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}