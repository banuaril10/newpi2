<?php include "../config/koneksi.php"; ?>
<?php include "../components/main.php"; ?>
<?php include "../components/sidebar.php"; ?>
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
<?php include "../components/hhh.php"; ?>

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


			
					<table class="table table-bordered table-hover" id="example">
						<thead>
							<tr>
								<th></th>
								<th>No</th>
								<th>SKU</th>
								<th>Name</th>
								<th>QTY</th>
								<th>Amount</th>
								<th>Price Discount</th>
								<th>Rack Name</th>
								<th>Tag</th>
							</tr>
						</thead>
						<tbody>
						

						</tbody>
					</table>
					
					
					
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div>


<script type="text/javascript">

</script>
</div>
<?php include "../components/fff.php"; ?>