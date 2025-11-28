<?php 
session_start();
// koneksi ke database
// var conn = fungsi koneksi("nama_host", "username", "password", "nama_db"); 
// cara cek username di db mysql dengan CMD --> select user();
$conn = mysqli_connect("localhost", "root", "", "SIMBS");

// fungsi untuk menampilkan data dari database
function query($query){
    global $conn;

	$result = mysqli_query($conn, $query);
	$rows = [];
	while( $row = mysqli_fetch_assoc($result) ) {
		$rows[] = $row;
	}
	return $rows;
}

// fungsi untuk menambahkan data ke database
function tambah_data($data){
    global $conn;

    $nama = $data['nama_buku'];
    // $kategori = $data['id_kategori'];
    $penulis = $data['penulis'];
    $gambar = $data['gambar']; // sementara tidak upload file

    $query = "INSERT INTO buku 
    (nama_buku, penulis, gambar, tanggal_input)
    VALUES 
    ('$nama', '$penulis', '$gambar', CURRENT_TIMESTAMP)";

    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}




// fungsi untuk menghapus data dari database
function hapus_data($id_buku){
    global $conn;
    $query = "DELETE FROM buku WHERE id_buku = $id_buku";
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);    
}


// fungsi untuk mengubah data dari database
function ubah_data($data){
    global $conn;

    $id_buku       = $data['id_buku'];  
    $nama_buku     = $data['nama_buku'];
    $penulis       = $data['penulis'];
    $gambar        = $data['gambar'];
    $tanggal_input = $data['tanggal_input'];

    $query = "UPDATE buku SET
                nama_buku = '$nama_buku',
                penulis = '$penulis',
                gambar = '$gambar',
                tanggal_input = '$tanggal_input'
              WHERE id_buku = $id_buku";

    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn); 
}

// fungsi untuk register
function register($data){
    global $conn;

    $username = strtolower(trim($data['username']));
    $email    = trim($data['email']);
    $password = mysqli_real_escape_string($conn, $data['password']);

    if (strlen($password) < 8) {
        return "Password Harus Mengandung Minimal 8 Karakter";
    }

    // cek username
    $query_username = mysqli_query($conn, 
        "SELECT username FROM user WHERE username = '$username'");
    if (mysqli_fetch_assoc($query_username)) {
        return "Username sudah terdaftar!";
    }

    // cek email
    $query_email = mysqli_query($conn, 
        "SELECT email FROM user WHERE email = '$email'");
    if (mysqli_fetch_assoc($query_email)) {
        return "Email sudah terdaftar!";
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO user (username, email, password)
              VALUES ('$username', '$email', '$password')";

    if (!mysqli_query($conn, $query)) {
        return "DB ERROR: " . mysqli_error($conn);
    }

    return true;
}

// fungsi untuk login
function login($data){
    global $conn;

    $username = $data['username'];
    $password = $data['password'];

    $result = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username'");

    if(mysqli_num_rows($result) === 1){
        $row = mysqli_fetch_assoc($result);

        if(password_verify($password, $row['password'])){
            $_SESSION['login'] = true;
            $_SESSION['username'] = $row['username'];
            return true;
        } 
        return "Password salah!";
    }

    return "Username tidak terdaftar!";
}


// fungsi untuk upload gambar
function upload_gambar($nim, $nama) {


    // setting gambar
    $namaFile = $_FILES['gambar']['name'];
    $ukuranFile = $_FILES['gambar']['size'];
    $error = $_FILES['gambar']['error'];
    $tmpName = $_FILES['gambar']['tmp_name'];


    // cek apakah tidak ada gambar yang diupload
    if( $error === 4 ) {
        echo "<script>
                alert('pilih gambar terlebih dahulu!');
              </script>";
        return false;
    }


    // cek apakah yang diupload adalah gambar
    $ekstensiGambarValid = ['jpg', 'jpeg', 'png'];
    $ekstensiGambar = explode('.', $namaFile);
    $ekstensiGambar = strtolower(end($ekstensiGambar));
    if( !in_array($ekstensiGambar, $ekstensiGambarValid) ) {
        echo "<script>
                alert('yang anda upload bukan gambar!');
              </script>";
        return false;
    }


    // cek jika ukurannya terlalu besar
    // maks --> 5MB
    if( $ukuranFile > 5000000 ) {
        echo "<script>
                alert('ukuran gambar terlalu besar!');
              </script>";
        return false;
    }


    // lolos pengecekan, gambar siap diupload
    // generate nama gambar baru
    $namaFileBaru = $nim . "_" . $nama;
    $namaFileBaru .= '.';
    $namaFileBaru .= $ekstensiGambar;


    move_uploaded_file($tmpName, 'img/' . $namaFileBaru);


    return $namaFileBaru;

}

//fungsi kategori
function tambah_kategori($data){
    global $conn;

    $nama_kategori = $data['nama_kategori'];

    $query = "INSERT INTO kategori (nama_kategori)
              VALUES ('$nama_kategori')";

    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}

// fungsi untuk hapus kategori
function hapus_kategori($id_kategori){
    global $conn;
    $query = "DELETE FROM kategori WHERE id_kategori = $id_kategori";
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);    
}

// fungsi untuk ubah kategori
function ubah_kategori($data){
    global $conn;

    $id_kategori       = $data['id_kategori'];  
    $nama_kategori     = $data['nama_kategori'];

    $query = "UPDATE kategori SET
                nama_kategori = '$nama_kategori'

              WHERE id_kategori = $id_kategori";

    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn); 
}

?>
