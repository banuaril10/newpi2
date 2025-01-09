<?php include "config/koneksi.php"; ?>
<?php include "components/main.php"; ?>
<?php include "components/sidebar.php"; ?>
<style>
.grid-container {
  display: grid;
  grid-template-columns: auto auto auto auto;
  background-color: #2196F3;
  padding: 10px;
}
.grid-item {
  background-color: rgba(255, 255, 255, 0.8);
  border: 1px solid rgba(0, 0, 0, 0.8);
  padding: 20px;
  font-size: 30px;
  text-align: center;
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

<!------ CONTENT AREA ------->
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h4>REPORT SALES ITEM TODAY</h4>
			</div>
			<div class="card-body">
			<div class="tables">			
				<div class="table-responsive bs-example widget-shadow">	
				<p id="notif1" style="color: red; font-weight: bold"></p>		

		
				<div class="form-group">
					<label for="date">Date</label>
					<input type="date" class="form-control" id="date" name="date" value="<?php echo date('Y-m-d'); ?>">
				</div>
				<button type="button" class="btn btn-primary" onclick="search()">Search</button>
				<br><br>



			
					<table class="table table-bordered table-hover" id="example">
						<thead>
							<tr>
								<th>No</th>
								<th>SKU</th>
								<th>Name</th>
								<th>QTY</th>
								<th>Amount</th>
							</tr>
						</thead>
						<tbody id="data">
						

						</tbody>
					</table>
					
					
					
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div>

//print datatable with cdn
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.colVis.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.flash.min.js"></script>



<script type="text/javascript">
	//create datatable sumber data from json api/cyber/report_sales_item.php
	$(document).ready(function() {

		$('#example').DataTable({
			"ajax": "api/cyber/report_sales_item.php",
			"columns": [{
					"data": "no"
				},
				{
					"data": "sku"
				},
				{
					"data": "name"
				},
				{
					"data": "qty"
				},
				{
					"data": "amount"
				},
			],
			"columnDefs": [{
				"targets": 4,
				"orderable": false,
				"searchable": false
			}],
			"order": [
				[0, "asc"]
			],
			"paging": true,
			"lengthChange": true,
			"searching": true,
			"ordering": true,
			"info": true,
			"autoWidth": true,
			"responsive": true,
			"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
		});
	});


	function search(){
		var date = $('#date').val();
		$('#example').DataTable().ajax.url('api/cyber/report_sales_item.php?date='+date).load();
	}

</script>
</div>
<?php include "components/fff.php"; ?>