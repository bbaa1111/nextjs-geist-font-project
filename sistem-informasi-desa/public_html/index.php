<?php
$page_title = 'Beranda';
require_once 'includes/header.php';

// Get statistics for public display
$stats = getDashboardStats();
$kategori_list = getKategoriList();

// Handle search and filter
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$kategori = isset($_GET['kategori']) ? sanitize($_GET['kategori']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$warga_data = getWarga($page, 10, $search, $kategori);
?>

<div class="row">
    <div class="col-12">
        <div class="jumbotron bg-primary text-white p-5 rounded mb-4">
            <div class="container">
                <h1 class="display-4">Sistem Informasi Desa</h1>
                <p class="lead">Portal informasi data kependudukan dan bantuan sosial desa</p>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stats-card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="card-title"><?php echo number_format($stats['total_penduduk']); ?></h3>
                        <p class="card-text">Total Penduduk</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="card-title"><?php echo number_format($stats['penduduk_miskin']); ?></h3>
                        <p class="card-text">Penduduk Miskin</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="card-title"><?php echo number_format($stats['fakir_miskin']); ?></h3>
                        <p class="card-text">Fakir Miskin</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-heart-broken fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="card-title"><?php echo number_format($stats['sudah_bantuan']); ?></h3>
                        <p class="card-text">Sudah Dapat Bantuan</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Pencarian Data Warga</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <label for="search" class="form-label">Cari berdasarkan nama:</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="<?php echo htmlspecialchars($search); ?>" placeholder="Masukkan nama warga...">
            </div>
            <div class="col-md-4">
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
        </form>
    </div>
</div>

<!-- Data Warga Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Data Warga</h5>
        <small class="text-muted">
            Menampilkan <?php echo count($warga_data['data']); ?> dari <?php echo $warga_data['total']; ?> data
        </small>
    </div>
    <div class="card-body">
        <?php if (empty($warga_data['data'])): ?>
            <div class="text-center py-4">
                <p class="text-muted">Tidak ada data yang ditemukan.</p>
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
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($warga_data['pages'] > 1): ?>
                <nav aria-label="Page navigation">
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

<?php include 'includes/footer.php'; ?>
