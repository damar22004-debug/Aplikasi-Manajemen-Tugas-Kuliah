<?php
// api.php - REST API for assignments
require_once 'config.php';

header('Content-Type: application/json');

requireLogin();

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'list':
            if ($method === 'GET') {
                // Get all assignments for current user
                $filter = sanitize($_GET['filter'] ?? 'all');
                $sort = sanitize($_GET['sort'] ?? 'deadline');
                
                $sql = "SELECT * FROM assignments WHERE user_id = ?";
                
                if ($filter !== 'all') {
                    $sql .= " AND status = ?";
                }
                
                // Add sorting
                switch ($sort) {
                    case 'priority':
                        $sql .= " ORDER BY FIELD(priority, 'Tinggi', 'Sedang', 'Rendah'), deadline ASC";
                        break;
                    case 'recent':
                        $sql .= " ORDER BY created_at DESC";
                        break;
                    default:
                        $sql .= " ORDER BY deadline ASC";
                }
                
                $stmt = $conn->prepare($sql);
                
                if ($filter !== 'all') {
                    $stmt->bind_param("is", $user_id, $filter);
                } else {
                    $stmt->bind_param("i", $user_id);
                }
                
                $stmt->execute();
                $result = $stmt->get_result();
                $assignments = [];
                
                while ($row = $result->fetch_assoc()) {
                    $assignments[] = $row;
                }
                
                echo json_encode(['success' => true, 'data' => $assignments]);
            }
            break;
            
        case 'create':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                
                $name = sanitize($data['name'] ?? '');
                $course = sanitize($data['course'] ?? '');
                $description = sanitize($data['description'] ?? '');
                $deadline = sanitize($data['deadline'] ?? '');
                $priority = sanitize($data['priority'] ?? 'Sedang');
                $status = sanitize($data['status'] ?? 'Belum Mulai');
                
                if (empty($name) || empty($course) || empty($deadline)) {
                    throw new Exception('Data tidak lengkap');
                }
                
                $stmt = $conn->prepare("INSERT INTO assignments (user_id, name, course, description, deadline, priority, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issssss", $user_id, $name, $course, $description, $deadline, $priority, $status);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'id' => $conn->insert_id, 'message' => 'Tugas berhasil ditambahkan']);
                } else {
                    throw new Exception('Gagal menambahkan tugas');
                }
            }
            break;
            
        case 'update':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                
                $id = intval($data['id'] ?? 0);
                $name = sanitize($data['name'] ?? '');
                $course = sanitize($data['course'] ?? '');
                $description = sanitize($data['description'] ?? '');
                $deadline = sanitize($data['deadline'] ?? '');
                $priority = sanitize($data['priority'] ?? 'Sedang');
                $status = sanitize($data['status'] ?? 'Belum Mulai');
                
                if ($id <= 0 || empty($name) || empty($course) || empty($deadline)) {
                    throw new Exception('Data tidak lengkap');
                }
                
                $stmt = $conn->prepare("UPDATE assignments SET name = ?, course = ?, description = ?, deadline = ?, priority = ?, status = ? WHERE id = ? AND user_id = ?");
                $stmt->bind_param("ssssssii", $name, $course, $description, $deadline, $priority, $status, $id, $user_id);
                
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        echo json_encode(['success' => true, 'message' => 'Tugas berhasil diupdate']);
                    } else {
                        throw new Exception('Tugas tidak ditemukan');
                    }
                } else {
                    throw new Exception('Gagal mengupdate tugas');
                }
            }
            break;
            
        case 'delete':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $id = intval($data['id'] ?? 0);
                
                if ($id <= 0) {
                    throw new Exception('ID tidak valid');
                }
                
                $stmt = $conn->prepare("DELETE FROM assignments WHERE id = ? AND user_id = ?");
                $stmt->bind_param("ii", $id, $user_id);
                
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        echo json_encode(['success' => true, 'message' => 'Tugas berhasil dihapus']);
                    } else {
                        throw new Exception('Tugas tidak ditemukan');
                    }
                } else {
                    throw new Exception('Gagal menghapus tugas');
                }
            }
            break;
            
        case 'stats':
            if ($method === 'GET') {
                $stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM assignments WHERE user_id = ? GROUP BY status");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                $stats = [
                    'total' => 0,
                    'Belum Mulai' => 0,
                    'Sedang Dikerjakan' => 0,
                    'Selesai' => 0,
                    'Terlambat' => 0
                ];
                
                while ($row = $result->fetch_assoc()) {
                    $stats[$row['status']] = intval($row['count']);
                    $stats['total'] += intval($row['count']);
                }
                
                echo json_encode(['success' => true, 'data' => $stats]);
            }
            break;
            
        case 'update_overdue':
            if ($method === 'POST') {
                // Update overdue assignments
                $stmt = $conn->prepare("UPDATE assignments SET status = 'Terlambat' WHERE user_id = ? AND deadline < NOW() AND status != 'Selesai' AND status != 'Terlambat'");
                $stmt->bind_param("i", $user_id);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'updated' => $stmt->affected_rows]);
                } else {
                    throw new Exception('Gagal mengupdate status');
                }
            }
            break;
            
        default:
            throw new Exception('Action tidak valid');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>