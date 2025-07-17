<?php
$page_title = 'Data Warga';
require_once '../includes/header.php';

// Require operator access
requireOperator();

// Handle search and filter
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$kategori = isset($_GET['kategori']) ? sanitize($_GET['kategori']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$warga_data = getWarga($page, 10, $search, $kategori);
$kategori_list = getKategoriList();

// Handle delete
if (isset($_GET['delete']) && isAdmin()) {
    $id = (int)$_GET['delete'];
    if (deleteWarga($id)) {
        $success = 'Data warga berhasil dihapus!';
    } else {
        $error = 'Gagal menghapus data warga!';
    }
    // Redirect to avoid resubmission
    header('Location: index.php');
    exit();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Data Warga</h2>
    <a href="add.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Tambah Warga
    </a>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-5">
                <label for="search" class="form-label">Cari berdasarkan nama:</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="<?php echo htmlspecialchars($search); ?>" placeholder="Masukkan nama warga...">
            </div>
            <div class="col-md-3">
                <label for="kategori" class="form-label">Filter kategori:</label>
                <select class="form-select" id="kategori" name="kategori">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($kategori_list as $kat): ?>
                        <option value="<?php echo htmlspecialchars($kat); ?>" 
                                <?php echo $kategori === $kat ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($kat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <a href="index.php" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Daftar Warga</h5>
        <small class="text-muted">
            Menampilkan <?php echo count($warga_data['data']); ?> dari <?php echo $warga_data['total']; ?> data
        </small>
    </div>
    <div class="card-body">
        <?php if (empty($warga_data['data'])): ?>
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <p class="text-muted">Tidak ada data yang ditemukan.</p>
                <a href="add.php" class="btn btn-primary">Tambah Warga Pertama</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>Alamat</th>
                            <th>Kategori</th>
                            <th>Status Bantuan</th>
                            <th>Tanggal Bantuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = ($warga_data['current_page'] - 1) * 10 + 1;
                        foreach ($warga_data['data'] as $warga): 
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($warga['nama']); ?></td>
                                <td><?php echo htmlspecialchars($warga['nik']); ?></td>
                                <td><?php echo htmlspecialchars($warga['alamat']); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $warga['kategori'] === 'Fakir Miskin' ? 'danger' : 
                                             ($warga['kategori'] === 'Miskin' ? 'warning' : 'secondary'); 
                                    ?>">
                                        <?php echo htmlspecialchars($warga['kategori']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $warga['status_bantuan'] === 'Sudah' ? 'success' : 'secondary'; ?>">
                                        <?php echo $warga['status_bantuan']; ?>
                                    </span>
                                </td>
                                <td><?php echo formatDate($warga['tanggal_bantuan']); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="edit.php?id=<?php echo $warga['id']; ?>" 
                                           class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if (isAdmin()): ?>
                                            <a href="index.php?delete=<?php echo $warga['id']; ?>" 
                                               class="btn btn-outline-danger" title="Hapus"
                                               onclick="return confirm('Yakin ingin menghapus data <?php echo htmlspecialchars($warga['nama']); ?>?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($warga_data['pages'] > 1): ?>
                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination justify-content-center">
                        <?php if ($warga_data['current_page'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $warga_data['current_page'] - 1; ?>&search=<?php echo urlencode($search); ?>&kategori=<?php echo urlencode($kategori); ?>">Previous</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $warga_data['pages']; $i++): ?>
                            <li class="page-item <?php echo $i === $warga_data['current_page'] ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&kategori=<?php echo urlencode($kategori); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($warga_data['current_page'] < $warga_data['pages']): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $warga_data['current_page'] + 1; ?>&search=<?php echo urlencode($search); ?>&kategori=<?php echo urlencode($kategori); ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
