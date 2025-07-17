<?php
$page_title = 'Kelola Operator';
require_once '../includes/header.php';

// Require admin access
requireAdmin();

$error = '';
$success = '';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id !== getCurrentUser()['id']) {
        if (deleteOperator($id)) {
            $success = 'Operator berhasil dihapus!';
        } else {
            $error = 'Gagal menghapus operator!';
        }
    } else {
        $error = 'Tidak dapat menghapus akun sendiri!';
    }
    header('Location: operators.php');
    exit();
}

$operators = getOperators();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Kelola Operator</h2>
    <a href="add-operator.php" class="btn btn-primary">Tambah Operator</a>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5>Daftar Operator</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    foreach ($operators as $operator): 
                    ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($operator['nama']); ?></td>
                            <td><?php echo htmlspecialchars($operator['email']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $operator['role'] === 'admin' ? 'primary' : 'success'; ?>">
                                    <?php echo ucfirst($operator['role']); ?>
                                </span>
                            </td>
                            <td><?php echo formatDate($operator['created_at']); ?></td>
                            <td>
                                <a href="edit-operator.php?id=<?php echo $operator['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <?php if ($operator['id'] !== getCurrentUser()['id']): ?>
                                    <a href="operators.php?delete=<?php echo $operator['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Yakin ingin menghapus operator <?php echo htmlspecialchars($operator['nama']); ?>?')">Hapus</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
