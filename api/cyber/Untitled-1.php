<?php session_start();
include "kongu.php";
include "kongu_hris.php";


// if($_COOKIE['dept'] == 'D12' || $_COOKIE['dept'] == 'D13' || $_COOKIE['section'] == 'S183'){
$section = $_COOKIE['section'];
$dept = $_COOKIE['dept'];

$people = array("D12", "D13", "S183", "D07");
if (!in_array($section, $people) && !in_array($dept, $people)) {
	header("Location: 404.php");
}


// $people = array("S183","D12","D13");
// if (!in_array($_COOKIE['dept'], $people))
// {
// header("Location: 404.php");
// }

// if (!in_array($_COOKIE['section'], $people))
// {
// header("Location: 404.php");
// }

// function getNextStatus($current)
// {
// 	$statusSequence = [0, 7, 3, 1, 2, 4];

// 	$index = array_search($current, $statusSequence);

// 	if ($index === false) {
// 		return null; // status tidak dalam flow
// 	}

// 	if (!isset($statusSequence[$index + 1])) {
// 		return null; // sudah akhir (Closed)
// 	}

// 	return $statusSequence[$index + 1];
// }


// $nextStatus = getNextStatus($row['status']);

// if ($nextStatus !== null) {
//     if ($nextStatus == 7) {
//         echo '<button class="btn btn-primary" onclick="updateStatus('.$row['komplain_id'].', 7)">Survey</button>';
//     }
//     if ($nextStatus == 3) {
//         echo '<button class="btn btn-warning" onclick="openBudgeting('.$row['komplain_id'].')">Input Budget</button>';
//     }
//     if ($nextStatus == 1) {
//         echo '<button class="btn btn-info" onclick="updateStatus('.$row['komplain_id'].', 1)">Work In Progress</button>';
//     }
//     if ($nextStatus == 2) {
//         echo '<button class="btn btn-success" onclick="openFinishModal('.$row['komplain_id'].')">Finish (Upload Foto)</button>';
//     }
//     if ($nextStatus == 4) {
//         echo '<button class="btn btn-dark" onclick="updateStatus('.$row['komplain_id'].', 4)">Close</button>';
//     }
// }

$statusMap = [
	0 => ['label' => 'Open', 'color' => 'red'],
	7 => ['label' => 'Survey', 'color' => 'orange'],
	3 => ['label' => 'Budgeting', 'color' => 'purple'],
	1 => ['label' => 'Work In Progress', 'color' => '#F3950D'],
	2 => ['label' => 'Finished by Project/GA', 'color' => 'blue'],
	4 => ['label' => 'Closed by Store', 'color' => 'green'],
	6 => ['label' => 'Cancel', 'color' => 'gray'],
	8 => ['label' => 'Delete', 'color' => 'black'],
];

function getStatusLabel($status)
{
	$statusMap = [
		0 => ['label' => 'Open', 'color' => 'red'],
		1 => ['label' => 'Work In Progress', 'color' => '#F3950D'],
		2 => ['label' => 'Finished', 'color' => 'blue'],
		3 => ['label' => 'Budgeting', 'color' => 'purple'],
		4 => ['label' => 'Closed', 'color' => 'brown'],
		// 5 => ['label' => 'Rencana', 'color' => '#000'],
		6 => ['label' => 'Cancel', 'color' => '#000'],
		7 => ['label' => 'Survey', 'color' => '#000'],
		8 => ['label' => 'Delete', 'color' => '#000'],
	];

	if (!isset($statusMap[$status])) {
		return "<font style='background-color: gray; padding: 5px; color: #fff'>Unknown</font>";
	}

	$info = $statusMap[$status];
	return "<font style='background-color: {$info['color']}; padding: 5px; color: #fff'>{$info['label']}</font>";
}




function selisihDate($tgl11, $tgl21)
{
	$tgl1 = strtotime($tgl11);
	$tgl2 = strtotime($tgl21);

	$jarak = $tgl2 - $tgl1;

	$hari = $jarak / 60 / 60 / 24;
	return $hari;

}


?>
<!DOCTYPE HTML>
<html>

<head>
	<title>GA Pengaduan</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="Modern Responsive web template, Bootstrap Web Templates, Flat Web Templates, Andriod Compatible web template, 
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyErricsson, Motorola web design" />
	<script
		type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
	<!-- Bootstrap Core CSS -->
	<link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
	<!-- Custom CSS -->
	<link href="css/style.css" rel='stylesheet' type='text/css' />
	<link href="css/font-awesome.css" rel="stylesheet">
	<!-- jQuery -->
	<link href="//cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css" rel="stylesheet">
	<!-- jQuery -->
	<script src="js/jquery.min.js"></script>
	<script src="//cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
	<!----webfonts--->
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900' rel='stylesheet' type='text/css'>
	<!---//webfonts--->
	<!-- Bootstrap Core JavaScript -->
	<script src="js/bootstrap.min.js"></script>
	<style>
		body {
			padding-right: 0px !important;
		}

		#snackbar {
			visibility: hidden;
			min-width: 250px;
			margin-left: -125px;
			background-color: #333;
			color: #fff;
			text-align: center;
			border-radius: 2px;
			padding: 16px;
			position: fixed;
			z-index: 1;
			left: 50%;
			bottom: 30px;
			font-size: 17px;
		}

		#snackbar.show {
			visibility: visible;
			-webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
			animation: fadein 0.5s, fadeout 0.5s 2.5s;
		}

		#snackbardelete {
			visibility: hidden;
			min-width: 250px;
			margin-left: -125px;
			background-color: #333;
			color: #fff;
			text-align: center;
			border-radius: 2px;
			padding: 16px;
			position: fixed;
			z-index: 1;
			left: 50%;
			bottom: 30px;
			font-size: 17px;
		}

		#snackbardelete.show {
			visibility: visible;
			-webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
			animation: fadein 0.5s, fadeout 0.5s 2.5s;
		}

		@-webkit-keyframes fadein {
			from {
				bottom: 0;
				opacity: 0;
			}

			to {
				bottom: 30px;
				opacity: 1;
			}
		}

		@keyframes fadein {
			from {
				bottom: 0;
				opacity: 0;
			}

			to {
				bottom: 30px;
				opacity: 1;
			}
		}

		@-webkit-keyframes fadeout {
			from {
				bottom: 30px;
				opacity: 1;
			}

			to {
				bottom: 0;
				opacity: 0;
			}
		}

		@keyframes fadeout {
			from {
				bottom: 30px;
				opacity: 1;
			}

			to {
				bottom: 0;
				opacity: 0;
			}
		}
	</style>

	<?php
	if ($_GET['id'] && !empty($_GET['id'])) {
		$id = $_GET['id'];
	} else {

		if ($_GET['id'] == 0) {

			$id = $_GET['id'];

		} else {

			$id = "all";
		}


	}


	if ($id == 0) {

		$show = 'Pengaduan Menunggu';
	} else if ($id == 1) {

		$show = 'Pengaduan Proses';
	} else if ($id == 2) {
		$show = 'Pengaduan Finished';

	} else if ($id == 3) {
		$show = 'Pengaduan Pending';

	} else if ($id == 4) {
		$show = 'Pengaduan Closed';

	} else if ($id == 5) {
		$show = 'Pengaduan Cancel';

	} else if ($id == "all") {
		$show = 'Semua Pengaduan';

	}
	?>

</head>

<body onload="getLocation();">
	<div id="snackbar"></div>


	<div id="wrapper">
		<!-- Navigation -->
		<?php include "menu.php"; ?>
		<div id="page-wrapper">
			<div class="col-md-12 graphs">
				<div class="xs">
					<h3><?php echo $show; ?></h3>
					<div class="bs-example4" data-example-id="simple-responsive-table">
						<div class="table-responsive">


							<form class="form-inline" action="" method="GET">


								<?php if ($_GET['tgl_awal'] && !empty($_GET['tgl_awal']) && $_GET['tgl_akhir'] && !empty($_GET['tgl_akhir'])) { ?>

									<b>Tanggal pengaduan dari <?php echo $_GET['tgl_awal']; ?> s.d
										<?php echo $_GET['tgl_akhir']; ?></b>

								<?php } else { ?>
									<b>Tanggal pengaduan semua tanggal</b>

								<?php } ?>
								<br>

								<div class="form-group" style="padding: 10px">




									<label for="nama">Tgl Awal :</label>

									<?php if ($_GET['tgl_awal'] && !empty($_GET['tgl_awal'])) { ?>

										<input type="date" name="tgl_awal" class="form-control"
											value="<?php echo $_GET['tgl_awal']; ?>" />

									<?php } else { ?>
										<input type="date" name="tgl_awal" class="form-control"
											value="<?php echo date('Y-m-01'); ?>" />

									<?php } ?>




									<label for="nama">Tgl Akhir :</label>

									<?php if ($_GET['tgl_akhir'] && !empty($_GET['tgl_akhir'])) { ?>

										<input type="date" name="tgl_akhir" class="form-control"
											value="<?php echo $_GET['tgl_akhir']; ?>" />

									<?php } else { ?>
										<input type="date" name="tgl_akhir" class="form-control"
											value="<?php echo date('Y-m-d'); ?>" />

									<?php } ?>


									<label for="nama">Kategori :</label>

									<?php if ($_GET['kategori'] && !empty($_GET['kategori'])) { ?>

										<select name="kategori" class="form-control cek" required>

											<?php
											$getnt = mysqli_query($koneksi, "SELECT * FROM master_kategori where id = '" . $_GET['kategori'] . "'");
											$getnamatoko = mysqli_fetch_array($getnt);
											?>

											<?php if ($_GET['kategori'] != 'all') { ?>
												<option value="<?php echo $_GET['kategori']; ?>">
													<?php echo $getnamatoko['kategori']; ?>
												</option>
												<option value="all">All</option>
											<?php } else { ?>

												<option value="all">All</option>
											<?php } ?>


											<?php
											$toko = mysqli_query($koneksi, "SELECT * FROM master_kategori order by kategori asc");
											while ($rtoko = mysqli_fetch_array($toko)) { ?>
												<option value="<?php echo $rtoko['id']; ?>"><?php echo $rtoko['kategori']; ?>
												</option>
											<?php } ?>
										</select>

									<?php } else { ?>
										<select name="kategori" class="form-control cek" required>
											<option value="all">All</option>

											<?php
											$toko = mysqli_query($koneksi, "SELECT * FROM master_kategori order by kategori asc");
											while ($rtoko = mysqli_fetch_array($toko)) { ?>
												<option value="<?php echo $rtoko['id']; ?>"><?php echo $rtoko['kategori']; ?>
												</option>
											<?php } ?>
										</select>





									<?php } ?>


									<label for="nama">Area :</label>
									<select name="area" class="form-control cek">
										<option value="all">All</option>

										<?php
										$area = mysqli_query($koneksi, "SELECT * FROM master_area order by nama_area asc");
										while ($rarea = mysqli_fetch_array($area)) {
											$selected = "";

											if ($rarea['id_area'] == $_GET['area']) {

												$selected = " selected";
											}

											?>
											<option value="<?php echo $rarea['id_area']; ?>" <?php echo $selected; ?>>
												<?php echo $rarea['nama_area']; ?>
											</option>
										<?php } ?>
									</select>



									<br><br>

									<label for="nama">Toko :</label>

									<?php if ($_GET['toko'] && !empty($_GET['toko'])) { ?>

										<select name="toko" class="form-control cek" required>

											<?php
											$gettoko = mysqli_query($koneksi2, "SELECT * FROM master_store where store_code = '" . $_GET['toko'] . "'");
											$getnamatokos = mysqli_fetch_array($gettoko);
											?>
											<?php if ($_GET['toko'] != 'all') { ?>
												<?php

												if ($_GET['toko'] == 'BOSDC0') {

													echo '<option value="BOSDC0">DISTRIBUTION CENTER</option><option value="all">All</option>';
												} else if ($_GET['toko'] == 'HO') {


													echo '<option value="HO">HEAD OFFICE</option><option value="all">All</option>';
												} else {

													echo '<option value="' . $_GET['toko'] . '">' . $getnamatokos['store_name'] . '</option><option value="all">All</option>';
												} ?>

											<?php } else { ?>

												<option value="all">All</option>
											<?php } ?>



											<?php $toko = mysqli_query($koneksi2, "SELECT * FROM master_store order by store_name asc");
											while ($rtoko = mysqli_fetch_array($toko)) { ?>
												<option value="<?php echo $rtoko['store_code']; ?>">
													<?php echo $rtoko['store_name']; ?>
												</option>
											<?php } ?>
											<option value="HO">HEAD OFFICE</option>
											<option value="BOSDC0">DISTRIBUTION CENTER</option>
										</select>



									<?php } else { ?>
										<select name="toko" class="form-control cek">
											<option value="all">All</option>

											<?php
											$toko = mysqli_query($koneksi2, "SELECT * FROM master_store order by store_name asc");
											while ($rtoko = mysqli_fetch_array($toko)) { ?>
												<option value="<?php echo $rtoko['store_code']; ?>">
													<?php echo $rtoko['store_name']; ?>
												</option>
											<?php } ?>

											<option value="HO">HEAD OFFICE</option>
											<option value="BOSDC0">DISTRIBUTION CENTER</option>


										</select>

									<?php } ?>


									<label for="nama">Status :</label>

									<select name="id" class="form-control cek">

										<?php if ($_GET['id'] && !empty($_GET['id'])) {


											if ($_GET['id'] == 0) {
												// $tanggal = $row['tgl_komplain'];
												$stat = "Menunggu";
											} else if ($_GET['id'] == 1) {
												// $tanggal = $row['tgl_proses'];
												$stat = "Pengerjaan";
											} else if ($_GET['id'] == 2) {
												// $tanggal = $row['tgl_selesai'];
												$stat = "Selesai";

											} else if ($_GET['id'] == 3) {
												// $tanggal = $row['tgl_pending'];
												$stat = "Budget & Survey";

											} else if ($_GET['id'] == 4) {
												// $tanggal = $row['tgl_closing'];
												$stat = "Closed";

											} else if ($_GET['id'] == 5) {
												// $tanggal = $row['tgl_closing'];
												$stat = "Rencana";

											} else if ($_GET['id'] == 6) {
												// $tanggal = $row['tgl_komplain'];
												$stat = "Cancel";

											} else if ($_GET['id'] == 'all') {
												// $tanggal = $row['tgl_komplain'];
												$stat = "All";

											}


											?>

											<option value="<?php echo $_GET['id']; ?>"><?php echo $stat; ?></option>


										<?php } else {

											if ($_GET['id'] === 0) {

												$stat = "Menunggu";
											} else {

												$stat = "-- Pilih Status --";
											}


											echo '<option value="' . $_GET['id'] . '">' . $stat . '</option>';
										}

										?>


										<option value="all">All</option>
										<option value="0">Menunggu</option>
										<option value="5">Rencana</option>
										<option value="6">Cancel</option>
										<option value="3">Budget & Survey</option>
										<option value="1">Pengerjaan</option>
										<option value="2">Selesai</option>
										<option value="4">Closed</option>


									</select>




		
									<label for="nama">Status Leadtime :</label>
									<select name="status_leadtime" class="form-control cek">
										<option value="all">All</option>
										<option value="Urgent" <?php if ($_GET['status_leadtime'] == 'Urgent') {
											echo 'selected';
										} ?>>Urgent</option>
										<option value="Reguler" <?php if ($_GET['status_leadtime'] == 'Reguler') {
											echo 'selected';
										} ?>>Reguler</option>
										<option value="Upgrade" <?php if ($_GET['status_leadtime'] == 'Upgrade') {
											echo 'selected';
										} ?>>Upgrade</option>
									</select>




								</div>
								<br>

								<button type="submit" class="btn btn-primary">Filter</button>

								<a href="export_pengaduan.php" class="btn btn-danger">Excel</a>


							</form>


							<br>
							<table class="table table-bordered" id="table">
    <thead>
        <tr>
            <th style="width: 40px;">No</th>
            <th style="width: 180px;">Informasi Toko & Pengaduan</th>
            <th style="width: 120px;">Lead Time</th>
            <th style="width: 150px;">User Pengaduan</th>
            <th style="width: 200px;">Detail Kerusakan</th>
            <th style="width: 150px;">PIC & Rencana</th>
            <th style="width: 120px;">Dokumentasi</th>
            <th style="width: 150px;">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
		$no = 1;
		$where = [];
		$orderBy = " ORDER BY tgl_komplain DESC";

		// Filter status
		$id = $_GET['id'] ?? 'all';
		if ($id == 'all') {
			$where[] = "status != '' AND status != '8'";
		} else {
			$id_safe = mysqli_real_escape_string($koneksi, $id);
			$where[] = "status = '$id_safe' AND status != '8'";
		}

		// Filter area
		$area = $_GET['area'] ?? '';
		if (!empty($area) && $area !== 'all') {
			$area_safe = mysqli_real_escape_string($koneksi, $area);
			$store_codes = [];

			$store_result = mysqli_query($koneksi, "SELECT store_code FROM master_store WHERE id_area = '$area_safe'");
			while ($store = mysqli_fetch_assoc($store_result)) {
				$store_codes[] = "'" . mysqli_real_escape_string($koneksi, $store['store_code']) . "'";
			}

			if (!empty($store_codes)) {
				$store_list = implode(",", $store_codes);
				$where[] = "store_code IN ($store_list)";
			}
		}

		// Filter tanggal
		$tgl_awal = $_GET['tgl_awal'] ?? date('Y-m-01');
		$tgl_akhir = $_GET['tgl_akhir'] ?? date('Y-m-d');

		$tgl_awal_safe = mysqli_real_escape_string($koneksi, $tgl_awal);
		$tgl_akhir_safe = mysqli_real_escape_string($koneksi, $tgl_akhir);

		$where[] = "DATE(tgl_komplain) BETWEEN '$tgl_awal_safe' AND '$tgl_akhir_safe'";

		// Filter kategori
		$kategori = $_GET['kategori'] ?? '';
		if (!empty($kategori) && $kategori !== 'all') {
			$kategori_safe = mysqli_real_escape_string($koneksi, $kategori);
			$where[] = "kategori_id = '$kategori_safe'";
		}

		// Filter toko
		$toko = $_GET['toko'] ?? '';
		if (!empty($toko) && $toko !== 'all') {
			$toko_safe = mysqli_real_escape_string($koneksi, $toko);
			$where[] = "store_code = '$toko_safe'";
		}

		// Filter status leadtime
		$status_leadtime = $_GET['status_leadtime'] ?? '';
		if (!empty($status_leadtime) && $status_leadtime !== 'all') {
			$status_leadtime_safe = mysqli_real_escape_string($koneksi, $status_leadtime);
			$where[] = "status_leadtime = '$status_leadtime_safe'";
		}

		// Gabungkan semua kondisi
		$where_sql = implode(" AND ", $where);
		$query = "SELECT * FROM komplain WHERE $where_sql $orderBy";


		// echo $query;

		$result = mysqli_query($koneksi, $query);

		while ($row = mysqli_fetch_array($result)) {
			// $tidak_sesuai = "";
			// $get_detail = mysqli_query($koneksi, "SELECT * FROM detail_komplain where komplain_id = '" . $row['komplain_id'] . "'");
			// while ($gd = mysqli_fetch_array($get_detail)) {
			// 	$tidak_sesuai .= $gd['keterangan'] . "<br>";
			// }

			$keterangan = $row['keterangan'];

			$statusdingin = "";
			if ($row['kategori_id'] == 2) {
				if ($row['status_suhu'] == 0) {
					$statusdingin = "<font style='background-color: green; padding: 5px; color: #fff'>Dingin</font>";
				} else if ($row['status_suhu'] == 1) {
					$statusdingin = "<font style='background-color: red; padding: 5px; color: #fff'>Tdk_Dingin</font>";
				}
			}

			$tanggal = $row['tgl_komplain'];
			$stat = getStatusLabel($row['status']);

			if ($row['file_after'] == null) {
				$fa = '-';
			} else {
				$fa = '<img style="width: 100px; cursor: pointer" src="' . $row['file_after'] . '" data-toggle="modal" data-target="#myModalAfter' . $row['komplain_id'] . '">';
			}

			if ($row['file'] == null) {
				$fa1 = '-';
			} else {
				$fa1 = '<img style="width: 100px; cursor: pointer" src="' . $row['file'] . '" data-toggle="modal" data-target="#myModal' . $row['komplain_id'] . '">';
			}

			$nik = mysqli_query($koneksi2, "SELECT nama_lengkap, bagian, nohp FROM employee where nik = '" . $row['nik'] . "'");
			$rn = mysqli_fetch_array($nik);

			$toko = mysqli_query($koneksi2, "SELECT store_name FROM master_store where store_code = '" . $row['store_code'] . "'");
			$rt = mysqli_fetch_array($toko);

			$area = mysqli_query($koneksi, "SELECT nik FROM master_area ma inner join master_store ms on ma.id_area = ms.id_area where ms.store_code = '" . $row['store_code'] . "'");
			$rarea = mysqli_fetch_array($area);

			$pic = mysqli_query($koneksi2, "SELECT nama_lengkap, section FROM master_user where nik = '" . $row['pic'] . "'");
			$rp = mysqli_fetch_array($pic);

			$ck = mysqli_query($koneksi, "select kategori from master_kategori where id = '" . $row['kategori_id'] . "'");
			$rck = mysqli_fetch_array($ck);

			$sc = mysqli_query($koneksi2, "SELECT section FROM master_section where section_code = '" . $rn['bagian'] . "'");
			$rpsc = mysqli_fetch_array($sc);

			if ($row['store_code'] == 'BOSDC0') {
				$sn = 'DC';
			} else if ($row['store_code'] == 'HO') {
				$sn = 'HEAD OFFICE';
			} else {
				$sn = $rt['store_name'];
			}

			$lead_time = "-";
			if ($row['tgl_selesai'] != '') {
				$lead_time = selisihDate(date('Y-m-d', strtotime($row['tgl_komplain'])), date('Y-m-d', strtotime($row['tgl_selesai'])));
			} else {
				$lead_time = selisihDate(date('Y-m-d', strtotime($row['tgl_komplain'])), date('Y-m-d'));
			}

			$nohp = preg_replace('/[^0-9]/', '', $rn['nohp']);
			if (substr($nohp, 0, 1) === '0') {
				$nohp = '62' . substr($nohp, 1);
			}

			$cks = mysqli_query($koneksi, "SELECT * FROM master_kategori WHERE id = '" . $row['kategori_id'] . "'");
			$rcks = mysqli_fetch_array($cks);

			if ($row['status_leadtime'] == 'Urgent') {
				$target_lead_time = $rcks['lead_time_urgent'];
			} elseif ($row['status_leadtime'] == 'Reguler') {
				$target_lead_time = $rcks['lead_time_reguler'];
			} elseif ($row['status_leadtime'] == 'Upgrade') {
				$target_lead_time = $rcks['lead_time_upgrade'];
			} else {
				$target_lead_time = null;
			}
			?>
			<tr>
				<th scope="row"><?php echo $no; ?></th>
			
				<!-- Kolom 1: Informasi Toko & Pengaduan -->
				<td>
					<strong><?php echo $sn; ?></strong><br>
					<?php $arr_leadtime = array('Urgent', 'Upgrade', 'Reguler'); ?>
					<select id="leadtime<?php echo $row['komplain_id']; ?>" onchange="updateStatusLeadtime('<?php echo $row['komplain_id']; ?>')">
						<option value="">Pilih Leadtime</option>
						<?php foreach ($arr_leadtime as $value) {
							$selected = "";
							if ($value == $row['status_leadtime']) {
								$selected = "selected";
							}
							?>
								<option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
						<?php } ?>
					</select>
					<br>
					<small style="color: #000"><strong style="color: #000">Tgl Pengaduan: <br><?php echo $tanggal; ?></strong> </small>
					<br>
					<br>
					<?php echo $stat; ?>
				</td>
			
				<!-- Kolom 2: Lead Time -->
				<td>
					<strong>Target:</strong> <?php echo $target_lead_time; ?> Hari<br>
					<strong>Realisasi:</strong> <?php echo $lead_time; ?> Hari
				</td>
			
				<!-- Kolom 3: User Pengaduan -->
				<td>
					<strong><?php echo $rn['nama_lengkap']; ?></strong><br>
					(<?php echo $rpsc['section']; ?>)<br>
					<a href="https://wa.me/<?php echo $nohp; ?>" target="_blank"><?php echo $rn['nohp']; ?></a>
				</td>
			
				<!-- Kolom 4: Detail Kerusakan -->
				<td>
					<strong>Kategori:</strong> <?php echo $rck['kategori']; ?> 	<?php echo $statusdingin; ?><br>
					<strong>Kerusakan:</strong> <?php echo $row['komplain']; ?><br>
					<strong>Keterangan:</strong> <?php echo $keterangan; ?>
				</td>
			
				<!-- Kolom 5: PIC & Rencana -->
				<td>
					<?php
					$detailkomplain = mysqli_query($koneksi, "SELECT * FROM detail_pic where komplain_id = '" . $row['komplain_id'] . "'");
					while ($dk = mysqli_fetch_array($detailkomplain)) {
						echo "- " . $dk['nama_pic'] . "<br>";
					}

					if ($row['status'] == 0) {
						?>
							<select id="pic<?php echo $row['komplain_id']; ?>">
								<option value="">Pilih PIC</option>
								<?php
								$ge = mysqli_query($koneksi, "SELECT nik,nama_lengkap FROM master_teknisi order by nama_lengkap asc");
								while ($ger = mysqli_fetch_array($ge)) { ?>
										<option value="<?php echo $ger['nik']; ?>"><?php echo $ger['nama_lengkap']; ?></option>
								<?php } ?>
							</select>
							<button onclick="pilihPIC('<?php echo $row['komplain_id']; ?>');">Submit</button>
							<br><br>
					<?php } ?>
				
					<?php if ($row['status'] == 0 || $row['status'] == 5) { ?>
							<?php if ($row['tgl_rencana'] == "") { ?>
									<input type="datetime-local" name="tgl_rencana<?php echo $row['komplain_id']; ?>" id="tgl_rencana<?php echo $row['komplain_id']; ?>" class="form-control" />
									<button onclick="updateRencana(<?php echo $row['komplain_id']; ?>,'<?php echo $rn['nohp']; ?>','<?php echo str_replace(array('\r\n', '\r'), '', $row['komplain']); ?>');" class="btn btn-success" type="button">Submit</button>
							<?php } else { ?>
									<strong>Rencana:</strong> <?php echo $row['tgl_rencana']; ?>
							<?php } ?>
					<?php } ?>
				</td>
			
				<!-- Kolom 6: Dokumentasi -->
				<td>
					<strong>Sebelum:</strong> <?php echo $fa1; ?><br>
					<strong>Setelah:</strong> <?php echo $fa; ?>
				</td>


								<!-- <td>
					<?php
										if ($row['status'] == 0 || $row['status'] == 5) {
											?>
										<button type="button" class="btn btn-success" data-toggle="modal"
											data-target="#myModalProses<?php echo $row['komplain_id']; ?>">Proses</button>
										<?php if ($_COOKIE['nik'] == '212823' || $_COOKIE['section'] == 'S144') { ?>
										<?php } else { ?>
											<button type="button" class="btn btn-danger" data-toggle="modal"
												data-target="#myModalCancel<?php echo $row['komplain_id']; ?>">Cancel</button>
										<?php } ?>
								
									<?php } else if ($row['status'] == 1) { ?>
											<button class="btn btn-primary"
												onclick="cetakForm('<?php echo $rn['nama_lengkap']; ?>', '<?php echo $sn; ?>','<?php echo $row['tgl_komplain']; ?>','<?php echo $row['komplain']; ?>','<?php echo $rp['nama_lengkap']; ?>','<?php echo $rpsc['section']; ?>','<?php echo $pa['nama_lengkap']; ?>');">Cetak
												Form</button>
											<button type="button" class="btn btn-success" data-toggle="modal"
												onclick="getLat(<?php echo $row['komplain_id']; ?>);"
												data-target="#myModals<?php echo $row['komplain_id']; ?>">Finishing</button>
								
									<?php } else if ($row['status'] == 2) { ?>
										<?php if ($row['kategori_id'] == "2" && $row['status_suhu'] == "1") { ?>
													<button class="btn btn-primary" onclick="dinginAc(<?php echo $row['komplain_id']; ?>);">Dingin</button>
										<?php } ?>
										<?php if ($_COOKIE['nik'] == '212823' || $_COOKIE['section'] == 'S144' || $_COOKIE['section'] == 'S161') { ?>
										<?php } ?>
								
									<?php } else if ($row['status'] == 3) { ?>
													<button type="button" class="btn btn-success" data-toggle="modal"
														data-target="#myModalProses<?php echo $row['komplain_id']; ?>">Proses</button>
										<?php if ($_COOKIE['nik'] == '212823' || $_COOKIE['section'] == 'S144') { ?>
										<?php } else { ?>
														<button type="button" class="btn btn-danger" data-toggle="modal"
															data-target="#myModalCancel<?php echo $row['komplain_id']; ?>">Cancel</button>
										<?php } ?>
								
									<?php } else if ($row['status'] == 4) { ?>
														<select name="kategori<?php echo $row['komplain_id']; ?>" id="kategori<?php echo $row['komplain_id']; ?>"
															class="form-control" required>
												<?php
												if ($row['kategori_id'] != "") {
													$kat = mysqli_query($koneksi, "SELECT * FROM master_kategori where id = '" . $row['kategori_id'] . "'");
													$pk = mysqli_fetch_array($kat);
													echo '<option value="' . $row['kategori_id'] . '">' . $pk['kategori'] . '</option>';
												} else { ?>
																<option value="">Pilih Kategori</option>
											<?php } ?>
											<?php $petugas = mysqli_query($koneksi, "SELECT * FROM master_kategori order by kategori asc");
											while ($pt = mysqli_fetch_array($petugas)) { ?>
																<option value="<?php echo $pt['id']; ?>"><?php echo $pt['kategori']; ?></option>
											<?php } ?>
														</select>
														<button class="btn btn-success" onclick="ubahKategori(<?php echo $row['komplain_id']; ?>);">Update</button>
										<?php if ($row['kategori_id'] == "2" && $row['status_suhu'] == "1") { ?>
															<button class="btn btn-primary" onclick="dinginAc(<?php echo $row['komplain_id']; ?>);">Dingin</button>
										<?php } ?>
								
									<?php } else if ($row['status'] == 6) { ?>
										<?php if ($_COOKIE['nik'] == '212823' || $_COOKIE['section'] == 'S144' || $_COOKIE['section'] == 'S161') { ?>
																<button type="button" class="btn btn-danger" data-toggle="modal"
																	data-target="#myModalDelete<?php echo $row['komplain_id']; ?>">Delete</button>
										<?php } ?>
									<?php } ?>
								</td> -->
			
				<!-- Kolom 7: Aksi -->

			<!-- Kolom 7: Aksi -->
			<td>
				<?php
					// Tombol aksi berdasarkan status
					$showProsesButton = in_array($row['status'], [0, 7, 3, 1, 2]);

					if ($showProsesButton) {
						// Label tombol berdasarkan status saat ini
						$buttonLabels = [
							0 => 'Proses ke Survey',
							7 => 'Proses ke Budgeting',
							3 => 'Proses ke Work In Progress',
							1 => 'Proses ke Finished',
							2 => 'Close Pengaduan'
						];
						$buttonLabel = $buttonLabels[$row['status']] ?? 'Proses Status';
						?>
					<button type="button" class="btn btn-success" data-toggle="modal"
						data-target="#myModalProses<?php echo $row['komplain_id']; ?>">
						<?php echo $buttonLabel; ?>
					</button>
					<?php
					}

					// Tombol khusus untuk AC dingin
					if ($row['status'] == 1 && $row['kategori_id'] == "2" && $row['status_suhu'] == "1") {
						?>
					<button class="btn btn-primary" onclick="dinginAc(<?php echo $row['komplain_id']; ?>);">Dingin</button>
					<?php
					}

					// Hapus tombol Cancel terpisah karena sudah ada di modal proses
					?>
			</td>
			</tr>

			<!-- Modal untuk gambar -->
			<div class="modal fade" id="myModal<?php echo $row['komplain_id']; ?>" role="dialog">
				<div class="modal-dialog">
					<img style="width: 800px" src="<?php echo $row['file']; ?>">
				</div>
			</div>
			<div class="modal fade" id="myModalAfter<?php echo $row['komplain_id']; ?>" role="dialog">
				<div class="modal-dialog">
					<img style="width: 800px" src="<?php echo $row['file_after']; ?>">
				</div>
			</div>

			<?php
			// Include modal untuk proses, cancel, delete sesuai kebutuhan
			include 'modal_proses.php'; // Anda perlu memindahkan modal ke file terpisah
			$no++;
		} ?>
    </tbody>
</table>
						</div><!-- /.table-responsive -->
					</div>
				</div>

				<div class="copy_layout">
					<p>Copyright © 2021 PT Idola Cahaya Semesta. All Rights Reserved </p>
				</div>
			</div>
		</div>
		<!-- /#page-wrapper -->
	</div>
	<!-- /#wrapper -->
	<!-- Nav CSS -->
	<link href="css/custom.css" rel="stylesheet">
	<!-- Metis Menu Plugin JavaScript -->
	<script src="js/metisMenu.min.js"></script>
	<script src="js/custom.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
	<script>
		$(document).ready(function () {
			$('#table').DataTable();
			$('.cek').select2();
		});

		function pilihPIC(komplain_id) {
			var pic = document.getElementById("pic" + komplain_id).value;

			$.ajax({
				url: "action.php?modul=komplain&act=inputdetail", //cek member jika ada kirim otp
				type: "POST",
				data: {
					komplain_id: komplain_id,
					pic: pic,
				},
				cache: false,
				success: function (dataResult) {
					var dataResult = JSON.parse(dataResult);
					if (dataResult.result == '1') {
						// $("#overlay").fadeOut(300);

						// $('#success').html("Maaf, nomor/password salah, coba dicek lagi");
						var x = document.getElementById("snackbar");
						x.className = "show";
						x.innerHTML = "Berhasil pilih pic";
						setTimeout(function () { x.className = x.className.replace("show", ""); }, 3000);
						// $( "#table" ).load( " #table" );

						location.reload();
						// $("#table").DataTable().ajax.reload(null, false); 

					}
					else {
						var x = document.getElementById("snackbar");
						x.className = "show";
						x.innerHTML = "Gagal pilih pic";
						setTimeout(function () { x.className = x.className.replace("show", ""); }, 3000);
						// $("#table").DataTable().ajax.reload(null, false); 
					}

				}
			});

		}

		function getLat(id) {
			// alert(id);
			document.getElementById("lat" + id).value = localStorage.getItem("lat");
			document.getElementById("lng" + id).value = localStorage.getItem("lng");


		}
		function getLocation() {


			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(showPosition);
			} else {
				view.innerHTML = "Yah browsernya ngga support Geolocation bro!";
			}
		}

		function showPosition(position) {
			// location.href("komplain.php?lat="+position.coords.latitude+"lng="+position.coords.longitude);
			// alert("test");
			// alert("Lokasi anda "+ position.coords.latitude+', '+position.coords.longitude);

			localStorage.setItem("lat", position.coords.latitude);
			localStorage.setItem("lng", position.coords.longitude);
			// document.getElementById("lat").value = position.coords.latitude;
			// document.getElementById("lng").value = position.coords.longitude;
			// document.getElementById("myText").value = "Johnny Bravo";

		}

		function sendFollowup(komplain_id, nohp, komplain, toko, tgl) {

			$.ajax({
				url: "action.php?modul=komplain&act=followup", //cek member jika ada kirim otp
				type: "POST",
				data: {
					komplain_id: komplain_id,
					nohp: nohp,
					komplain: komplain,
					toko: toko,
					tanggal: tgl,
				},
				cache: false,
				success: function (dataResult) {
					var dataResult = JSON.parse(dataResult);
					if (dataResult.result == '1') {
						// $("#overlay").fadeOut(300);

						// $('#success').html("Maaf, nomor/password salah, coba dicek lagi");
						var x = document.getElementById("snackbar");
						x.className = "show";
						x.innerHTML = "Berhasil request closing";
						setTimeout(function () { x.className = x.className.replace("show", ""); }, 3000);
						$("#table").load(" #table");


					}
					else {
						var x = document.getElementById("snackbar");
						x.className = "show";
						x.innerHTML = "Nomor tidak ditemukan / Hari ini sudah request";
						setTimeout(function () { x.className = x.className.replace("show", ""); }, 3000);
						$("#table").load(" #table");
					}

				}
			});

		}


		function cancelOrder(komplain_id, status) {
			var alasan = $("#alasan" + komplain_id).val();

			if (alasan != "") {
				$.ajax({
					url: "action.php?modul=komplain&act=cancel", //cek member jika ada kirim otp
					type: "POST",
					data: {
						komplain_id: komplain_id,
						status: status,
						alasan: alasan,
					},
					cache: false,
					success: function (dataResult) {
						console.log(dataResult);
						var dataResult = JSON.parse(dataResult);
						if (dataResult.result == '1') {
							var x = document.getElementById("snackbar");
							x.className = "show";
							x.innerHTML = "Berhasil Cancel Komplain";
							setTimeout(function () { x.className = x.className.replace("show", ""); }, 3000);
							$("#table").load(" #table");


							location.reload();

						}
						else {
							alert("Gagal cancel Komplain");
						}

					}
				});

			} else {

				alert("Alasan harus diisi");

			}




		}

		function dinginAc(komplain_id) {


			$.ajax({
				url: "action.php?modul=komplain&act=dingin", //cek member jika ada kirim otp
				type: "POST",
				data: {
					komplain_id: komplain_id,
				},
				cache: false,
				success: function (dataResult) {
					var dataResult = JSON.parse(dataResult);
					if (dataResult.result == '1') {
						var x = document.getElementById("snackbar");
						x.className = "show";
						x.innerHTML = "Berhasil Dinginkan AC";
						setTimeout(function () { x.className = x.className.replace("show", ""); }, 3000);
						$("#table").load(" #table");
						location.reload();

					}
					else {
						alert("Gagal Dinginkan AC");
					}

				}
			});


		}



		// function changeOrder(komplain_id) {
		// 	var proses = $("#proses" + komplain_id).val();

		// 	// alert(id_kategori);

		// 	if (proses != '') {
		// 		$.ajax({
		// 			url: "action.php?modul=komplain&act=proses", //cek member jika ada kirim otp
		// 			type: "POST",
		// 			data: {
		// 				komplain_id: komplain_id,
		// 				status: proses,
		// 			},
		// 			cache: false,
		// 			success: function (dataResult) {
		// 				var dataResult = JSON.parse(dataResult);
		// 				if (dataResult.result == '1') {
		// 					var x = document.getElementById("snackbar");
		// 					x.className = "show";
		// 					x.innerHTML = "Berhasil Proses Komplain";
		// 					setTimeout(function () { x.className = x.className.replace("show", ""); }, 3000);
		// 					$("#table").load(" #table");
		// 					location.reload();

		// 				}
		// 				else {
		// 					alert("Gagal input user");
		// 				}

		// 			}
		// 		});


		// 	} else {
		// 		alert("Status harus dipilih");
		// 	}
		// }

		function approveOrder(komplain_id, status) {
			var id_kategori = $("#kategori" + komplain_id).val();

			// alert(id_kategori);

			if (id_kategori != '') {
				$.ajax({
					url: "action.php?modul=komplain&act=proses", //cek member jika ada kirim otp
					type: "POST",
					data: {
						komplain_id: komplain_id,
						status: status,
						id_kategori: id_kategori,
					},
					cache: false,
					success: function (dataResult) {
						var dataResult = JSON.parse(dataResult);
						if (dataResult.result == '1') {
							var x = document.getElementById("snackbar");
							x.className = "show";
							x.innerHTML = "Berhasil Proses Komplain";
							setTimeout(function () { x.className = x.className.replace("show", ""); }, 3000);
							$("#table").load(" #table");
							location.reload();

						}
						else {
							alert("Gagal input user");
						}

					}
				});


			} else {
				alert("Kategori harus dipilih");
			}
		}

		function ubahKategori(komplain_id) {
			var id_kategori = $("#kategori" + komplain_id).val();

			// alert(id_kategori);

			if (id_kategori != '') {
				$.ajax({
					url: "action.php?modul=komplain&act=ubahkategori", //cek member jika ada kirim otp
					type: "POST",
					data: {
						komplain_id: komplain_id,
						id_kategori: id_kategori,
					},
					cache: false,
					success: function (dataResult) {
						var dataResult = JSON.parse(dataResult);
						if (dataResult.result == '1') {
							var x = document.getElementById("snackbar");
							x.className = "show";
							x.innerHTML = "Berhasil Proses Komplain";
							setTimeout(function () { x.className = x.className.replace("show", ""); }, 3000);
							$("#table").load(" #table");
							// location.reload();

						}
						else {
							alert("Gagal ubah");
						}

					}
				});


			} else {
				alert("Kategori harus dipilih");
			}
		}

		// updateUrgent
		function updateUrgent(komplain_id) {
			var urgent = $("#urgent" + komplain_id).val();

			// alert(id_kategori);

			$.ajax({
				url: "action.php?modul=komplain&act=ubahurgent", //cek member jika ada kirim otp
				type: "POST",
				data: {
					komplain_id: komplain_id,
					urgent: urgent,
				},
				cache: false,
				success: function (dataResult) {
					var dataResult = JSON.parse(dataResult);
					if (dataResult.result == '1') {
						var x = document.getElementById("snackbar");
						x.className = "show";
						x.innerHTML = dataResult.msg;
						setTimeout(function () { x.className = x.className.replace("show", ""); }, 3000);
						//  $( "#table" ).load( " #table" );
						// location.reload();

					}
					else {
						alert("Gagal input user");
					}

				}
			});
		}
		function updateStatusLeadtime(komplain_id) {
			var leadtime = $("#leadtime" + komplain_id).val();

			// alert(id_kategori);

			$.ajax({
				url: "action.php?modul=komplain&act=ubahstatusleadtime", //cek member jika ada kirim otp
				type: "POST",
				data: {
					komplain_id: komplain_id,
					leadtime: leadtime,
				},
				cache: false,
				success: function (dataResult) {
					var dataResult = JSON.parse(dataResult);
					if (dataResult.result == '1') {
						var x = document.getElementById("snackbar");
						x.className = "show";
						x.innerHTML = dataResult.msg;
						setTimeout(function () { x.className = x.className.replace("show", ""); }, 3000);
						//  $( "#table" ).load( " #table" );
						// location.reload();

					}
					else {
						alert("Gagal input user");
					}

				}
			});
		}




		function updateRencana(komplain_id, nohp, komplain) {
			var tgl_rencana = $("#tgl_rencana" + komplain_id).val();

			// alert(id_kategori);

			if (tgl_rencana != '') {
				$.ajax({
					url: "action.php?modul=komplain&act=ubahrencana", //cek member jika ada kirim otp
					type: "POST",
					data: {
						komplain_id: komplain_id,
						tgl_rencana: tgl_rencana,
						nohp: nohp,
						komplain: komplain,
					},
					cache: false,
					success: function (dataResult) {
						var dataResult = JSON.parse(dataResult);
						if (dataResult.result == '1') {
							var x = document.getElementById("snackbar");
							x.className = "show";
							x.innerHTML = dataResult.msg;
							setTimeout(function () { x.className = x.className.replace("show", ""); }, 3000);
							$("#table").load(" #table");
							// location.reload();

						}
						else {
							var x = document.getElementById("snackbar");
							x.className = "show";
							x.innerHTML = dataResult.msg;
							setTimeout(function () { x.className = x.className.replace("show", ""); }, 3000);
						}

					}
				});


			} else {
				alert("Tanggal harus diisi");
			}
		}



		function pendingOrder(komplain_id, status) {

			$.ajax({
				url: "action.php?modul=komplain&act=pending", //cek member jika ada kirim otp
				type: "POST",
				data: {
					komplain_id: komplain_id,
					status: status,
				},
				cache: false,
				success: function (dataResult) {
					var dataResult = JSON.parse(dataResult);
					if (dataResult.result == '1') {
						// $("#overlay").fadeOut(300);

						// $('#success').html("Maaf, nomor/password salah, coba dicek lagi");
						var x = document.getElementById("snackbar");
						x.className = "show";
						x.innerHTML = "Berhasil Proses Komplain";
						setTimeout(function () { x.className = x.className.replace("show", ""); }, 3000);
						$("#table").load(" #table");


					}
					else {
						// $("#overlay").fadeOut(300);

						// $('#success').html("Maaf, nomor/password salah, coba dicek lagi");
						alert("Gagal input user");
					}

				}
			});

		}



		function deleteKomplain(komplain_id) {

			$.ajax({
				url: "action.php?modul=komplain&act=delete", //cek member jika ada kirim otp
				type: "POST",
				data: {
					komplain_id: komplain_id,
				},
				cache: false,
				success: function (dataResult) {
					var dataResult = JSON.parse(dataResult);
					if (dataResult.result == '1') {
						// $("#overlay").fadeOut(300);
						$('.modal').modal('hide');
						// $('#success').html("Maaf, nomor/password salah, coba dicek lagi");
						var x = document.getElementById("snackbar");
						x.className = "show";
						x.innerHTML = "Berhasil Delete Komplain";
						setTimeout(function () { x.className = x.className.replace("show", ""); }, 3000);
						$("#table").load(" #table");


					}
					else {
						// $("#overlay").fadeOut(300);

						// $('#success').html("Maaf, nomor/password salah, coba dicek lagi");
						alert("Gagal input user");
					}

				}
			});

		}

		function approveOrderFinish(id, status) {

			const fileupdate = $('#fileupdate' + id).prop('files')[0];
			const tgl_supervisi = $('#tgl_supervisi' + id).val;

			// var id = document.querySelector('input[name="alt_code"]').value;
			let formData = new FormData();
			formData.append('fileupdate', fileupdate);
			formData.append('komplain_id', id);
			formData.append('tgl_supervisi', tgl_supervisi);
			formData.append('status', status);

			$.ajax({
				xhr: function () {
					var xhr = new window.XMLHttpRequest();
					xhr.upload.addEventListener("progress", function (evt) {
						if (evt.lengthComputable) {
							var percentComplete = ((evt.loaded / evt.total) * 100);
							$("#progress-bar" + id).width(percentComplete + '%');
							$("#progress-bar" + id).html(percentComplete + '%');
						}
					}, false);
					return xhr;
				},
				type: 'POST',
				url: "action.php?modul=komplain&act=finish",
				data: formData,
				cache: false,
				processData: false,
				contentType: false,
				success: function (dataResult) {
					var dataResult = JSON.parse(dataResult);
					if (dataResult.result == '1') {
						$("#notif" + id).html("<p id='load-font' style='color: green'><b>File berhasil diupload</b></p>");
						$("#file-loads" + id).load(" #file-loads" + id);
						$("#reload" + id).load(" #reload" + id);
					}
					else {
						$("#notif" + id).html("<p id='load-font' style='color: red'><b>File Gagal diupload</b></p>");

					}


				},
				error: function () {
				}
			});

		}

		function cetakForm(nama, store, tgl, komplain, pic, jabatan, picarea) {

			// alert(nama+" "+store+" "+tgl+" "+komplain+" "+pic+" "+jabatan+" "+picarea);

			setTimeout(function () {


				let text = "";
				text += "<center>";
				text += "<table style='border:0px'>";

				text += "<tr>";
				text += "<td colspan='3'><font style='font-size:25px'><center><b>PT. IDOLA CAHAYA SEMESTA</b><center></td>";
				text += "</tr>";

				text += "<tr>";
				text += "<td>Office : Jl. Raya Jati Bening No. 60A (Depan Sentra Kota), Jati Bening, Pondok Gede, Bekasi 17412<br>Tlp : (021) 296 808 38 / 296 808 39 / 296 808 40, Fax : 021 - 84 900 387</td>";
				text += "</tr>";
				text += "</table>";
				text += "</center>";

				text += "<hr>";
				text += "<center><h2>FORM PENGADUAN GA</h2></center>";


				text += "<table style='border:0px; width:100%; font-size:13px; border-right:1px solid #000;border-left:1px solid #000;border-top:1px solid #000;';>"


				text += "<tr>";
				text += "<td>Nama</td>";
				text += "<td>:</td>";
				text += "<td>" + nama + " (" + jabatan + ")</td>";
				text += "</tr>";

				text += "<tr>";
				text += "<td>Outlet/Dept</td>";
				text += "<td>:</td>";
				text += "<td>" + store + "</td>";
				text += "</tr>";

				text += "<tr>";
				text += "<td>Tanggal</td>";
				text += "<td>:</td>";
				text += "<td>" + tgl + "</td>";
				text += "</tr>";

				text += "<tr>";
				text += "<td>Pengaduan User</td>";
				text += "<td>:</td>";
				text += "<td>" + komplain + "</td>";
				text += "</tr>";

				text += "<tr>";
				text += "<td>Area</td>";
				text += "<td>:</td>";
				text += "<td>" + picarea + "</td>";
				text += "</tr>";


				text += "</table>";//penutup td 2

				text += "<table style='width:100%; border:1px solid black;font-size:12px'>";
				text += "<tr>";
				text += "<td><center>Diterima oleh<br><br><br><br><br></center></td>";
				text += "<td><center>Dibuat oleh<br><br><br><br><br></center></td>";
				text += "</tr>";

				text += "<tr>";
				text += "<td style='width:50%'><center>(" + pic + ")</center></td>";
				text += "<td style='width:50%'><center>(.........................................)</center></td>";
				text += "</tr>";

				text += "<tr>";
				text += "<td style='width:50%'><center></center></td>";
				text += "<td style='width:50%'><center></center></td>";
				text += "</tr>";

				text += "</table>";


				var mywindow = window.open('', 'my div', 'height=600,width=800');
				/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
				mywindow.document.write('<style>@media print {@page { margin: 0; }body { margin: 1.6cm; }}</style>');
				mywindow.document.write(text);


				mywindow.print();

				// mywindow.print();
				// mywindow.close();

				// return true;

			}, 100);



		}




		$('#butsave').on('click', function () {

			var product_name = $('#product_name').val();
			var cat_id = $('#cat_id').val();
			var stock = $('#stock').val();
			var image = $('#image')[0].files[0];


			var formData = new FormData();
			formData.append('image', image);
			formData.append('product_name', product_name);
			formData.append('cat_id', cat_id);
			formData.append('stock', stock);



			if (product_name != "" || cat_id != "" || stock != "" || image.length > 0) {
				$("#overlay").fadeIn(300);
				$.ajax({
					url: "action.php?modul=product&act=input", //cek member jika ada kirim otp
					type: "POST",
					data: formData,
					processData: false,  // tell jQuery not to process the data
					contentType: false,  // tell jQuery not to set contentType
					success: function (dataResult) {
						var dataResult = JSON.parse(dataResult);
						if (dataResult.result == '1') {
							// $("#overlay").fadeOut(300);

							// $('#success').html("Maaf, nomor/password salah, coba dicek lagi");
							var x = document.getElementById("snackbar");
							x.className = "show";
							setTimeout(function () { x.className = x.className.replace("show", ""); }, 3000);
							$("#table").load(" #table");


						}
						else {
							// $("#overlay").fadeOut(300);

							// $('#success').html("Maaf, nomor/password salah, coba dicek lagi");
							alert("Gagal input");
						}

					}
				});
			}
			else {
				alert("Lengkapi isian dulu..");
			}
		});


		

function changeOrder(komplainId) {
    var status = $('#proses' + komplainId).val();
    var nominal = $('#nominal' + komplainId).val();
    var photoFile = $('#photoFinished' + komplainId)[0].files[0];
    var alasan = $('#alasan' + komplainId).val();
    
    // Validasi berdasarkan status
    if (status == '3' && (!nominal || nominal <= 0)) {
        alert('Harap masukkan nominal budget yang valid untuk status Budgeting');
        return;
    }
    
    if (status == '2' && !photoFile) {
        alert('Harap upload foto untuk status Finished by Project/GA');
        return;
    }
    
    if (status == '6' && !alasan) {
        alert('Harap masukkan alasan cancel');
        return;
    }
    
    // Konfirmasi pesan berbeda berdasarkan aksi
    var confirmMessage = '';
    if (status == '6') {
        confirmMessage = 'Apakah anda yakin membatalkan pengaduan ini?';
    } else {
        confirmMessage = 'Apakah anda yakin mengubah status menjadi: ' + $('#proses' + komplainId + ' option:selected').text() + '?';
    }
    
    // Buat form data untuk upload file
    var formData = new FormData();
    formData.append('komplain_id', komplainId);
    formData.append('status', status);
    formData.append('nominal', nominal || '');
    formData.append('alasan', alasan || '');
    
    if (photoFile) {
        formData.append('photo_finished', photoFile);
    }
    
    if (confirm(confirmMessage)) {
        $.ajax({
            url: 'action.php?modul=komplain&act=change_status',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response === 'success') {
                    alert('Status berhasil diubah');
                    location.reload();
                } else {
                    alert('Error: ' + response);
                }
            },
            error: function() {
                alert('Terjadi kesalahan');
            }
        });
    }
}


	</script>


</body>

</html>