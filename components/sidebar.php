<div id="sidebar" class="active">
	<div class="sidebar-wrapper active">
		<div class="sidebar-header">
			<div class="d-flex justify-content-between">
				<div class="logo">
					<a style="font-size: 20px" href="content.php">Store App <?php echo $_SESSION['kode_toko']; ?></a>
					<p style="font-size: 20px"><?php echo $_SESSION['username']; ?> (<?php echo $_SESSION['name']; ?>)
					</p>
				</div>
				<div class="toggler">
					<a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
				</div>
			</div>
		</div>
		<div class="sidebar-menu">
			<ul class="menu">
				<li class="sidebar-title">Menu</li>


				<li class="sidebar-item">
					<a href="receive_print.php" class='sidebar-link'>
						<i class="bi bi-cash-stack"></i>
						<span>Cetak Receive</span>
					</a>
				</li>
				
				<li class="sidebar-item">
					<a href="muser.php" class='sidebar-link'>
						<i class="bi bi-cash-stack"></i>
						<span>User POS</span>
					</a>
				</li>
				
				<li class="sidebar-item  has-sub">
					<a href="#" class='sidebar-link'>
						<i class="bi bi-camera"></i>
						<span>Capture</span>
					</a>
					<ul class="submenu ">
						<li class="submenu-item ">
							<a href="capture_sku_plano.php">Planogram</a>
						</li>
						<li class="submenu-item ">
							<a href="capture_sku_plano_approved.php">Planogram Approved</a>
						</li>
						<li class="submenu-item ">
							<a href="capture_sku.php">Event</a>
						</li>
						<li class="submenu-item ">
							<a href="capture_sku_go.php">GO / Relaunching</a>
						</li>
						<li class="submenu-item ">
							<a href="capture_sku_promo.php">Promo</a>
						</li>
					</ul>
				</li>

				<li class="sidebar-item  has-sub">
					<a href="#" class='sidebar-link'>
						<i class="bi bi-archive-fill"></i>
						<span>Physical Inventory</span>
					</a>
					<ul class="submenu ">
						<li class="submenu-item ">
							<a href="content.php">Inventory List</a>
						</li>
						<li class="submenu-item ">
							<a href="verify.php">Inventory Verify</a>
						</li>
						<li class="submenu-item ">
							<a href="pigantung.php">List PI Expired</a>
						</li>
						<li class="submenu-item ">
							<a href="invscanheader.php">Inventory All</a>
						</li>
						<li class="submenu-item ">
							<a href="importer.php">Importer</a>
						</li>
					</ul>
				</li>

				<li class="sidebar-item  has-sub">
					<a href="#" class='sidebar-link'>
						<i class="bi bi-archive-fill"></i>
						<span>PI Nasional</span>
					</a>
					<ul class="submenu ">
						<li class="submenu-item ">
							<a href="content_nasional.php">Inventory List</a>
						</li>
						<li class="submenu-item ">
							<a href="verify_nasional.php">Inventory Verify</a>
						</li>
						<li class="submenu-item ">
							<a href="invscan.php">Importer</a>
						</li>
					</ul>
				</li>
				
				<li class="sidebar-item">
					<a href="cek_harga.php" class='sidebar-link'>
						<i class="bi bi-cash-stack"></i>
						<span>Cek Harga</span>
					</a>
				</li>


				<li class="sidebar-item  has-sub">
					<a href="#" class='sidebar-link'>
						<i class="bi bi-cart3"></i>
						<span>Sync POS</span>
					</a>
					<ul class="submenu ">
						<li class="submenu-item ">
							<a href="sync_grab.php">Sync Grab</a>
						</li>

						<li class="submenu-item ">
							<a href="sync_function.php">Sync Function</a>
						</li>


						<?php 
						if($username == 'pos'){ ?>
						<li class="submenu-item ">
							<a href="sync_function_dev.php">Sync Function Dev</a>
						</li>
						<?php } ?>

						
						<li class="submenu-item ">
							<a href="sync_struk_category.php">Sync Struk Undian</a>
						</li>
						
						<li class="submenu-item ">
							<a href="sync.php">Sync All</a>
						</li>
					</ul>
				</li>
				
				<li class="sidebar-item  has-sub">
					<a href="#" class='sidebar-link'>
						<i class="bi bi-gift"></i>
						<span>Promo</span>
					</a>
					<ul class="submenu ">
						<li class="submenu-item ">
							<a href="cek_promo.php">Reguler & Code</a>
						</li>
						<li class="submenu-item ">
							<a href="cek_promo_grosir.php">Grosir</a>
						</li>
						<li class="submenu-item ">
							<a href="cek_promo_buyget.php">Buy & Get</a>
						</li>
						<li class="submenu-item ">
							<a href="cek_promo_bundling.php">Bundling</a>
						</li>
					</ul>
				</li>


				<li class="sidebar-item">
					<a href="mitemspromo_live.php" class='sidebar-link'>
						<i class="bi bi-tags-fill"></i>
						<span>Perubahan Harga Promo</span>
					</a>
				</li>

				<li class="sidebar-item  has-sub">
					<a href="#" class='sidebar-link'>
						<i class="bi bi-tags-fill"></i>
						<span>Price Tag Baby Doll</span>
					</a>
					<ul class="submenu ">
						<li class="submenu-item ">
							<a href="mitems_baby.php">Harga Reguler</a>
						</li>
						<li class="submenu-item ">
							<a href="mitemsold_baby.php">Harga Reguler Format Lama</a>
						</li>
						<li class="submenu-item ">
							<a href="mitems_alt_baby.php">Harga Reguler Per Rack</a>
						</li>
					</ul>
				</li>
				
				<li class="sidebar-item  has-sub">
					<a href="#" class='sidebar-link'>
						<i class="bi bi-tags-fill"></i>
						<span>Price Tag</span>
					</a>
					<ul class="submenu ">
						<li class="submenu-item ">
							<a href="mitems.php">Harga Reguler</a>
						</li>
						<li class="submenu-item ">
							<a href="mitemsold.php">Harga Reguler Format Lama</a>
						</li>
						<li class="submenu-item ">
							<a href="mitems_alt.php">Harga Reguler Per Rack</a>
						</li>
						<li class="submenu-item ">
							<a href="mitemspromo.php">Harga Promo</a>
						</li>
						<li class="submenu-item ">
							<a href="mitemsspecial.php">Harga Khusus</a>
						</li>
						<li class="submenu-item ">
							<a href="mitemspromocode_live.php">Harga Bertingkat</a>
						</li>
					</ul>
				</li>

				<li class="sidebar-item  has-sub">
					<a href="#" class='sidebar-link'>
						<i class="bi bi-shop"></i>
						<span>Sewa Tenant</span>
					</a>
					<ul class="submenu ">
						<li class="submenu-item ">
							<a href="instore.php">In Store</a>
						</li>
						<li class="submenu-item ">
							<a href="outstore.php">Out Store</a>
						</li>
					</ul>
				</li>
				
				<li class="sidebar-item  has-sub">
					<a href="#" class='sidebar-link'>
						<i class="bi bi-file-bar-graph"></i>
						<span>Report</span>
					</a>
					<ul class="submenu ">
						<li class="submenu-item ">
							<a href="report_sales_item_today.php">Sales Items Today</a>
						</li>
					</ul>
				</li>

				<li class="sidebar-item">
					<a href="logout.php" class='sidebar-link'>
						<i class="bi bi-arrow-bar-left"></i>
						<span>Logout</span>
					</a>
				</li>



			</ul>
		</div>
		<button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
	</div>
</div>