<?php include "config/koneksi.php"; ?>
<?php include "components/main.php"; ?>
<?php include "components/sidebar.php"; ?>

<?php
$ll = "select * from ad_morg where isactived = 'Y'";
$query = $connec->query($ll);

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
	$idstore = $row['ad_morg_key'];
	$name = $row['name'];
}
?>

<style>
	/* Loading Spinner */
	#overlay {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background: rgba(0,0,0,0.5);
		z-index: 9999;
		display: none;
		justify-content: center;
		align-items: center;
	}
	#overlay.show {
		display: flex;
	}
	.cv-spinner {
		display: flex;
		justify-content: center;
		align-items: center;
	}
	.spinner {
		width: 50px;
		height: 50px;
		border: 5px solid #fff;
		border-top-color: #2196F3;
		border-radius: 50%;
		animation: spin 1s linear infinite;
	}
	@keyframes spin {
		to { transform: rotate(360deg); }
	}
	
	/* PB Input Section */
	.pb-input-section {
		background: #f8f9fa;
		padding: 20px;
		border-radius: 8px;
		margin-bottom: 20px;
	}
	.pb-form-group {
		display: flex;
		gap: 15px;
		align-items: flex-end;
		flex-wrap: wrap;
	}
	.pb-form-group .form-group {
		flex: 1;
		min-width: 250px;
	}
	.pb-form-group label {
		font-weight: bold;
		margin-bottom: 5px;
		display: block;
	}
	.pb-form-group input {
		width: 100%;
		padding: 10px;
		border: 1px solid #ddd;
		border-radius: 5px;
	}
	.btn-view {
		background: #2196F3;
		color: white;
		border: none;
		padding: 10px 25px;
		border-radius: 5px;
		cursor: pointer;
		font-weight: bold;
	}
	.btn-view:hover {
		background: #0b7dda;
	}
	.btn-sync {
		background: #ff9800;
		color: white;
		border: none;
		padding: 10px 25px;
		border-radius: 5px;
		cursor: pointer;
		font-weight: bold;
	}
	.btn-sync:hover {
		background: #e68900;
	}
	.btn-sync:disabled, .btn-view:disabled {
		opacity: 0.6;
		cursor: not-allowed;
	}
	.stock-info {
		margin-top: 20px;
		padding: 15px;
		background: white;
		border-radius: 5px;
		border: 1px solid #ddd;
		overflow-x: auto;
	}
	.stock-info table {
		width: 100%;
	}
	.stock-info th, .stock-info td {
		padding: 10px;
		text-align: left;
	}
	.pb-header-info {
		background: #e3f2fd;
		padding: 10px 15px;
		border-radius: 5px;
		margin-bottom: 15px;
	}
	.badge-success {
		background: #28a745;
		color: white;
		padding: 5px 10px;
		border-radius: 5px;
		font-size: 12px;
	}
	.badge-warning {
		background: #ffc107;
		color: #333;
		padding: 5px 10px;
		border-radius: 5px;
		font-size: 12px;
	}
	.badge-info {
		background: #17a2b8;
		color: white;
		padding: 5px 10px;
		border-radius: 5px;
		font-size: 12px;
	}
	.stock-diff {
		font-weight: bold;
	}
	.stock-diff.positive {
		color: #28a745;
	}
	.stock-diff.negative {
		color: #dc3545;
	}
</style>

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
		
		<!------ CONTENT AREA ------>
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h4>Sync Stok Lokal - PB</h4>
					</div>
					
					<div class="card-body">
						<!-- Section Input Nomor PB -->
						<div class="pb-input-section">
							<h5>Input Nomor PB</h5>
							<div class="pb-form-group">
								<div class="form-group">
									<label>Nomor PB (Purchase Order)</label>
									<input type="text" id="no_pb" class="form-control" placeholder="Contoh: PB-00084601" autocomplete="off">
								</div>
								<div class="form-group">
									<button id="viewBtn" class="btn-view">
										<i class="bi bi-eye"></i> View Data
									</button>
									<button id="syncBtn" class="btn-sync" disabled>
										<i class="bi bi-arrow-repeat"></i> Sync Stock
									</button>
								</div>
							</div>
							
							<!-- Info Store -->
							<div class="mt-3 text-muted">
								<small>Toko: <?php echo $name; ?></small>
							</div>
						</div>
						
						<!-- Hasil Data PB -->
						<div id="pbResult" style="display: none;">
							<div class="pb-header-info">
								<strong>PB Number:</strong> <span id="pb_number_display"></span> &nbsp;|&nbsp;
								<strong>Total Item:</strong> <span id="total_items_display"></span>
							</div>
							<h5>Data PB</h5>
							<div class="stock-info">
								<table class="table table-bordered" id="pbTable">
									<thead>
										<tr>
											<th>SKU</th>
											<th>Barcode</th>
											<th>Nama Barang</th>
											<!-- <th>Qty PB</th> -->
											<th>Stok Lokal</th>
											<th>Stok ERP</th>
											<th>QTY Sales Berjalan</th>
											<!-- <th>Stok Setelah Sync</th> -->
											<th>Last Sync</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody id="pbTableBody">
									</tbody>
								</table>
							</div>
						</div>
						
						<!-- Notifikasi -->
						<div id="notification" style="display: none;" class="alert mt-3"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Variable global untuk menyimpan data PB
var currentPBData = null;
var currentPBNumber = '';
var currentStockData = {}; // Stok lokal pos_mproduct
var currentERPStockData = {}; // Stok ERP
var currentSalesData = {};
$(document).ready(function() {
	
	// Enter key untuk submit
	$('#no_pb').on('keypress', function(e) {
		if(e.which === 13) {
			$('#viewBtn').click();
		}
	});
	
	// Tombol View Data
	$('#viewBtn').on('click', function() {
		var noPB = $('#no_pb').val().trim();
		var idstore = '<?php echo $idstore; ?>';
		
		if(noPB === '') {
			showNotification('Silakan masukkan nomor PB terlebih dahulu!', 'warning');
			return;
		}
		
		// Tampilkan loading
		showLoading(true);
		
		// Sembunyikan hasil sebelumnya
		$('#pbResult').hide();
		$('#notification').hide();
		
		// Panggil API get data PB
		var apiUrl = 'https://api.idolmartidolaku.com/apiidolmart/store/pb/get_data.php?pb_number=' + encodeURIComponent(noPB) + '&idstore=' + encodeURIComponent(idstore);
		
		$.ajax({
			url: apiUrl,
			type: 'GET',
			dataType: 'json',
			success: function(response) {
				if(response.status === 'success') {
					currentPBData = response.data;
					currentPBNumber = response.pb_number;
					
					// Setelah dapat data PB, ambil stok lokal dari database
					getLocalStock(noPB);
				} else {
					showLoading(false);
					showNotification('Gagal: ' + response.message, 'danger');
					$('#syncBtn').prop('disabled', true);
				}
			},
			error: function(xhr, status, error) {
				showLoading(false);
				showNotification('Terjadi kesalahan: ' + error, 'danger');
				$('#syncBtn').prop('disabled', true);
				console.error('Error:', error);
			}
		});
	});
	
	// Fungsi untuk mengambil stok lokal dari tabel pos_mproduct
	function getLocalStock(pbNumber) {
    $.ajax({
        url: 'get_local_stock.php',
        type: 'POST',
        dataType: 'json',
        data: {
            pb_number: pbNumber,
            id_morg: '<?php echo $idstore; ?>'
        },
        success: function(response) {
            if(response.status === 'success') {
                currentStockData = response.data;
                // Setelah dapat stok lokal, ambil stok ERP
                getERPStock();
            } else {
                currentStockData = {};
                getERPStock();
            }
        },
        error: function() {
            currentStockData = {};
            getSales();
        }
    });
}


function getSales() {
		var skus = [];
		$.each(currentPBData, function(i, item) {
			if(item.sku) skus.push(item.sku);
		});
		
		var tanggal = $('#salesDate').val() || new Date().toISOString().split('T')[0];
		
		$.ajax({
			url: 'get_local_sales.php',
			type: 'POST',
			dataType: 'json',
			data: {
				id_morg: '<?php echo $idstore; ?>',
				skus: skus.join(','),
				tanggal: tanggal
			},
			success: function(response) {
				if(response.status === 'success') {
					currentSalesData = response.data;
				} else {
					currentSalesData = {};
				}
				getERPStock();
			},
			error: function() {
				currentSalesData = {};
				getERPStock();
			}
		});
	}


// Fungsi untuk mengambil stok ERP
function getERPStock() {
    // Kumpulkan semua SKU dari PB
    var skus = [];
    $.each(currentPBData, function(i, item) {
        if(item.sku) {
            skus.push(item.sku);
        }
    });
    
    var skuString = skus.join(',');
    var idstore = '<?php echo $idstore; ?>';
    
    $.ajax({
        url: 'https://api.idolmartidolaku.com/apiidolmart/store/items/get_stock_by_sku.php',
        type: 'GET',
        dataType: 'json',
        data: {
            idstore: idstore,
            skus: skuString
        },
        success: function(response) {
            showLoading(false);
            if(response.status === 'success') {
                // Convert ke object dengan key SKU
                currentERPStockData = {};
                $.each(response.data, function(i, item) {
                    currentERPStockData[item.sku] = item.stock;
                });
                displayPBDataWithAllStocks();
                $('#syncBtn').prop('disabled', false);
                showNotification('Data ditemukan!', 'info');
            } else {
                displayPBDataWithAllStocks();
                $('#syncBtn').prop('disabled', true);
                showNotification('Gagal ambil stok ERP', 'warning');
            }
        },
        error: function() {
            showLoading(false);
            displayPBDataWithAllStocks();
            $('#syncBtn').prop('disabled', true);
            showNotification('Gagal ambil stok ERP', 'warning');
        }
    });
}

// Fungsi tampil semua data
function displayPBDataWithAllStocks() {
    $('#pb_number_display').text(currentPBNumber);
    $('#total_items_display').text(currentPBData.length);
    
    var tbody = $('#pbTableBody');
    tbody.empty();
    
    if(currentPBData && currentPBData.length > 0) {
        $.each(currentPBData, function(i, item) {
            var sku = item.sku;
            var localStock = currentStockData[sku] || { stockqty: 0, last_sync: '-' };
            var erpStock = currentERPStockData[sku] || 0;
            var currentLocalStock = localStock.stockqty;
            var newStock = currentLocalStock + (item.qty || 0);
            var lastSync = localStock.last_sync || '-';
            var statusBadge = '<span class="badge-warning">Belum Sync</span>';
             var qtySales = currentSalesData[sku] || 0; // QTY Sales Berjalan
            // Selisih stok lokal vs ERP
            var diff = currentLocalStock - erpStock;
            var diffHtml = diff !== 0 ? (diff > 0 ? 
                '<small class="text-success"> (+' + diff + ')</small>' : 
                '<small class="text-danger"> (' + diff + ')</small>') : '';
            
            var row = '<tr>' +
                '<td>' + (sku || '-') + '</td>' +
                '<td>' + (item.barcode || '-') + '</td>' +
                '<td>' + (item.item_name || '-') + '</td>' +
                '<td>' + currentLocalStock.toLocaleString() + diffHtml + '</td>' +
                '<td>' + erpStock.toLocaleString() + '</td>' +
				'<td>' + qtySales.toLocaleString() + '</td>' +  // QTY Sales Berjalan
                // '<td class="text-success">' + newStock.toLocaleString() + '</td>' +
                '<td>' + lastSync + '</td>' +
                '<td>' + statusBadge + '</td>' +
            '</tr>';
            tbody.append(row);
        });
        $('#pbResult').show();
    }
}
	
	// Tombol Sync Stock
	$('#syncBtn').on('click', function() {
		if(!currentPBData || currentPBData.length === 0) {
			showNotification('Tidak ada data PB yang akan di-sync', 'warning');
			return;
		}
		
		if(confirm('Yakin akan melakukan sync stok lokal untuk PB ' + currentPBNumber + '?\n\nStok akan ditambah sesuai Qty PB.')) {
			showLoading(true);
			
			// Panggil API untuk update stok
			$.ajax({
				url: 'update_local_stock.php',
				type: 'POST',
				dataType: 'json',
				data: {
					pb_number: currentPBNumber,
					id_morg: '<?php echo $idstore; ?>',
					items: JSON.stringify(currentPBData)
				},
				success: function(response) {
					showLoading(false);
					if(response.status === 'success') {
						// Refresh data stok lokal setelah sync
						getLocalStock(currentPBNumber);
						showNotification('Sync stok lokal berhasil!', 'success');
					} else {
						showNotification('Gagal sync: ' + response.message, 'danger');
					}
				},
				error: function(xhr, status, error) {
					showLoading(false);
					showNotification('Terjadi kesalahan saat sync: ' + error, 'danger');
					console.error('Error:', error);
				}
			});
		}
	});
	
	// Fungsi loading
	function showLoading(show) {
		if(show) {
			$('#overlay').addClass('show');
		} else {
			$('#overlay').removeClass('show');
		}
	}
	
	// Fungsi notifikasi
	function showNotification(message, type) {
		var notification = $('#notification');
		notification.removeClass('alert-success alert-danger alert-warning alert-info');
		notification.addClass('alert-' + type);
		notification.html(message);
		notification.show();
		
		setTimeout(function() {
			notification.fadeOut();
		}, 5000);
	}
	
});
</script>

<?php include "components/fff.php"; ?>