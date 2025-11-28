<?php 
session_start();
    if( !isset($_SESSION["login"]) ) {
        header("Location: login.php");
        exit;
    }
    require("function.php");

    $id = $_GET['id'];

    // jika query hapus data yang ada di fungsi hapus_data() di function.php bernilai true (berhasil dihapus)
    // maka tampilkan alert bahwa data berhasil dihapus
    if(hapus_data($id) > 0){
        echo "
            <script>
                alert('Data berhasil dihapus dari database!');
                document.location.href = 'index.php';
            </script>
        ";
    }else{
        echo "
            <script>
                alert('Data gagal dihapus dari database!');
                document.location.href = 'index.php';
            </script>
        ";
    }
?>