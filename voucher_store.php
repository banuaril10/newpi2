<?php include "config/koneksi.php"; ?>
<?php include "components/main.php"; ?>
<?php include "components/sidebar.php"; ?>

<div id="overlay">
	<div class="cv-spinner">
		<span class="spinner"></span>
	</div>
</div>

<div id="app">
<div id="main">

<header class="mb-3">
	<a href="#" class="burger-btn d-block d-xl-none">
		<i class="bi bi-justify fs-3"></i>
	</a>
</header>

<?php include "components/hhh.php"; ?>

<!------ CONTENT AREA ------->
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h4>Konfigurasi Voucher per Toko</h4>
				<button type="button" class="btn btn-primary" id="sync" onclick="syncVoucher();">
					<i class="bi bi-arrow-repeat"></i> Sync Data
				</button>
			</div>

			<div class="card-body">
				<p id="notif1" class="fw-bold"></p>
				<input type="text" id="search" class="form-control mb-3" placeholder="Cari toko atau nominal...">

				<div class="table-responsive">
					<table class="table table-bordered table-striped" id="voucherTable">
						<thead class="table-dark text-center">
							<tr>
								<th>No</th>
								<th>ID Store</th>
								<th>Nominal</th>
								<th>Max Qty</th>
								<th>Aktif</th>
								<th>Insert By</th>
								<th>Insert Date</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						$sql = "SELECT * FROM in_config_voucher_store ORDER BY id_store ASC, nominal ASC";
						$no = 1;
						foreach ($connec->query($sql) as $row) {
						?>
							<tr>
								<td><?= $no++; ?></td>
								<td><?= htmlspecialchars($row['id_store']); ?></td>
								<td class="text-end"><?= number_format($row['nominal'], 0, ',', '.'); ?></td>
								<td class="text-center"><?= $row['max_qty']; ?></td>
								<td class="text-center">
									<?= $row['isactive'] ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>'; ?>
								</td>
								<td><?= htmlspecialchars($row['insertby']); ?></td>
								<td><?= $row['insertdate']; ?></td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</div>

			</div>
		</div>
	</div>
</div>
</div>
</div>

<script>
document.getElementById("search").addEventListener("keyup", function() {
	var filter = this.value.toUpperCase();
	var rows = document.querySelector("#voucherTable tbody").rows;
	for (var i = 0; i < rows.length; i++) {
		let td1 = rows[i].cells[1].textContent.toUpperCase();
		let td2 = rows[i].cells[2].textContent.toUpperCase();
		if (td1.indexOf(filter) > -1 || td2.indexOf(filter) > -1) {
			rows[i].style.display = "";
		} else {
			rows[i].style.display = "none";
		}
	}
});

function syncVoucher(){
	$.ajax({
		url: "api/cyber/sync_voucher_store.php",
		type: "GET",
		beforeSend: function(){
			$('#sync').prop('disabled', true);
			$('#notif1').html("<span style='color:red'>Sedang melakukan sinkronisasi, mohon tunggu...</span>");
			$("#overlay").fadeIn(300);
		},
		success: function(result){
			let data = {};
			try { data = JSON.parse(result); } catch(e) {}

			if(data.status === "OK"){
				$('#notif1').html("<span style='color:green'>"+data.message+" (Total: "+data.count+")</span>");
				$("#voucherTable").load(location.href + " #voucherTable>*", "");
			} else {
				$('#notif1').html("<span style='color:red'>"+(data.message || 'Gagal sinkronisasi')+"</span>");
			}
			$('#sync').prop('disabled', false);
			$("#overlay").fadeOut(300);
		},
		error: function(){
			$('#notif1').html("<span style='color:red'>Terjadi kesalahan saat sinkronisasi</span>");
			$('#sync').prop('disabled', false);
			$("#overlay").fadeOut(300);
		}
	});
}
</script>

<?php include "components/fff.php"; ?>
