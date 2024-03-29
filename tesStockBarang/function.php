<?php
session_start();
// Membuat koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "tesStockBarang"); // format ("host","username sql","password","database")
// Menambah Barang Baru
if (isset($_POST['addnewbarang'])){
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stockbarang = $_POST["stockbarang"];
    // Gambar
    $allowed_extensions= array('png','jpg'); // jenis file yang diijinkan
    $nama = $_FILES['gambar']['name']; // mengambil nama gambar
    $dot = explode('.',$nama); // untuk mengambil kata setelah titik dalam file
    $ekstension = strtolower(end($dot)); //menampung ekstensinya
    $ukuran = $_FILES['gambar']['size']; //menampung size gambar
    $file_tmp = $_FILES['gambar']['tmp_name']; // menampung lokasi file
    //penampilan --> 
    $addtotable = mysqli_query($conn,"insert into stock(namabarang,deskripsi,stockbarang) values('$namabarang','$deskripsi','$stockbarang')");
    if($addtotable){
        header("location:index.php");
    }else{
        echo "Gagal";
        header("location:index.php");
    }
};
// Menambah barang masuk
if(isset($_POST['barangmasuk'])){
    // mengambil data dri inputan
    $barangin = $_POST['barangin'];
    $keterangan = $_POST['keterangan'];
    $qtymasuk = $_POST['qtymasuk'];
    // mengambil data dari tabel stock
    $cekstocknow = mysqli_query($conn,"select * from stock where idbarang ='$barangin'");
    $ambilstock = mysqli_fetch_array($cekstocknow);
    // mengambil data stockbarang dri tabel stock
    $stocknow = $ambilstock['stockbarang'];
    $stockqty = $stocknow+$qtymasuk;
    //memasukan data yg diinput kedalam table masuk
    // jangan memakai mysql_command pkai mysqli_command suka error gaboleh di mix juga
    $addtablemasuk = mysqli_query($conn,"insert into masuk(idbarang,keterangan,qtymasuk) values('$barangin','$keterangan','$qtymasuk')");
    //mengupdate data barang masuk ke dalam data stockbarang di tabel masuk
    $updatestockmasuk = mysqli_query($conn,"update stock set stockbarang='$stockqty' where idbarang='$barangin'");
    if($addtablemasuk&&$updatestockmasuk){
        header("location:masuk.php");
    }else{
        echo "Gagal Memasukan Data";
        header("location:masuk.php");
    }
}
// menambah barang keluar
if(isset($_POST['barangkeluar'])){
    // mengambil data dri inputan
    $barangout = $_POST['barangout'];
    $penerima = $_POST['penerima'];
    $qtykeluar = $_POST['qtykeluar'];
    // mengambil data dari tabel stock
    $cekstocknow = mysqli_query($conn,"select * from stock where idbarang ='$barangout'");
    $ambilstock = mysqli_fetch_array($cekstocknow);
    $stocks = $ambilstock['stockbarang'];
    //kalau barangnya cukup
    if($stocks >= $qtykeluar){
        $stockqty = $stocks-$qtykeluar;
        //memasukan data yg diinput kedalam table masuk
        // jangan memakai mysql_command pkai mysqli_command suka error gaboleh di mix juga
        $addtablekeluar = mysqli_query($conn,"insert into keluar(idbarang,penerima,qtykeluar) values('$barangout','$penerima','$qtykeluar')");
        //mengupdate data barang masuk ke dalam data stockbarang di tabel masuk
        $updatestockkeluar = mysqli_query($conn,"update stock set stockbarang='$stockqty' where idbarang='$barangout'");
        if($addtablekeluar&&$updatestockkeluar){
            header("location:keluar.php");
        }else{
            echo "Gagal Memasukan Data";
            header("location:keluar.php");
        }
    // kalau barangnya tidak cukup
    }else{
        echo'
        <script>
            alert("Stock saat ini tidak mencukupi");
            window.location.href="keluar.php";
        </script>
        ';
    }
}
// edit stock barang
if(isset($_POST['editstockbarang'])){
    $idb = $_POST['idb'];
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $editStock = mysqli_query($conn,"update stock set namabarang = '$namabarang', deskripsi = '$deskripsi' where idbarang = '$idb'");
    if($editStock){
        header("location:index.php");
    }else{
        echo "Edit Gagal";
        header("location:index.php");
    }
}
// hapus stock barang
if(isset($_POST['hapusstockbarang'])){
    $idb = $_POST['idb'];
    $hapusStock = mysqli_query($conn,"delete from stock where idbarang = '$idb'");
    $hapusbarangmasuk= mysqli_query($conn,"delete from masuk where idbarang ='$idb'");
    $hapusbarangkeluar= mysqli_query($conn,"delete from keluar where idbarang ='$idb'");
    if($hapusStock&&$hapusbarangmasuk&&$hapusbarangkeluar){
        header("location:index.php");
    }else{
        echo "Edit Gagal";
        header("location:index.php");
    }
}
// edit barang masuk
if(isset($_POST['editmasukbarang'])){
    $idb = $_POST['idb'];
    $idm = $_POST['idm'];
    $namabarang = $_POST['namabarang'];
    $keterangan = $_POST['keterangan'];
    $qtymasuk = $_POST['qtymasuk'];
    // ambil data stockbarang dari tabel stock
    $cekstock = mysqli_query($conn,"select * from stock where idbarang = '$idb'");
    $ambilstock = mysqli_fetch_array($cekstock);
    $stocklama = $ambilstock['stockbarang'];
    // ambil data qtymasuk dari tabel masuk
    $cekqtymasuk= mysqli_query($conn,"select*from masuk where idmasuk ='$idm'");
    $ambilqtymasuk= mysqli_fetch_array($cekqtymasuk);
    $qtymasuklama= $ambilqtymasuk['qtymasuk'];
    if($qtymasuk<$qtymasuklama){
        $selisih = $qtymasuklama - $qtymasuk;
        if($stocklama>=$selisih){
            $stockbaru = $stocklama - $selisih;
            $kurangstock = mysqli_query($conn,"update stock set stockbarang = '$stockbaru' where idbarang ='$idb'");
            $editMasuk = mysqli_query($conn,"update masuk set qtymasuk = '$qtymasuk', keterangan='$keterangan' where idmasuk='$idm'");
            if($kurangstock&&$editMasuk){
                header("location:masuk.php");
            }else{
                echo "Gagal";
                header("location:masuk.php");
            }
        }else{
            echo'
            <script>
                alert("Stock saat ini tidak mencukupi");
                window.location.href="masuk.php";
            </script>
            ';
        }
    }else if($qtymasuk>$qtymasuklama){
        $selisih = $qtymasuk-$qtymasuklama;
        $stockbaru= $stocklama+$selisih;
        $tambahstock=mysqli_query($conn,"update stock set stockbarang ='$stockbaru' where idbarang='$idb'");
        $editMasuk=mysqli_query($conn,"update masuk set qtymasuk='$qtymasuk', keterangan='$keterangan' where idmasuk ='$idm'");
        if($tambahstock&&$editMasuk){
            header("location:masuk.php");
        }else{
            echo "Gagal";
            header("location:masuk.php");
        }
    }
}
// hapus barang masuk
if(isset($_POST['hapusmasukbarang'])){
    $idb=$_POST['idb'];
    $idm=$_POST['idm'];
    $qtymasuk=$_POST['qtymasuk'];
    $getstock=mysqli_query($conn,"select*from stock where idbarang='$idb'");
    $takestock=mysqli_fetch_array($getstock);
    $datastock= $takestock['stockbarang'];
    if($datastock>$qtymasuk){
        $kurangistock= $datastock-$qtymasuk;
        $updatestockhapus = mysqli_query($conn,"update stock set stockbarang ='$kurangistock' where idbarang='$idb'");
        $hapusmasuk=mysqli_query($conn,"delete from masuk where idmasuk ='$idm'");
        if($updatestockhapus&&$hapusmasuk){
            header("location:masuk.php");
        }else{
            echo"Gagal";
            header("location:masuk.php");
        }
    }else{
        echo'
        <script>
            alert("Stock saat ini tidak mencukupi. Silahkan tambah Stock atau Edit Barang Keluar Terlebih dahulu");
            window.location.href="masuk.php";
        </script>
        ';
    }
}
// edit barang keluar
if(isset($_POST['editkeluarbarang'])){
    $idb = $_POST['idb'];
    $idk = $_POST['idk'];
    $namabarang = $_POST['namabarang'];
    $penerima = $_POST['penerima'];
    $qtykeluar = $_POST['qtykeluar'];
    // ambil data stockbarang dari tabel stock
    $cekstock = mysqli_query($conn,"select * from stock where idbarang = '$idb'");
    $ambilstock = mysqli_fetch_array($cekstock);
    $stocklama = $ambilstock['stockbarang'];
    // ambil data qtymasuk dari tabel masuk
    $cekqtykeluar= mysqli_query($conn,"select*from keluar where idkeluar ='$idk'");
    $ambilqtykeluar= mysqli_fetch_array($cekqtykeluar);
    $qtykeluarlama= $ambilqtykeluar['qtykeluar'];
    if($qtykeluar>$qtykeluarlama){
        $selisih = $qtykeluar - $qtykeluarlama;
        if($stocklama>=$selisih){
            $stockbaru = $stocklama - $selisih;
            $kurangstock = mysqli_query($conn,"update stock set stockbarang = '$stockbaru' where idbarang ='$idb'");
            $editKeluar = mysqli_query($conn,"update keluar set qtykeluar = '$qtykeluar', penerima='$penerima' where idkeluar='$idk'");
            if($kurangstock&&$editKeluar){
                header("location:keluar.php");
            }else{
                echo "Gagal";
                header("location:keluar.php");
            }
        }else{
            echo'
            <script>
                alert("Stock saat ini tidak mencukupi");
                window.location.href="keluar.php";
            </script>
            ';
        }
    }else if($qtykeluar<$qtykeluarlama){
        $selisih = $qtykeluarlama-$qtykeluar;
        $stockbaru= $stocklama + $selisih;
        $tambahstock=mysqli_query($conn,"update stock set stockbarang ='$stockbaru' where idbarang='$idb'");
        $editKeluar=mysqli_query($conn,"update keluar set qtykeluar='$qtykeluar', penerima='$penerima' where idkeluar ='$idk'");
        if($tambahstock&&$editKeluar){
            header("location:keluar.php");
        }else{
            echo "Gagal";
            header("location:keluar.php");
        }
    }
}
// hapus barang keluar
if(isset($_POST['hapuskeluarbarang'])){
    $idb=$_POST['idb'];
    $idk=$_POST['idk'];
    $qtykeluar=$_POST['qtykeluar'];
    $getstock=mysqli_query($conn,"select*from stock where idbarang='$idb'");
    $takestock=mysqli_fetch_array($getstock);
    $datastock= $takestock['stockbarang'];
    $kurangistock= $datastock+$qtykeluar;
    $updatestockhapus = mysqli_query($conn,"update stock set stockbarang ='$kurangistock' where idbarang='$idb'");
    $hapuskeluar=mysqli_query($conn,"delete from keluar where idkeluar ='$idk'");
    if($updatestockhapus&&$hapuskeluar){
        header("location:keluar.php");
    }else{
        echo"Gagal";
        header("location:keluar.php");
    }
}
// Tambah Admin Baru
if(isset($_POST['addnewadmin'])){
    $email=$_POST['emailadmin'];
    $password=$_POST['password'];
    $insertlogin=mysqli_query($conn,"insert into login (email,password) values ('$email','$password')");
    if($insertlogin){
        header("location:admin.php");
    }else{
        echo"
        <script>
            alert('Data Admin Gagal Dimasukan')
            window.location.href='admin.php';
        </script>";
    }
}
//Edit Data Admin
if (isset($_POST['editadmin'])){
    $email = $_POST['emailbaru'];
    $password=$_POST['passwordbaru'];
    $idu=$_POST['idu'];
    $editlogin=mysqli_query($conn,"update login set email='$email', password='$password' where iduser='$idu'");
    if($editlogin){
        header("location:admin.php");
    }else{
        echo"
        <script>
            alert('Data Admin Gagal Diubah');
            window.location.href='admin.php';
        </script>";
    }
}
//Hapus Data Admin
if(isset($_POST['hapusadmin'])){
    $idu=$_POST['idu'];
    $hapuslogin=mysqli_query($conn,"delete from login where iduser = '$idu'");
    if($hapuslogin){
        header("location:admin.php");
    }else{
        echo
        "<script>
            alert('Data Admin Gagal Dihapus');
            window.location.href='admin.php';
        </script>";
    }
}
?>