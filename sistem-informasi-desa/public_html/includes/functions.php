<?php
require_once 'config.php';

// Get dashboard statistics
function getDashboardStats() {
    $pdo = getConnection();
    
    $stats = [];
    
    // Total penduduk
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM warga");
    $stats['total_penduduk'] = $stmt->fetch()['total'];
    
    // Penduduk miskin
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM warga WHERE kategori = 'Miskin'");
    $stats['penduduk_miskin'] = $stmt->fetch()['total'];
    
    // Fakir miskin
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM warga WHERE kategori = 'Fakir Miskin'");
    $stats['fakir_miskin'] = $stmt->fetch()['total'];
    
    // Sudah dapat bantuan
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM warga WHERE status_bantuan = 'Sudah'");
    $stats['sudah_bantuan'] = $stmt->fetch()['total'];
    
    // Belum dapat bantuan
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM warga WHERE status_bantuan = 'Belum'");
    $stats['belum_bantuan'] = $stmt->fetch()['total'];
    
    return $stats;
}

// Get all warga with pagination and search
function getWarga($page = 1, $limit = 10, $search = '', $kategori = '') {
    $pdo = getConnection();
    $offset = ($page - 1) * $limit;
    
    $where = "WHERE 1=1";
    $params = [];
    
    if (!empty($search)) {
        $where .= " AND nama LIKE ?";
        $params[] = "%$search%";
    }
    
    if (!empty($kategori)) {
        $where .= " AND kategori = ?";
        $params[] = $kategori;
    }
    
    // Get total count
    $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM warga $where");
    $countStmt->execute($params);
    $total = $countStmt->fetch()['total'];
    
    // Get data
    $stmt = $pdo->prepare("SELECT * FROM warga $where ORDER BY nama ASC LIMIT $limit OFFSET $offset");
    $stmt->execute($params);
    $data = $stmt->fetchAll();
    
    return [
        'data' => $data,
        'total' => $total,
        'pages' => ceil($total / $limit),
        'current_page' => $page
    ];
}

// Get single warga by ID
function getWargaById($id) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM warga WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Save warga (insert or update)
function saveWarga($data, $id = null) {
    $pdo = getConnection();
    
    if ($id) {
        // Update
        $stmt = $pdo->prepare("UPDATE warga SET nama = ?, nik = ?, alamat = ?, kategori = ?, status_bantuan = ?, tanggal_bantuan = ? WHERE id = ?");
        return $stmt->execute([
            $data['nama'],
            $data['nik'],
            $data['alamat'],
            $data['kategori'],
            $data['status_bantuan'],
            $data['tanggal_bantuan'] ?: null,
            $id
        ]);
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO warga (nama, nik, alamat, kategori, status_bantuan, tanggal_bantuan) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['nama'],
            $data['nik'],
            $data['alamat'],
            $data['kategori'],
            $data['status_bantuan'],
            $data['tanggal_bantuan'] ?: null
        ]);
    }
}

// Delete warga
function deleteWarga($id) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("DELETE FROM warga WHERE id = ?");
    return $stmt->execute([$id]);
}

// Get all operators
function getOperators() {
    $pdo = getConnection();
    $stmt = $pdo->query("SELECT id, email, nama, role, created_at FROM admin ORDER BY nama ASC");
    return $stmt->fetchAll();
}

// Get operator by ID
function getOperatorById($id) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT id, email, nama, role FROM admin WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Save operator (insert or update)
function saveOperator($data, $id = null) {
    $pdo = getConnection();
    
    if ($id) {
        // Update
        if (!empty($data['password'])) {
            $stmt = $pdo->prepare("UPDATE admin SET email = ?, nama = ?, role = ?, password = ? WHERE id = ?");
            return $stmt->execute([
                $data['email'],
                $data['nama'],
                $data['role'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $id
            ]);
        } else {
            $stmt = $pdo->prepare("UPDATE admin SET email = ?, nama = ?, role = ? WHERE id = ?");
            return $stmt->execute([
                $data['email'],
                $data['nama'],
                $data['role'],
                $id
            ]);
        }
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO admin (email, nama, role, password) VALUES (?, ?, ?, ?)");
        return $stmt->execute([
            $data['email'],
            $data['nama'],
            $data['role'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
    }
}

// Delete operator
function deleteOperator($id) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("DELETE FROM admin WHERE id = ?");
    return $stmt->execute([$id]);
}

// Get unique categories
function getKategoriList() {
    $pdo = getConnection();
    $stmt = $pdo->query("SELECT DISTINCT kategori FROM warga ORDER BY kategori ASC");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Format date
function formatDate($date) {
    if (!$date) return '-';
    return date('d/m/Y', strtotime($date));
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
?>
