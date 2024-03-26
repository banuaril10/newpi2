<?php include "config/koneksi.php"; ?>
<?php include "components/main.php"; ?>
<?php include "components/sidebar.php";

?>
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
				<h4>CEK PROMO BUY & GET</h4>
			</div>
			
			 
			
			<div class="card-body">
			
			<button type="button" onclick="syncPromo();" class="btn btn-primary">Sync Promo</button>
			
			<div class="tables">	
			<p id="notif" style="color: red; font-weight: bold"></p>
			<!--<button onclick="turnOn();" class="switch">On</button>
			<button onclick="turnOff();" class="switch1">Off</button>
			
			
			<div id="qr-reader" style="width: 100%"></div>-->
			
				<div class="table-responsive bs-example widget-shadow">	
				
				
				<!--<input type="text" id="search" class="form-control" id="exampleInputName2" placeholder="Search">-->
				
			<table class="table table-striped table-bordered table-sm server" style="width:100%">
            <thead>
              <tr>
               <!-- <th scope="col">No</th>-->
                <th scope="col">Postdate</th>
                <th scope="col">Nama Diskon</th>
				<th scope="col">SKU Beli</th>
				<th scope="col">SKU Dapat</th>
                <th scope="col">Tgl Mulai</th>
                <th scope="col">Tgl Berakhir</th>
                <!--<th scope="col">Harga</th>
                <th scope="col">Harga Diskon</th>-->
              </tr>
            </thead>
            <tbody>
              <!-- List Data Menggunakan DataTable -->             
            </tbody>
          </table>
					
					
					
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div>
<script src="https://unpkg.com/html5-qrcode@2.0.9/dist/html5-qrcode.min.js"></script>
<script type="text/javascript">
  $(function(){
 
           $('.server').DataTable({
              "processing": true,
              "serverSide": true,
              "ajax":{
                       "url": "api/action.php?modul=inventory&act=api_datatable_promo_buyget",
                       "dataType": "json",
                       "type": "POST"
                     },
              "columns": [
                  // { "data": "no" },
                  { "data": "postdate" },
                  { "data": "discountname" },
				  { "data": "skubuy" },
                  { "data": "skuget" },
                  { "data": "fromdate" },
                  { "data": "todate" },
                  // { "data": "price" },
                  // { "data": "price_discount" },
              ]  
 
          });
		  
		  
		  
		 
		  
        });
		
		
		
function syncPromo(){
	$("#overlay").fadeIn(300);

		$.ajax({
		url: "api/action.php?modul=inventory&act=sync_promo_buyget",
		type: "POST",
		beforeSend: function(){
			$('#notif').html("Proses sync Promo..");
			
		},
		success: function(dataResult){
			console.log(dataResult);
			// var dataResult = JSON.parse(dataResult);
			location.reload();
		}
		});
}


function formatRupiah(angka, prefix){
			var number_string = angka.replace(/[^,\d]/g, '').toString(),
			split   		= number_string.split(','),
			sisa     		= split[0].length % 3,
			rupiah     		= split[0].substr(0, sisa),
			ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);
 
			// tambahkan titik jika yang di input sudah menjadi angka ribuan
			if(ribuan){
				separator = sisa ? '.' : '';
				rupiah += separator + ribuan.join('.');
			}
 
			rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
			return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
		}

</script>
</div>
<?php include "components/fff.php"; ?>