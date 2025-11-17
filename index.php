<?php
// Load data sekolah aman
$data = require __DIR__ . "/secure-data/data_sekolah_secure.php";

// Urutkan berdasarkan nama sekolah (alfabet)
$sorted = [];
foreach ($data as $id => $row) {
    $sorted[$row['nama_sekolah']] = ["id" => $id, "pendamping" => $row['pendamping']];
}
ksort($sorted);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Peserta Bebras Challenge 2025</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Daftar Peserta Bebras Challenge 2025</h1>

<div class="school-list">

<?php
// Loop sekolah (UI tetap sama)
foreach ($sorted as $school_name => $info):
    $schoolId = $info['id'];
    $pendamping_list = $info['pendamping'];
?>
    <div class='school-item'>
        <h3><?= htmlspecialchars($school_name) ?></h3>
        <ul class='pendamping-list'>
        <?php foreach ($pendamping_list as $pendampingId => $pendamping): ?>
            <li>
                <strong>Pendamping:</strong> <?= htmlspecialchars($pendamping['nama']) ?>
                
                <!-- Tidak ada kode verifikasi di HTML! -->
                <button class='download-btn'
                    onclick='openModal(<?= json_encode($schoolId) ?>, <?= json_encode($pendampingId) ?>, <?= json_encode($pendamping["nama"]) ?>)'
                >
                    Download PDF
                </button>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>

<?php endforeach; ?>

</div>

<!-- The Modal -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Verifikasi Unduhan</h3>
        <p id="modal-pendamping-name"></p>
        <p>Masukkan 4 digit terakhir nomor telepon pendamping untuk mengunduh PDF.</p>

        <!-- Form menuju download.php -->
        <form id="verificationForm" method="POST" action="download.php">
            <input type="hidden" id="schoolIdInput" name="school_id">
            <input type="hidden" id="pendampingIdInput" name="pendamping_id">

            <label for="verificationCode">Kode Verifikasi (4 Digit):</label>
            <input type="text" id="verificationCode" name="kode_verifikasi" placeholder="XXXX" maxlength="4" required>

            <div id="errorMessage" class="error-message"></div>

            <button type="submit">Verifikasi dan Unduh</button>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById("myModal");

    function openModal(schoolId, pendampingId, pendampingName) {
        document.getElementById("schoolIdInput").value = schoolId;
        document.getElementById("pendampingIdInput").value = pendampingId;
        document.getElementById("modal-pendamping-name").textContent = "Pendamping: " + pendampingName;

        document.getElementById("verificationCode").value = "";
        document.getElementById("errorMessage").textContent = "";

        modal.style.display = "block";
    }

    function closeModal() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) closeModal();
    };

    // Client-side validation ONLY (UI), verifikasi tetap dilakukan server-side
    document.getElementById("verificationForm").addEventListener("submit", function(event) {
        const code = document.getElementById("verificationCode").value.trim();
        const errorDiv = document.getElementById("errorMessage");

        errorDiv.textContent = "";

        if (code.length !== 4 || isNaN(code)) {
            errorDiv.textContent = "Kode harus berupa 4 digit angka.";
            event.preventDefault();
            return;
        }
    });
</script>

</body>
</html>
