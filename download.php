<?php

// Pastikan request hanya dari POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?error=1");
    exit;
}

$schoolId     = $_POST['school_id'] ?? "";
$pendampingId = $_POST['pendamping_id'] ?? "";
$kodeInput    = $_POST['kode_verifikasi'] ?? "";

// Validasi format dasar kode (harus 4 digit)
if (!preg_match('/^[0-9]{4}$/', $kodeInput)) {
    header("Location: index.php?error=1");
    exit;
}

// Load data aman (tidak dikirim ke browser)
$data = require __DIR__ . "/secure-data/data_sekolah_secure.php";

// Validasi ID sekolah
if (!isset($data[$schoolId])) {
    header("Location: index.php?error=1");
    exit;
}

// Validasi ID pendamping pada sekolah tsb
if (!isset($data[$schoolId]['pendamping'][$pendampingId])) {
    header("Location: index.php?error=1");
    exit;
}

$entry = $data[$schoolId]['pendamping'][$pendampingId];

// Verifikasi kode dengan aman
if (!hash_equals($entry['kode_verifikasi'], $kodeInput)) {
    header("Location: index.php?error=1");
    exit;
}

// Ambil file PDF yang sesuai
$pdf  = $entry['pdf_file'];
$path = __DIR__ . "/pdf_files/" . $pdf;

// Pastikan file ada
if (!is_file($path)) {
    header("Location: index.php?error=1");
    exit;
}

// Kirim file ke browser
header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"" . basename($pdf) . "\"");
header("Content-Length: " . filesize($path));

readfile($path);
exit;
