<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sistem Ojol</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fcfcfe;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px;
            margin: 0;
            color: #070606;
        }

        form {
            background: #fefdfd;
            padding: 18px 22px;
            border-radius: 8px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
            width: 100%;
            max-width: 520px;
            box-sizing: border-box;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 12px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #3b0b0b;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
        }

        button {
            background-color: #5905f5cc;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <h1>Sistem Ojol</h1>

    <form method="POST" action="">
        <div class="form-group">
            <label>Nama Pelanggan</label>
            <input type="text" name="nama" required placeholder="Nama lengkap">
        </div>

        <div class="form-group">
            <label>No Hp</label>
            <input type="text" name="noHp" required placeholder="08xx">
        </div>

        <div class="form-group">
            <label>Jarak Tempuh (KM)</label>
            <input type="number" name="jarakTempuh" required min="1" step="1" placeholder="Harus lebih dari 0">
        </div>

        <div class="form-group">
            <label>Jenis Layanan</label><br>
            <select name="layanan">
                <option value="GoRide Reguler">GoRide Reguler</option>
                <option value="GoRide Prioritas">GoRide Prioritas</option>
                <option value="GoCar">GoCar</option>
                <option value="GoCar XL">GoCar XL</option>
                <option value="GoFood">GoFood</option>
            </select>
        </div>

        <div class="form-group">
            <label>Kode Voucher</label>
            <input type="text" name="kodeVoucher" placeholder="(opsional) HEMAT10">
        </div>

        <button type="submit" name="submit">Pesan</button>
    </form>

    <?php

    class User {
        public $nama;
        private $noHp;

        public function __construct($nama, $noHp)
        {
            $this->nama = $nama;
            $this->noHp = $noHp;
        }
        public function getNama()
        {
            return $this->nama;
        }
        public function getStatus()
        {
            return "Member";
        }

        public function setNoHp($noHp)
        {
            $this->noHp = $noHp;
        }

        public function getNoHp()
        {
            $nomor = preg_replace('/\D/', '', $this->noHp);

            if (strlen($nomor) < 10) {
                return "No HP tidak boleh kurang dari 10";
            }

            return $this->noHp;
        }
    }

    class Pelanggan extends User {
        public $poin;

        public function __construct($nama, $noHp, $poin = 0)
        {
            parent::__construct($nama, $noHp);
            $this->poin = $poin;
        }

        public function getStatus()
        {
            return parent::getStatus();
        }

        public function tambahPoint()
        {
            $this->poin += 10;
        }
    }

    class Layanan {
        public $jenisLayanan;

        public function __construct($jenisLayanan)
        {
            $this->jenisLayanan = $jenisLayanan;
        }

        public function getLayanan()
        {
            return $this->jenisLayanan;
        }

        public function getTarif()
        {
            switch ($this->jenisLayanan) {
                case "GoRide Reguler":
                    return 2500;
                case "GoRide Prioritas":
                    return 3000;
                case "GoCar":
                    return 4500;
                case "GoCar XL":
                    return 6000;
                case "GoFood":
                    return 2000;
                default:
                    return 0;
            }
        }
    }

    class Voucher {
        public $kodeVoucher;
        public $diskonPersen = 0;

        public function __construct($kodeVoucher)
        {
            $this->kodeVoucher = $kodeVoucher;
            $this->hitungDiskon();
        }

        public function hitungDiskon()
        {
            if ($this->kodeVoucher == "HEMAT10") {
                $this->diskonPersen = 0.1;
            } else if ($this->kodeVoucher == "HEMAT20") {
                $this->diskonPersen = 0.2;
            } else if ($this->kodeVoucher == "HEMAT30") {
                $this->diskonPersen = 0.3;
            } else {
                $this->diskonPersen = 0;
            }
        }
    }

    class Pembayaran {
        public function getMetode()
        {
            return "Transfer Bank";
        }
    }

    class Transaksi {
        public $pelanggan;
        public $layanan;
        public $pembayaran;
        public $voucher;
        public $jarakTempuh;

        public function __construct($pelanggan, $layanan, $pembayaran, $voucher, $jarakTempuh)
        {
            $this->pelanggan = $pelanggan;
            $this->layanan = $layanan;
            $this->pembayaran = $pembayaran;
            $this->voucher = $voucher;
            $this->jarakTempuh = $jarakTempuh;
        }
        public function hitungSubTotal()
        {
            return $this->layanan->getTarif() * $this->jarakTempuh;
        }

        public function hitungDiskonMember()
        {
            if ($this->hitungSubTotal() > 50000) {
                return 0.05 * $this->hitungSubTotal();
            } else {
                return 0;
            }
        }

        public function hitungBiayaAdmin()
        {
            return 2500;
        }

        public function hitungTotal()
        {
            $subTotal = $this->hitungSubTotal();
            $diskonMember = $this->hitungDiskonMember();
            $diskonVoucher = $this->voucher->diskonPersen * $subTotal;
            $biayaAdmin = $this->hitungBiayaAdmin();

            return $subTotal - $diskonMember - $diskonVoucher + $biayaAdmin;
        }
    }
    ?>

    <?php
    $transaksi_data = null;

    if (isset($_POST['submit'])) {
        $namaInput = trim($_POST['nama']);
        $noHpInput = trim($_POST['noHp']);
        $jarakInput = intval($_POST['jarakTempuh']);
        $layananInput = $_POST['layanan'];
        $voucherInput = strtoupper(trim($_POST['kodeVoucher']));

        $pelanggan = new Pelanggan($namaInput, $noHpInput);
        $layanan = new Layanan($layananInput);
        $pembayaran = new Pembayaran();
        $voucher = new Voucher($voucherInput);

        $transaksi = new Transaksi($pelanggan, $layanan, $pembayaran, $voucher, $jarakInput);

        $subTotal = $transaksi->hitungSubTotal();
        $diskonMember = $transaksi->hitungDiskonMember();
        $diskonVoucherAmount = $voucher->diskonPersen * $subTotal;
        $biayaAdmin = $transaksi->hitungBiayaAdmin();
        $total = $transaksi->hitungTotal();

        $transaksi_data = [
            'pelanggan' => $pelanggan,
            'layanan' => $layanan,
            'pembayaran' => $pembayaran,
            'voucher' => $voucher,
            'transaksi' => $transaksi,
            'subTotal' => $subTotal,
            'diskonMember' => $diskonMember,
            'diskonVoucherAmount' => $diskonVoucherAmount,
            'biayaAdmin' => $biayaAdmin,
            'total' => $total
        ]; 
    }
    ?>
    <?php
    if (isset($_POST['submit'])) {
        
        $namaInput = trim($_POST['nama']);
        $noHpInput = trim($_POST['noHp']);  
        $jarakInput = intval($_POST['jarakTempuh']);
        $layananInput = $_POST['layanan'];
        $voucherInput = strtoupper(trim($_POST['kodeVoucher']));

        $pelanggan = new Pelanggan($namaInput, $noHpInput);
        $layanan = new Layanan($layananInput);
        $pembayaran = new Pembayaran();
        $voucher = new Voucher($voucherInput);
        
        $transaksi = new Transaksi($pelanggan, $layanan, $pembayaran, $voucher, $jarakInput);

        $subTotal = $transaksi->hitungSubTotal();
        $diskonMember = $transaksi->hitungDiskonMember();
        $diskonVoucherAmount = $voucher->diskonPersen * $subTotal;
        $biayaAdmin = $transaksi->hitungBiayaAdmin();
        $total = $transaksi->hitungTotal();

        echo "<div class='output-box' style='background: #fff; padding: 18px 22px; border-radius: 8px; box-shadow: 0 6px 18px rgba(0,0,0,0.06); width: 100%; max-width: 520px; box-sizing: border-box; margin-top: 20px;'>";
        echo "<h3>Hasil Transaksi:</h3>";
        echo "<b>Nama Pelanggan:</b> " . htmlspecialchars($pelanggan->getNama()) . "<br>";
        echo "<b>No HP:</b> " . htmlspecialchars($pelanggan->getNoHp()) . "<br>";
        echo "<b>Status Pelanggan:</b> " . htmlspecialchars($pelanggan->getStatus()) . "<br>";
        echo "<hr>";
        echo "<div class='row'><div><b>Layanan:</b> " . htmlspecialchars($layanan->getLayanan()) . "<br><span class='muted' style='color: #666;'>Tarif per KM: Rp. " .     number_format($layanan->getTarif(), 0, ',', '.') . "</span><br><b>Jarak:</b> " . (int)$transaksi->jarakTempuh . " KM</div></div>";
        echo "<hr>";
        echo "<b>Sub Total:</b> Rp. " . number_format($subTotal, 0, ',', '.') . "<br>";
        echo "<b>Diskon Member:</b> Rp. " . number_format($diskonMember, 0, ',', '.') . "<br>";

        if ($voucher->diskonPersen > 0) {
            echo "<b>Diskon Voucher (" . intval($voucher->diskonPersen * 100) . "%):</b> Rp. " . number_format($diskonVoucherAmount, 0, ',', '.') . "<br>";
        } else {
            echo "<b>Diskon Voucher:</b> Tidak ada<br>";
        }
        echo "<b>Biaya Admin:</b> Rp. " . number_format($biayaAdmin, 0, ',', '.') . "<br><hr>";
        echo "<b>Metode Pembayaran:</b> " . htmlspecialchars($pembayaran->getMetode()) . "<br><br>";
        echo "<h3><b>Total Pembayaran:</b> Rp. " . number_format($total, 0, ',', '.') . "</h3>";
        echo "</div>";
    } 
    ?>
</body>
</html>