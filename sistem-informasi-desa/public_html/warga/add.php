<?php
$page_title = 'Tambah Warga';
require_once '../includes/header.php';

// Require operator access
requireOperator();

$error = '';
$success = '';

if ($_POST) {
    $nama = sanitize($_POST['nama']);
    $nik = sanitize($_POST['nik']);
    $alamat = sanitize($_POST['alamat']);
    $kategori = sanitize($_POST['kategori']);
    $status_bantuan = sanitize($_POST['status_bantuan']);
    $tanggal_bantuan = sanitize($_POST['tanggal_bantuan']);
    
    // Validation
    if (empty($nama) || empty($nik) || empty($alamat) || empty($kategori)) {
        $error = 'Semua field wajib diisi!';
    } elseif (strlen($nik) !== 16) {
        $error = 'NIK harus 16 digit!';
    } elseif (!is_numeric($nik)) {
        $error = 'NIK harus berupa angka!';
    } else {
        // Check if NIK already exists
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT id FROM warga WHERE nik = ?");
        $stmt->execute([$nik]);
        if ($stmt->fetch()) {
            $error = 'NIK sudah terdaftar!';
        } else {
            $data = [
                'nama' => $nama,
                'nik' => $nik,
                'alamat' => $alamat,
                'kategori' => $kategori,
                'status_bantuan' => $status_bantuan,
                'tanggal_bantuan' => $tanggal_bantuan
            ];
            
            if (saveWarga($data)) {
                $success = 'Data warga berhasil ditambahkan!';
                // Clear form
                $_POST = [];
            } else {
                $error = 'Gagal menambahkan data warga!';
            }
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Tambah Warga</h2>
    <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Form Tambah Warga</h5>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama" name="nama" 
                               value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>" 
                               required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nik" class="form-label">NIK (16 digit) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nik" name="nik" 
                               value="<?php echo isset($_POST['nik']) ? htmlspecialchars($_POST['nik']) : ''; ?>" 
                               maxlength="16" pattern="[0-9]{16}" required>
                        <div class="form-text">Masukkan 16 digit NIK</div>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?php echo isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : ''; ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select" id="kategori" name="kategori" required>
                            <option value="">Pilih Kategori</option>
                            <option value="Mampu" <?php echo (isset($_POST['kategori']) && $_POST['kategori'] === 'Mampu') ? 'selected' : ''; ?>>Mampu</option>
                            <option value="Miskin" <?php echo (isset($_POST['kategori']) && $_POST['kategori'] === 'Miskin') ? 'selected' : ''; ?>>Miskin</option>
                            <option value="Fakir Miskin" <?php echo (isset($_POST['kategori']) && $_POST['kategori'] === 'Fakir Miskin') ? 'selected' : ''; ?>>Fakir Miskin</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status_bantuan" class="form-label">Status Bantuan</label>
                        <select class="form-select" id="status_bantuan" name="status_bantuan">
                            <option value="Belum" <?php echo (!isset($_POST['status_bantuan']) || $_POST['status_bantuan'] === 'Belum') ? 'selected' : ''; ?>>Belum</option>
                            <option value="Sudah" <?php echo (isset($_POST['status_bantuan']) && $_POST['status_bantuan'] === 'Sudah') ? 'selected' : ''; ?>>Sudah</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="tanggal_bantuan" class="form-label">Tanggal Bantuan</label>
                <input type="date" class="form-control" id="tanggal_bantuan" name="tanggal_bantuan" 
                       value="<?php echo isset($_POST['tanggal_bantuan']) ? htmlspecialchars($_POST['tanggal_bantuan']) : ''; ?>">
                <div class="form-text">Kosongkan jika belum mendapat bantuan</div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-enable tanggal_bantuan when status is "Sudah"
document.getElementById('status_bantuan').addEventListener('change', function() {
    const tanggalBantuan = document.getElementById('tanggal_bantuan');
    if (this.value === 'Sudah') {
        tanggalBantuan.required = true;
        if (!tanggalBantuan.value) {
            tanggalBantuan.value = new Date().toISOString().split('T')[0];
        }
    } else {
        tanggalBantuan.required = false;
        tanggalBantuan.value = '';
    }
});

// NIK validation
document.getElementById('nik').addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
    if (this.value.length > 16) {
        this.value = this.value.slice(0, 16);
    }
});
</script>

<?php include '../includes/footer.php'; ?>
