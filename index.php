<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Aplikasi Naive Bayes - Import</title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<style>
	th {
		text-align: center;
	}
	</style>
	<script src="js/jquery.min.js"></script>
	<!-- Membuat script untuk menyembunyikan validasi kosong -->
	<script>
	$(document).ready(function(){
		$("#kosong").hide();
	});
	</script>
</head>
<body>
	<div class="container">
		<h3><b>Import Data</b></h3>
		<hr>
		<!-- Membuat form untuk menginput data excel -->
		<form method="post" action="" enctype="multipart/form-data">
			<input type="file" name="file" class="pull-left">
			<button type="submit" name="import" class="btn btn-success btn-sm">
				<span class="glyphicon glyphicon-upload"></span> Import
			</button>
		</form>
		<?php
			// Membuat validasi jika button import ditekan
			if(isset($_POST['import'])){
				echo "<form method='post' action='dataimport.php'>";
				$nama_file_baru = 'data.xlsx';
				
				// Cek apakah terdapat file data.xlsx pada folder tmp
				if(is_file('tmp/'.$nama_file_baru)) // Jika file tersebut ada
					unlink('tmp/'.$nama_file_baru); // Hapus file tersebut
				
				$tipe_file = $_FILES['file']['type']; // Ambil tipe file yang akan diupload
				$tmp_file = $_FILES['file']['tmp_name'];
				
				// Cek apakah file yang diupload adalah file Excel 2007 (.xlsx)
				if($tipe_file == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
					// Upload file yang dipilih ke folder tmp
					// dan rename file tersebut menjadi data{ip_address}.xlsx
					// {ip_address} diganti jadi ip address user yang ada di variabel $ip
					// Contoh nama file setelah di rename : data127.0.0.1.xlsx
					move_uploaded_file($tmp_file, 'tmp/'.$nama_file_baru);

					// Jika file yang diupload File Excel 2007 (.xlsx)
					// Munculkan pesan validasi benar dan tombol lihat data
					echo "<br> <div class='alert alert-info'>
					Berhasil
					</div>";

					echo "<button type='submit' name='lihatdata' class='btn btn-primary'>
						<span class='glyphicon glyphicon-upload'></span> Lihat Data
						</button>";
					echo "</form>";
				} else { 
					// Jika file yang diupload bukan File Excel 2007 (.xlsx)
					// Munculkan pesan validasi salah
					echo "<br> <div class='alert alert-danger'>
					Hanya File Excel 2007 (.xlsx) yang diperbolehkan
					</div>";
				}
			}
		?>
	</div>
</body>
</html>