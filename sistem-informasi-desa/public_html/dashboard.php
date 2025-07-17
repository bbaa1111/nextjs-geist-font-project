<?php
$page_title = 'Dashboard';
require_once 'includes/header.php';

// Require login
requireLogin();

$stats = getDashboardStats();
$user = getCurrentUser();
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Dashboard</h2>
                <p class="text-muted mb-0">Selamat datang, <?php echo htmlspecialchars($user['nama']); ?>!</p>
            </div>
            <div>
                <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'primary' : 'success'; ?> fs-6">
                    <?php echo ucfirst($user['role']); ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
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
    
    <div class="col-lg-3 col-md-6 mb-3">
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
    
    <div class="col-lg-3 col-md-6 mb-3">
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
    
    <div class="col-lg-3 col-md-6 mb-3">
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

<!-- Status Bantuan Chart -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Status Bantuan</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-success"><?php echo number_format($stats['sudah_bantuan']); ?></h4>
                            <p class="text-muted mb-0">Sudah Dapat</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-secondary"><?php echo number_format($stats['belum_bantuan']); ?></h4>
                        <p class="text-muted mb-0">Belum Dapat</p>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 10px;">
                    <?php 
                    $total_bantuan = $stats['sudah_bantuan'] + $stats['belum_bantuan'];
                    $percentage = $total_bantuan > 0 ? ($stats['sudah_bantuan'] / $total_bantuan) * 100 : 0;
                    ?>
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: <?php echo $percentage; ?>%" 
                         aria-valuenow="<?php echo $percentage; ?>" 
                         aria-valuemin="0" aria-valuemax="100">
                        <?php echo number_format($percentage, 1); ?>%
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Kategori Penduduk</h5>
            </div>
            <div class="card-body">
                <?php
                $kategori_stats = [
                    'Miskin' => $stats['penduduk_miskin'],
                    'Fakir Miskin' => $stats['fakir_miskin'],
                    'Lainnya' => $stats['total_penduduk'] - $stats['penduduk_miskin'] - $stats['fakir_miskin']
                ];
                ?>
                <?php foreach ($kategori_stats as $kategori => $jumlah): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><?php echo $kategori; ?></span>
                        <span class="badge bg-<?php 
                            echo $kategori === 'Fakir Miskin' ? 'danger' : 
                                 ($kategori === 'Miskin' ? 'warning' : 'secondary'); 
                        ?>">
                            <?php echo number_format($jumlah); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="warga/" class="btn btn-outline-primary w-100">
                            <i class="fas fa-users me-2"></i>
                            Kelola Data Warga
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="warga/add.php" class="btn btn-outline-success w-100">
                            <i class="fas fa-user-plus me-2"></i>
                            Tambah Warga Baru
                        </a>
                    </div>
                    <?php if (isAdmin()): ?>
                        <div class="col-md-3 mb-3">
                            <a href="admin/operators.php" class="btn btn-outline-info w-100">
                                <i class="fas fa-user-cog me-2"></i>
                                Kelola Operator
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="admin/import.php" class="btn btn-outline-warning w-100">
                                <i class="fas fa-file-excel me-2"></i>
                                Import Excel
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
