<?php

/**
 * Membuat public/images/logo.png dari logo.svg.
 *
 * DomPDF tidak selalu bisa menggambar SVG dari data-URI <img>, jadi lambang
 * dirender jadi PNG lebih dulu. Dipakai sekali saat setup; kalau lambangnya
 * diganti, hapus logo.png lalu jalankan skrip ini lagi.
 */
$svg = dirname(__DIR__).'/public/images/logo.svg';
$png = dirname(__DIR__).'/public/images/logo.png';


$ukuran = 256;

// GD tidak bisa membaca SVG langsung. Karena itu lambangnya digambar manual
// di kanvas GD dengan bentuk yang sama seperti logo.svg (tiga percikan warna
// + cincin biru + tetesan cat).
$img = imagecreatetruecolor($ukuran, $ukuran);
imagealphablending($img, true);
imagesavealpha($img, true);

$putih = imagecolorallocate($img, 255, 255, 255);
imagefilledrectangle($img, 0, 0, $ukuran, $ukuran, $putih);

$skala = $ukuran / 128;
$px = fn ($v) => (int) round($v * $skala);

$warnaLingkar = imagecolorallocate($img, 37, 99, 235);
$merah = imagecolorallocate($img, 239, 68, 68);
$kuning = imagecolorallocate($img, 245, 158, 11);
$biru = imagecolorallocate($img, 37, 99, 235);
$tetes = imagecolorallocate($img, 29, 78, 216);

// Cincin luar
imagesetthickness($img, $px(6));
imageellipse($img, $px(64), $px(64), $px(88), $px(88), $warnaLingkar);

// Tiga percikan warna
imagefilledellipse($img, $px(64), $px(42), $px(30), $px(30), $merah);
imagefilledellipse($img, $px(83), $px(76), $px(30), $px(30), $kuning);
imagefilledellipse($img, $px(45), $px(76), $px(30), $px(30), $biru);

// Inti oplosan
imagefilledellipse($img, $px(64), $px(64), $px(18), $px(18), $kuning);

// Tetesan cat
imagefilledellipse($img, $px(64), $px(117), $px(14), $px(14), $tetes);
imagefilledrectangle($img, $px(57), $px(101), $px(71), $px(117), $tetes);

imagepng($img, $png);
imagedestroy($img);

echo "logo.png dibuat: {$png} (".filesize($png)." bytes)\n";
