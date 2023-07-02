<?php
// based on PHP File Upload basic example https://www.w3schools.com/php/php_file_upload.asp

date_default_timezone_set('Asia/Jakarta');  //--> Sesuaikan dengan zona waktu Anda.
$target_dir = "captured_images/"; //--> Folder untuk menyimpan gambar.
$date   = new DateTime(); //--> Ini mengembalikan tanggal dan waktu saat ini.
$date_string = $date->format('Y-m-d_His ');
$target_file = $target_dir . $date_string . basename($_FILES["imageFile"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
$file_name = pathinfo($target_file, PATHINFO_BASENAME);

// Periksa apakah file gambar asli atau palsu.
if (isset($_FILES["imageFile"])) {
    $check = getimagesize($_FILES["imageFile"]["tmp_name"]);
    if ($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}

// Periksa apakah file sudah ada.
if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}

// Periksa ukuran file.
if ($_FILES["imageFile"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}

// Izinkan hanya format file tertentu.
if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}

// Periksa apakah $uploadOk diatur menjadi 0 karena ada kesalahan.
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
    // Jika semuanya baik-baik saja, coba unggah file.
} else {
    if (move_uploaded_file($_FILES["imageFile"]["tmp_name"], $target_file)) {
        echo "Photos successfully uploaded to the server with the name: " . $file_name;

        // Simpan informasi file dan UID ke MySQL.
        $servername = "localhost"; // Ganti dengan server MySQL Anda.
        $username = "root"; // Ganti dengan username MySQL Anda.
        $password = ""; // Ganti dengan password MySQL Anda.
        $dbname = "foto"; // Ganti dengan nama database MySQL Anda.

        // Membuat koneksi ke MySQL.
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Memeriksa koneksi.
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Mendapatkan UID dari permintaan HTTP POST.
        $uid = $_POST['uid'];

        // Menyimpan informasi file dan UID ke tabel di database.
        $sql = "INSERT INTO foto (foto, waktu, uid) VALUES ('$file_name', NOW(), '$uid')";

        if ($conn->query($sql) === TRUE) {
            echo "File information saved to MySQL successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        // Menutup koneksi ke MySQL.
        $conn->close();
    } else {
        echo "Sorry, there was an error in the photo upload process.";
    }
}
?>
