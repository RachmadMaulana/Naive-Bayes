<?php

    error_reporting(E_ALL ^ (E_NOTICE));

    // Membuat validasi jika button lihatdata di halaman index ditekan
    if(isset($_POST['lihatdata'])){
        $nama_file_baru = 'data.xlsx';

        // Load librari PHPExcel nya
        require_once 'PHPExcel/PHPExcel.php';
        
        $excelreader = new PHPExcel_Reader_Excel2007();
        $loadexcel = $excelreader->load('tmp/'.$nama_file_baru); // Load file yang tadi diupload ke folder tmp
        $sheet = $loadexcel->getActiveSheet()->toArray(null, true, true ,true);
        
        // Perulangan untuk import data dari excel ke dalam array $dataexcelnama dan $dataexcelnilai
        $kosong = 0;
        $abjad1 = 'A';
        while ($abjad1 != 'Z') {
            if ($sheet[1][$abjad1] !== null) {
                $dataexcelnama[1][] = $sheet[1][$abjad1];
                $abjad1 = chr(ord($abjad1) + 1);
            } else {
                break;
            }
        }
        
        $abjad2 = 'A';
        $nilai1 = 0;
        $nilai2 = 0;
        for ($i=2; $i<=count($sheet); $i++) {
            while ($abjad2 != 'Z') {
                if ($sheet[$i][$abjad2] !== null) {
                    $dataexcelnilai[$nilai2][$dataexcelnama[1][$nilai1]] = $sheet[$i][$abjad2];
                    $abjad2 = chr(ord($abjad2) + 1);
                    $nilai1++;
                } else {
                    break;
                }
            }
            $abjad2 = 'A';
            $nilai1 = 0;
            $nilai2++;
        
        }
    }
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aplikasi Naive Bayes - Data</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        th {
            background-color: #afd9ee;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><b>Dataset</b></h1>
        <br>
        <table class='table table-bordered table-hover table-striped'>
            <tr>
            <?php
                // Menampilkan dataset yang sudah di import
                echo "<th>NO.</th>";
                foreach ($dataexcelnilai[0] as $key => $value) {
                    echo "<th>".strtoupper($key)."</th>";
                }

                for ($i=0; $i<count($dataexcelnilai); $i++) {
                    echo "<tr>";
                    echo "<td>".str_pad($i+1,2,0,STR_PAD_LEFT)."</td>";
                    foreach ($dataexcelnilai[$i] as $key => $value) {
                        echo "<td>{$value}</td>";
                    }
                    echo "</tr>";
                }
            ?>	
            </tr>
        </table>
        <div class="row">
            <div class="col-md-6">
                <h3><b>Tambah Data Baru</b></h3>
                <br>
                <form action="dataimport.php" method="post">
                <?php
                    // Menampilkan form untuk menginputkan data baru
                    $nomor = 0;
                    foreach ($dataexcelnama[1] as $value) {
                        for ($i=0; $i<count($dataexcelnilai); $i++) {
                            $coba{$nomor}[] = $dataexcelnilai[$i][$value];
                        }
                        $tes[] = array_unique($coba[$nomor]);
                        $nomor++;
                    }
                    for ($i=0; $i<count($dataexcelnama[1]) - 1; $i++) {
                        echo "<p>";
                        echo "<label>Masukkan data {$dataexcelnama[1][$i]} baru : </label>";
                        echo "<input class='form-control' type='text' name={$dataexcelnama[1][$i]} id={$dataexcelnama[1][$i]}>";
                        echo "</p>";
                    }
                ?>
                <div class="col-sm-offset-8">
                    <p>
                    <br>
                        <input class="pull-right btn btn-primary" type="submit" name="lihatdata" value="Submit">
                    </p>
                </div>
                <br><br>
            </div>
            <div class="col-md-6">
                </form>
                <?php
                    // Membuat validasi jika button submit ditekan
                    if(isset($_POST['lihatdata'])){
                        // Mengambil data baru yang di input user
                        for ($i=0; $i<count($dataexcelnama[1]) - 1; $i++) {
                            $databaru[] = trim($_POST[$dataexcelnama[1][$i]]);
                        }
                        foreach ($tes[count($dataexcelnama[1])-1] as $key => $value) {
                            $NamaKelas[] = $value;
                        }

                        // Menghitung jumlah kelas ya dan tidak
                        $JumlahKelas[$NamaKelas[0]]=0;
                        $JumlahKelas[$NamaKelas[1]]=0;
                        for($i=0; $i<count($dataexcelnilai); $i++) {
                            if($dataexcelnilai[$i][$dataexcelnama[1][count($dataexcelnama[1])-1]] == $NamaKelas[0]) {
                                $JumlahKelas[$NamaKelas[0]]++;
                            } else {
                                $JumlahKelas[$NamaKelas[1]]++;
                            }
                        }

                        // Fungsi untuk mensorting atribut data yang dipilih kedalam array $result
                        function sortingAtribut($nilaibaru, $data, $data2, $dataexcelnama) {
                            $b=0; 
                            for($i=0; $i<count($data); $i++) {
                                if ($nilaibaru == $data[$i][$data2]) {
                                    $result[$b][$data2] = $data[$i][$data2];
                                    $result[$b]['nilaikelas'] = $data[$i][$dataexcelnama[1][count($dataexcelnama[1])-1]];
                                    $b+=1;
                                }
                            }
                            return $result;
                        }

                        // Memasukkan fungsi sortingAtribut kedalam variabel berbeda
                        for($i=0; $i<count($dataexcelnama[1])-1; $i++) {
                            $dataatribut[] = sortingAtribut($databaru[$i], $dataexcelnilai, $dataexcelnama[1][$i], $dataexcelnama);
                        }
                        
                        // Fungsi untuk mensorting jumlah nilai ya dan tidak
                        function sortingKelas($result, $NamaKelas) {
                            $Jumlah[$NamaKelas[0]]=0;
                            $Jumlah[$NamaKelas[1]]=0;
                            for($i=0; $i<count($result); $i++) {
                                if ($result[$i]['nilaikelas'] == $NamaKelas[0]) {
                                    $Jumlah[$NamaKelas[0]]++;
                                } else {
                                    $Jumlah[$NamaKelas[1]]++;
                                }
                            }
                            return $Jumlah;
                        }

                        // Memasukkan fungsi sortingKelas kedalam array $sorting
                        for($i=0; $i<count($dataexcelnama[1])-1; $i++) {
                            $sorting[] = sortingKelas($dataatribut[$i], $NamaKelas);
                        }

                        // Fungsi untuk menghitung peluang atribut yang dipilih
                        function peluang($nilaiatribut, $jumlahkelas, $NamaKelas) {
                            $Phasil[$NamaKelas[0]] = $nilaiatribut[$NamaKelas[0]] / $jumlahkelas[$NamaKelas[0]];
                            $Phasil[$NamaKelas[1]] = $nilaiatribut[$NamaKelas[1]] / $jumlahkelas[$NamaKelas[1]];
                            return $Phasil;
                        }
                        
                        // Memasukkan fungsi peluang kedalam array $datapeluang
                        for($i=0; $i<count($dataexcelnama[1])-1; $i++) {
                            $datapeluang[] = peluang($sorting[$i], $JumlahKelas, $NamaKelas);
                        }

                        // Mengkalikan hasil dari array $datapeluang dan memasukkan ke dalam array $peluang
                        $peluang[$NamaKelas[0]] = $datapeluang[0][$NamaKelas[0]];
                        $peluang[$NamaKelas[1]] = $datapeluang[0][$NamaKelas[1]];
                        for($i=1; $i<count($dataexcelnama[1])-1; $i++) {
                            $peluang[$NamaKelas[0]] *= $datapeluang[$i][$NamaKelas[0]];
                            $peluang[$NamaKelas[1]] *= $datapeluang[$i][$NamaKelas[1]];
                        }

                        // Mengkalikan array $peluang dengan masing2 jumlah kelas / jumlah semua data
                        $hasil[$NamaKelas[0]] = $peluang[$NamaKelas[0]] * $JumlahKelas[$NamaKelas[0]] / count($dataexcelnilai);
                        $hasil[$NamaKelas[1]] = $peluang[$NamaKelas[1]] * $JumlahKelas[$NamaKelas[1]] / count($dataexcelnilai);

                        // Hasil untuk menentukan kelas mana yang akan dipilih
                        if ($hasil[$NamaKelas[0]] > $hasil[$NamaKelas[1]]) {
                            $jawaban = $NamaKelas[0];
                        } else if ($hasil[$NamaKelas[0]] < $hasil[$NamaKelas[1]]) {
                            $jawaban = $NamaKelas[1];
                        } else {
                            $jawaban = "";
                        }

                        if ($databaru[0] != null) {

                        echo "<br><br><br><br><br>";
                        echo "<div class='alert alert-info'>
                            <p><b>Data baru</b></p>";
                            for($i=0; $i<count($dataexcelnama[1])-1; $i++) {
                                echo "<p>{$dataexcelnama[1][$i]} = $databaru[$i]</p>";
                            } 
                        echo "</div>";

                        echo "<div class='alert alert-info'>
                            <p><b>Hasil</b></p>";
                        echo $jawaban;
                        echo "</div>";
                    ?>
            </div>
        </div>
        <div class="alert alert-info">
            <!-- Menampilkan data perhitungan -->
            <h3><b>Perhitungan</b></h3>
            <br>
            <?php
                for ($i=0; $i<count($dataexcelnama[1])-1; $i++) {
                    echo "<p>P({$dataexcelnama[1][$i]} = {$databaru[$i]} | {$dataexcelnama[1][count($dataexcelnama[1])-1]} = {$NamaKelas[0]}) = {$sorting[$i][$NamaKelas[0]]}/{$JumlahKelas[$NamaKelas[0]]} = {$datapeluang[$i][$NamaKelas[0]]}</p>";
                    echo "<p>P({$dataexcelnama[1][$i]} = {$databaru[$i]} | {$dataexcelnama[1][count($dataexcelnama[1])-1]} = {$NamaKelas[1]}) = {$sorting[$i][$NamaKelas[1]]}/{$JumlahKelas[$NamaKelas[1]]} = {$datapeluang[$i][$NamaKelas[1]]}</p>";
                    echo "<br>";
                }

                echo "<p>P( X | {$dataexcelnama[1][count($dataexcelnama[1])-1]} = {$NamaKelas[0]}) = ";
                for ($i=0; $i<count($dataexcelnama[1])-1; $i++) {
                    if ($i >= count($dataexcelnama[1])-2) {
                        echo "{$datapeluang[$i][$NamaKelas[0]]} = {$peluang[$NamaKelas[0]]}";
                    } else {
                        echo "{$datapeluang[$i][$NamaKelas[0]]} * ";
                    }
                }
                echo "<p>P( X | {$dataexcelnama[1][count($dataexcelnama[1])-1]} = {$NamaKelas[1]}) = ";
                for ($i=0; $i<count($dataexcelnama[1])-1; $i++) {
                    if ($i >= count($dataexcelnama[1])-2) {
                        echo "{$datapeluang[$i][$NamaKelas[1]]} = {$peluang[$NamaKelas[1]]}";
                    } else {
                        echo "{$datapeluang[$i][$NamaKelas[1]]} * ";
                    }
                }
                echo "<br><br>";
                $jumlahdata = count($dataexcelnilai);
                echo "<p>P( X | {$dataexcelnama[1][count($dataexcelnama[1])-1]} = {$NamaKelas[0]}) * P({$dataexcelnama[1][count($dataexcelnama[1])-1]} = {$NamaKelas[0]}) = {$peluang[$NamaKelas[0]]} * {$JumlahKelas[$NamaKelas[0]]}/$jumlahdata = {$hasil[$NamaKelas[0]]}</p>
                      <p>P( X | {$dataexcelnama[1][count($dataexcelnama[1])-1]} = {$NamaKelas[1]}) * P({$dataexcelnama[1][count($dataexcelnama[1])-1]} = {$NamaKelas[1]}) = {$peluang[$NamaKelas[1]]} * {$JumlahKelas[$NamaKelas[1]]}/$jumlahdata = {$hasil[$NamaKelas[1]]}</p>
                      <br>";
                      
                echo "X Memliliki kelas {$dataexcelnama[1][count($dataexcelnama[1])-1]} = <b>$jawaban</b> karena P( X | {$dataexcelnama[1][count($dataexcelnama[1])-1]} = $jawaban) memiliki nilai maksimum";
            }
        }
            ?>
        </div>
    </div>
</body>
</html>