-- Database: sistem_informasi_desa
CREATE DATABASE IF NOT EXISTS sistem_informasi_desa;
USE sistem_informasi_desa;

-- Tabel admin (untuk admin dan operator)
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'operator') NOT NULL DEFAULT 'operator',
    nama VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel warga
CREATE TABLE warga (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nik VARCHAR(20) NOT NULL UNIQUE,
    alamat TEXT NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    status_bantuan ENUM('Sudah', 'Belum') NOT NULL DEFAULT 'Belum',
    tanggal_bantuan DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin
INSERT INTO admin (email, password, role, nama) VALUES 
('admin@desa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator');
-- Password default: password

-- Insert sample data warga
INSERT INTO warga (nama, nik, alamat, kategori, status_bantuan, tanggal_bantuan) VALUES
('Ahmad Suryanto', '3201010101010001', 'Jl. Merdeka No. 1, RT 01/RW 01', 'Miskin', 'Sudah', '2024-01-15'),
('Siti Nurhaliza', '3201010101010002', 'Jl. Kemerdekaan No. 5, RT 02/RW 01', 'Fakir Miskin', 'Belum', NULL),
('Budi Santoso', '3201010101010003', 'Jl. Pancasila No. 10, RT 03/RW 02', 'Miskin', 'Sudah', '2024-02-20'),
('Dewi Sartika', '3201010101010004', 'Jl. Diponegoro No. 15, RT 01/RW 03', 'Mampu', 'Belum', NULL),
('Joko Widodo', '3201010101010005', 'Jl. Sudirman No. 20, RT 02/RW 03', 'Fakir Miskin', 'Sudah', '2024-03-10');
