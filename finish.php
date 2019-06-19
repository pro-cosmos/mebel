<?php
	echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';

	require_once 'config.php';
	
	$dbhost = DB_HOSTNAME;
	$dbuser = DB_USERNAME;
	$dbpass = DB_PASSWORD;
	$dbdatabase = DB_DATABASE;
	$dbprefix = DB_PREFIX;
	
	$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbdatabase);
	if(!$conn ) die('Could not connect: ' . mysql_error());

	$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbdatabase);
		
	$table = $dbprefix . "suppler";
	if (!getColumnName($conn, $dbdatabase, $table, "bonus")) {
		$query = "ALTER TABLE `".$dbdatabase.".".$dbprefix."suppler` ADD `bonus` VARCHAR(64) NOT NULL";
		$retval = $conn->query($query);
	}	
	if (!getColumnName($conn, $dbdatabase, $table, "bprice")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `bprice` VARCHAR(3) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "ddesc")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `ddesc` VARCHAR(1) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "ffile")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `ffile` VARCHAR(1) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "idcat")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `idcat` VARCHAR(1) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "jopt")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `jopt` int(2) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "kmenu")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `kmenu` VARCHAR(3) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "main")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `main` VARCHAR(1) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "metka")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `metka` VARCHAR(1) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "newproduct")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `newproduct` VARCHAR(5) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "onoff")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `onoff` VARCHAR(1) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "opt_fotos")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `opt_fotos` VARCHAR(1) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "opt_prices")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `opt_prices` VARCHAR(1) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "optsku")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `optsku` VARCHAR(1) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "parsq")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `parsq` VARCHAR(3) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "placeq")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `placeq` VARCHAR(5) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "plusopt")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `plusopt` VARCHAR(1) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "pointq")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `pointq` VARCHAR(64) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "qu_discount")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `qu_discount` VARCHAR(128) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "ratek")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `ratek` DECIMAL(12,4) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "ratep")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `ratep` DECIMAL(12,4) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "ref")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `ref` VARCHAR(128) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "ref1")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `ref1` VARCHAR(128) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "ref2")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `ref2` VARCHAR(128) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "ref3")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `ref3` VARCHAR(128) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "serie")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `serie` VARCHAR(3) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "sleep")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `sleep` VARCHAR(1) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "t_ref")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `t_ref` VARCHAR(3) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "t_ref1")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `t_ref1` VARCHAR(3) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "t_ref2")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `t_ref2` VARCHAR(3) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "t_ref3")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `t_ref3` VARCHAR(3) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "t_status")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `t_status` VARCHAR(255) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "termin")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `termin` VARCHAR(3) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "usd")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `usd` VARCHAR(3) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "zero")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `zero` VARCHAR(1) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "cheap")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `cheap` VARCHAR(3) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "spec")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `spec` VARCHAR(128) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "subfolder")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `subfolder` VARCHAR(1) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "rprice")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `rprice` VARCHAR(3) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "ddata")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `ddata` VARCHAR(3) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "mpn")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `mpn` VARCHAR(3) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "ean")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `ean` VARCHAR(3) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "upc")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `upc` VARCHAR(3) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "newurl")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `newurl` VARCHAR(1) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "disc")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `disc` VARCHAR(12) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "refer")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `refer` VARCHAR(3) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "onn")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `onn` VARCHAR(12) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "umanuf")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `umanuf` VARCHAR(1) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "off")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `off` VARCHAR(1) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "stay")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `stay` VARCHAR(1) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "catcreate")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `catcreate` VARCHAR(1) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "pic_ext")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `pic_ext` VARCHAR(1024) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "delimiter")) {
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `delimiter` VARCHAR(64) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "skuprefix")) {	
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `skuprefix` VARCHAR(24) NOT NULL";	
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "formdate")) {	
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `formdate` datetime";	
		$retval = $conn->query($query);
	}	
	if (!getColumnName($conn, $dbdatabase, $table, "location")) {	
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `location` VARCHAR(3) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "jan")) {	
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `jan` VARCHAR(3) NOT NULL";
		$retval = $conn->query($query);
	}
	if (!getColumnName($conn, $dbdatabase, $table, "isbn")) {	
		$query = "ALTER TABLE `".$dbprefix."suppler` ADD `isbn` VARCHAR(3) NOT NULL";
		$retval = $conn->query($query);
	}	
	
	$query = "ALTER TABLE `".$dbprefix."suppler` MODIFY `my_qu` VARCHAR(4096) NOT NULL";
	$retval = $conn->query($query);
   	$query = "ALTER TABLE `".$dbprefix."suppler` MODIFY `delimiter` VARCHAR(64) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler` MODIFY `ad` VARCHAR(2) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler` MODIFY `cheap` VARCHAR(3) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler` MODIFY `pic_ext` VARCHAR(1024) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler` MODIFY `my_mark` VARCHAR(1024) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler` MODIFY `spec` VARCHAR(128) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler` MODIFY `warranty` VARCHAR(1024) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler` MODIFY `my_qu` VARCHAR(512) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler` MODIFY `pic_ext` VARCHAR(256) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler` MODIFY `cat` VARCHAR(512) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler` MODIFY `jopt` INT(2) NOT NULL";
	$retval = $conn->query($query);
	
	$query = "ALTER TABLE `".$dbprefix."suppler` MODIFY `ref` VARCHAR(128) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler` MODIFY `ref1` VARCHAR(128) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler` MODIFY `ref2` VARCHAR(128) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler` MODIFY `ref3` VARCHAR(128) NOT NULL";
	$retval = $conn->query($query);
	
	$query = "ALTER TABLE `".$dbprefix."suppler_data` MODIFY `cat_ext` VARCHAR(512) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler_data` MODIFY `pic_int` VARCHAR(512) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler_data` MODIFY `cat_plus` TEXT NOT NULL";
	$retval = $conn->query($query);	
		
	$table = $dbprefix . "suppler_attributes";
	if (!getColumnName($conn, $dbdatabase, $table, "filter_group_id")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_attributes` ADD `filter_group_id` INT(11) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "attr_point")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_attributes` ADD `attr_point` INT(11) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "attr_point")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_attributes` ADD `attr_point` INT(11) NOT NULL";
		$retval = $conn->query($query);		
	}
	$query = "ALTER TABLE `".$dbprefix."suppler_attributes` MODIFY `attr_ext` VARCHAR(255) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler_attributes` MODIFY `attr_point` VARCHAR(255) NOT NULL";
	$retval = $conn->query($query);
	
	$table = $dbprefix . "suppler_base_price";
	if (!getColumnName($conn, $dbdatabase, $table, "bav")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_base_price` ADD `bav` DECIMAL(12,4) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "bmax")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_base_price` ADD `bmax` DECIMAL(12,4) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "bmin")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_base_price` ADD `bmin` DECIMAL(12,4) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "market_percent_to_bdprice")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_base_price` ADD `market_percent_to_bdprice` DECIMAL(6,3) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "market_percent_to_bprice")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_base_price` ADD `market_percent_to_bprice` DECIMAL(6,3) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "market_percent_to_price")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_base_price` ADD `market_percent_to_price` DECIMAL(6,3) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "optimal")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_base_price` ADD `optimal` DECIMAL(12,4) NOT NULL";
		$retval = $conn->query($query);		
	}
	
	$table = $dbprefix . "relatedoptions";
	if (!getColumnName($conn, $dbdatabase, $table, "defaultselect")) {
		$query = "ALTER TABLE `".$dbprefix."relatedoptions` ADD `defaultselect` tinyint(1) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "defaultselectpriority")) {
		$query = "ALTER TABLE `".$dbprefix."relatedoptions` ADD `defaultselectpriority` int(11) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "relatedoptions_variant_product_id")) {
		$query = "ALTER TABLE `".$dbprefix."relatedoptions` ADD `relatedoptions_variant_product_id` int(11) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "sku")) {
		$query = "ALTER TABLE `".$dbprefix."relatedoptions` ADD `sku` VARCHAR(64) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "upc")) {
		$query = "ALTER TABLE `".$dbprefix."relatedoptions` ADD `upc` VARCHAR(12) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "ean")) {
		$query = "ALTER TABLE `".$dbprefix."relatedoptions` ADD `ean` VARCHAR(14) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "location")) {
		$query = "ALTER TABLE `".$dbprefix."relatedoptions` ADD `location` VARCHAR(128) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "stock_status_id")) {
		$query = "ALTER TABLE `".$dbprefix."relatedoptions` ADD `stock_status_id` int(11) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "price_prefix")) {
		$query = "ALTER TABLE `".$dbprefix."relatedoptions` ADD `price_prefix` VARCHAR(1) NOT NULL";
		$retval = $conn->query($query);		
	}	
	if (!getColumnName($conn, $dbdatabase, $table, "model")) {
		$query = "ALTER TABLE `".$dbprefix."relatedoptions` ADD `model` VARCHAR(64) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "price")) {
		$query = "ALTER TABLE `".$dbprefix."relatedoptions` ADD `price` DECIMAL(15,4) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "weight_prefix")) {
		$query = "ALTER TABLE `".$dbprefix."relatedoptions` ADD `weight_prefix` varchar(1) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "weight")) {
		$query = "ALTER TABLE `".$dbprefix."relatedoptions` ADD `weight` decimal(15,8) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "description")) {
		$query = "ALTER TABLE `".$dbprefix."relatedoptions` ADD `description` text NOT NULL";
		$retval = $conn->query($query);		
	}
	
	$table = $dbprefix . "relatedoptions_variant_product";
	if (!getColumnName($conn, $dbdatabase, $table, "relatedoptions_variant_product_id")) {
		$query = "ALTER TABLE `".$dbprefix."relatedoptions_variant_product` ADD `relatedoptions_variant_product_id` int(11)";
		$retval = $conn->query($query);
		
		$query = "ALTER TABLE `".$dbprefix."relatedoptions_variant_product` CHANGE relatedoptions_variant_product_id  relatedoptions_variant_product_id int(11) AUTO_INCREMENT PRIMARY KEY";
		$retval = $conn->query($query);		
	}
	
	$table = $dbprefix . "suppler_options";
	if (!getColumnName($conn, $dbdatabase, $table, "art")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_options` ADD `art` varchar(3) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "foto")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_options` ADD `foto` varchar(3) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "opt_point")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_options` ADD `opt_point` varchar(128) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "opt_pref")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_options` ADD `opt_pref` varchar(1) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "opt_margin")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_options` ADD `opt_margin` varchar(1) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "opt")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_options` ADD `opt` varchar(1) NOT NULL";
		$retval = $conn->query($query);		
	}
	$query = "ALTER TABLE `".$dbprefix."suppler_options` MODIFY `opt` VARCHAR(128) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler_options` MODIFY `pr` VARCHAR(128) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler_options` MODIFY `opt_point` VARCHAR(128) NOT NULL";
	$retval = $conn->query($query);
	
	$table = $dbprefix . "suppler_price";
	if (!getColumnName($conn, $dbdatabase, $table, "baseprice")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_price` ADD `baseprice` INT(1) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "noprice")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_price` ADD `noprice` VARCHAR(128) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "paramnp")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_price` ADD `paramnp` VARCHAR(128) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "pointnp")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_price` ADD `pointnp` VARCHAR(128) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "mratek")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_price` ADD `mratek` DECIMAL(15,4) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "point")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_price` ADD `point` VARCHAR(128) NOT NULL";
		$retval = $conn->query($query);		
	}
	$query = "ALTER TABLE `".$dbprefix."suppler_price` MODIFY `noprice` VARCHAR(128) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler_price` MODIFY `pointnp` VARCHAR(128) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler_price` MODIFY `point` VARCHAR(128) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler_price` MODIFY `ident` VARCHAR(128) NOT NULL";
	$retval = $conn->query($query);
	
	$table = $dbprefix . "suppler_ref";
	if (!getColumnName($conn, $dbdatabase, $table, "price")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_ref` ADD `price` DECIMAL(15,4) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "ident")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_ref` ADD `ident` VARCHAR(128) NOT NULL";
		$retval = $conn->query($query);		
	}
	$query = "ALTER TABLE `".$dbprefix."suppler_ref` MODIFY `ident` VARCHAR(128) NOT NULL";
	$retval = $conn->query($query);
	
	$table = $dbprefix . "suppler_seo";
	if (!getColumnName($conn, $dbdatabase, $table, "prod_h1")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_seo` ADD `prod_h1` TEXT NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "prod_keyword")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_seo` ADD `prod_keyword` TEXT NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "prod_photo")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_seo` ADD `prod_photo` TEXT NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "prod_url")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_seo` ADD `prod_url` TEXT NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "cat_h1")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_seo` ADD `cat_h1` TEXT NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "cat_keyword")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_seo` ADD `cat_keyword` TEXT NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "manuf_h1")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_seo` ADD `manuf_h1` TEXT NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "manuf_keyword")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_seo` ADD `manuf_keyword` TEXT NOT NULL";
		$retval = $conn->query($query);		
	}
	
	$query = "ALTER TABLE `".$dbprefix."suppler_seo` MODIFY `cat_meta_desc` TEXT NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler_seo` MODIFY `cat_title` TEXT NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler_seo` MODIFY `manuf_meta_desc` TEXT NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler_seo` MODIFY `manuf_title` TEXT NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler_seo` MODIFY `prod_meta_desc` TEXT NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler_seo` MODIFY `prod_title` TEXT NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler_seo` MODIFY `prod_h1` TEXT NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."suppler_seo` MODIFY `prod_url` TEXT NOT NULL";
	$retval = $conn->query($query);
	
	$table = $dbprefix . "suppler_sku_description";
	if (!getColumnName($conn, $dbdatabase, $table, "store_id")) {
		$query = "ALTER TABLE `".$dbprefix."suppler_sku_description` ADD `store_id` INT(2) NOT NULL";
		$retval = $conn->query($query);		
	}
	$query = "ALTER TABLE `".$dbprefix."suppler_sku_description` MODIFY `sku` VARCHAR(64) NOT NULL";
	$retval = $conn->query($query);
	
	$table = $dbprefix . "product";
	if (!getColumnName($conn, $dbdatabase, $table, "ean")) {
		$query = "ALTER TABLE `".$dbprefix."product` ADD `ean` VARCHAR(64) NOT NULL";
		$retval = $conn->query($query);		
	}	
	
	if (!getColumnName($conn, $dbdatabase, $table, "mpn")) {
		$query = "ALTER TABLE `".$dbprefix."product` ADD `mpn` VARCHAR(64) NOT NULL";
		$retval = $conn->query($query);		
	}	
	
	if (!getColumnName($conn, $dbdatabase, $table, "jan")) {
		$query = "ALTER TABLE `".$dbprefix."product` ADD `jan` VARCHAR(64) NOT NULL";
		$retval = $conn->query($query);		
	}
		
	if (!getColumnName($conn, $dbdatabase, $table, "isbn")) {
		$query = "ALTER TABLE `".$dbprefix."product` ADD `isbn` VARCHAR(64) NOT NULL";
		$retval = $conn->query($query);		
	}
	if (!getColumnName($conn, $dbdatabase, $table, "cost")) {	
		$query = "ALTER TABLE `".$dbprefix."product` ADD `cost` DECIMAL(12,4) NOT NULL";
		$retval = $conn->query($query);
	}
	
	$query = "ALTER TABLE `".$dbprefix."product` MODIFY `upc` VARCHAR(64) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."product` MODIFY `ean` VARCHAR(64) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."product` MODIFY `mpn` VARCHAR(64) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."product` MODIFY `jan` VARCHAR(64) NOT NULL";
	$retval = $conn->query($query);
	$query = "ALTER TABLE `".$dbprefix."product` MODIFY `isbn` VARCHAR(64) NOT NULL";
	$retval = $conn->query($query);
	
	$table = $dbprefix . "product_option_value";
	if (!getColumnName($conn, $dbdatabase, $table, "optsku")) {
		$query = "ALTER TABLE `".$dbprefix."product_option_value` ADD `optsku` VARCHAR(64) NOT NULL";
		$retval = $conn->query($query);		
	}
	
	$query = "ALTER TABLE `".$dbprefix."attribute_description` MODIFY `name` VARCHAR(256) NOT NULL";
	$retval = $conn->query($query);	
	
	$conn->close();
	echo " The database is ready";
		
  /*******************************************************/
	function getColumnName($conn, $dbdatabase, $table, $name) {
		$query = "SELECT COLUMN_NAME FROM information_schema.columns WHERE table_name='" .$table."' AND  column_name = '". $name ."' AND table_schema ='" . $dbdatabase . "'";
		
		$retval = $conn->query($query);
		$rows = $retval->fetch_assoc();
		
		$ok = 0;
		if (isset($rows['COLUMN_NAME'])) $ok = 1;
		
		return $ok;
	}
?>	