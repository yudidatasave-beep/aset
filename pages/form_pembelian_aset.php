<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once dirname(__DIR__) . '/includes/db.php';
// Ambil daftar supplier
$suppliers = $conn->query("SELECT id_supplier, nama_supplier FROM supplier ORDER BY nama_supplier ASC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Pembelian Aset</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        body {
            background-color: #f0f4f7;
        }
        .navbar {
            background-color: #003366;
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .card {
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        th {
            background-color: #003366;
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar px-4">
        <a class="navbar-brand" href="#">Sistem Manajemen Aset RS</a>
    </nav>

    <div class="container mt-4">
        <div class="card p-4">
            <h4 class="mb-4 text-primary">Form Pembelian Aset</h4>

            <form action="pages/simpan_pembelian.php" method="POST">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="nomor_faktur" class="form-label">Nomor Faktur</label>
                        <input type="text" name="nomor_faktur" id="nomor_faktur" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label for="tanggal_pembelian" class="form-label">Tanggal Pembelian</label>
                        <input type="date" name="tanggal_pembelian" id="tanggal_pembelian" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label for="tanggal_jatuh_tempo" class="form-label">Tanggal Jatuh Tempo</label>
                        <input type="date" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="id_supplier" class="form-label">Supplier</label>
                    <select name="id_supplier" id="id_supplier" class="form-select" required>
                        <option value="">-- Pilih Supplier --</option>
                        <?php foreach ($suppliers as $s): ?>
                            <option value="<?= $s['id_supplier'] ?>"><?= htmlspecialchars($s['nama_supplier']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <h5 class="mt-4">Detail Pembelian</h5>
                <table class="table table-bordered" id="detailPembelian">
                    <thead>
                        <tr>
                            <th>Nama Aset</th>
                            <th>Nomor Seri</th>
                            <th>Jumlah</th>
                            <th>Satuan</th>
                            <th>Harga Perolehan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="hidden" name="id_aset[]" class="id_aset">
                                <input type="text" name="nama_aset[]" class="form-control nama_aset" placeholder="Ketik kode/nama aset...">
                            </td>
                            <td><input type="text" name="nomor_seri[]" class="form-control" required></td>
                            <td><input type="number" name="jumlah[]" class="form-control" required></td>
                            <td><input type="text" name="satuan[]" class="form-control"></td>
                            <td><input type="number" name="harga_perolehan[]" class="form-control" required></td>
                            <td><button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" id="addRow" class="btn btn-secondary btn-sm">+ Tambah Baris</button>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Pembelian</button>
                    <a href="index.php?page=pembelian_aset" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
    function setAutocomplete() {
        $('.nama_aset').autocomplete({
            source: 'pages/search_aset.php',
            minLength: 2,
            select: function (event, ui) {
                $(this).val(ui.item.label);
                $(this).closest('td').find('.id_aset').val(ui.item.value);
                return false;
            }
        });
    }

    $(document).ready(function () {
        setAutocomplete();

        $('#addRow').click(function () {
            let newRow = `<tr>
                <td>
                    <input type="hidden" name="id_aset[]" class="id_aset">
                    <input type="text" name="nama_aset[]" class="form-control nama_aset" placeholder="Ketik nama aset/kode aset...">
                </td>
                <td><input type="text" name="nomor_seri[]" class="form-control" required></td>
                <td><input type="number" name="jumlah[]" class="form-control" required></td>
                <td><input type="text" name="satuan[]" class="form-control"></td>
                <td><input type="number" name="harga_perolehan[]" class="form-control" required></td>
                <td><button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button></td>
            </tr>`;
            $('#detailPembelian tbody').append(newRow);
            setAutocomplete();
        });

        $(document).on('click', '.removeRow', function () {
            $(this).closest('tr').remove();
        });
    });
    </script>
</body>
</html>
