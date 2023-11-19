<?php

if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/');
require_once(ABSPATH . 'wp-config.php');
global $wpdb;

$task = $_POST["task"];

if($task == "add_park"){
	$parkName = $_POST["parkName"];
	
	$tableName = $wpdb->prefix . 'scwacpbm_parklots';
	$rs = $wpdb->get_results("SELECT * from $tableName where parklotname = '".$parkName."'");
	
	if($rs){
		echo "This Parking Lot already exists!";
	}else{
		echo $wpdb->insert( $tableName,
			array( 
				'parklotname' => $parkName
		));
	}
}elseif($task == "delete_lot"){
	$lotId = $_POST["lotId"];
	
	$tableName = $wpdb->prefix . 'scwacpbm_parklots';
	echo $wpdb->delete( $tableName, array(
		'id' => $lotId
	));
}elseif($task == "save_lot_name"){
	$lotId = $_POST["lotId"];
	$newLotname = $_POST["newLotname"];
	
	$tableName = $wpdb->prefix . 'scwacpbm_parklots';
	echo $wpdb->update($tableName, array(
		'parklotname' => $newLotname
	),array(
		'id' => $lotId
	));
}elseif($task == "save_lot_bg"){
	$lotId = $_POST["lotId"];
	$color = $_POST["color"];
	$bg = $_POST["bg"];
	
	$tableName = $wpdb->prefix . 'scwacpbm_parklots';
	echo $wpdb->update($tableName, array(
		'lotcolor' => $color,
		'lotbg' => $bg
	),array(
		'id' => $lotId
	));
}elseif($task == "save_lot_size"){
	$lotId = $_POST["lotId"];
	$width = $_POST["width"];
	$height = $_POST["height"];
	
	$tableName = $wpdb->prefix . 'scwacpbm_parklots';
	echo $wpdb->update($tableName, array(
		'width' => $width,
		'height' => $height
	),array(
		'id' => $lotId
	));
}elseif($task == "add_type"){
	$lotId = $_POST["lotId"];
	$typename = $_POST["typename"];
	$typecolor = $_POST["typecolor"];
	$isbooked = $_POST["isbooked"];
	
	$tableName = $wpdb->prefix . 'scwacpbm_typeofslot';
	$rs = $wpdb->get_results("SELECT * from $tableName where lotid = '".$lotId."' and typename = '".$typename."'");
	
	if($rs){
		echo "This type already exists!";
	}else{
		echo $wpdb->insert( $tableName,
			array( 
				'lotid' => $lotId,
				'typename' => $typename,
				'typecolor' => $typecolor,
				'isbooked' => $isbooked
		));
	}
}elseif($task == "save_type"){
	$thistypeid = $_POST["thistypeid"];
	$thistypename = $_POST["thistypename"];
	$thistypecolor = $_POST["thistypecolor"];
	$thisisbooked = $_POST["thisisbooked"];
	$lotId = $_POST["lotId"];
	
	$tableName = $wpdb->prefix . 'scwacpbm_typeofslot';
	
	if($thisisbooked){
		$wpdb->update($tableName, array(
			'isbooked' => "0"
		),array(
			'lotid' => $lotId
		));
	}
	
	echo $wpdb->update($tableName, array(
		'typename' => $thistypename,
		'typecolor' => $thistypecolor,
		'isbooked' => $thisisbooked
	),array(
		'id' => $thistypeid
	));
}elseif($task == "delete_type"){
	$thistypeid = $_POST["thistypeid"];
	
	$tableName = $wpdb->prefix . 'scwacpbm_typeofslot';
	echo $wpdb->delete( $tableName, array(
		'id' => $thistypeid
	));
}elseif($task == "add_slot"){
	$lotId = $_POST["lotId"];
	$label = $_POST["label"];
	$type = $_POST["type"];
	$width = $_POST["width"];
	$height = $_POST["height"];
	$mleft = $_POST["mleft"];
	$mtop = $_POST["mtop"];
	$tilt = $_POST["tilt"];
	
	$tableName = $wpdb->prefix . 'scwacpbm_slots';
	$rs = $wpdb->get_results("SELECT * from $tableName where lotid = '".$lotId."' and label = '".$label."'");
	
	if($rs){
		echo "This slot already exists!";
	}else{
		echo $wpdb->insert( $tableName,
			array( 
				'lotid' => $lotId,
				'label' => $label,
				'type' => $type,
				'width' => $width,
				'height' => $height,
				'mleft' => $mleft,
				'mtop' => $mtop,
				'tilt' => $tilt
		));
	}
}elseif($task == "save_slot"){
	$thisslotid = $_POST["thisslotid"];
	$thisslotlabel = $_POST["thisslotlabel"];
	$thisslottype = $_POST["thisslottype"];
	$thisslotwidth = $_POST["thisslotwidth"];
	$thisslotheight = $_POST["thisslotheight"];
	$thisslotcx = $_POST["thisslotcx"];
	$thisslotcy = $_POST["thisslotcy"];
	$thisslottilt = $_POST["thisslottilt"];
	
	$tableName = $wpdb->prefix . 'scwacpbm_slots';

	echo $wpdb->update($tableName, array(
		'label' => $thisslotlabel,
		'type' => $thisslottype,
		'width' => $thisslotwidth,
		'height' => $thisslotheight,
		'mleft' => $thisslotcx,
		'mtop' => $thisslotcy,
		'tilt' => $thisslottilt
	),array(
		'id' => $thisslotid
	));
}elseif($task == "delete_slot"){
	$thisslotid = $_POST["thisslotid"];
	
	$tableName = $wpdb->prefix . 'scwacpbm_slots';
	echo $wpdb->delete( $tableName, array(
		'id' => $thisslotid
	));
}elseif($task == "save_price"){
	$lotId = $_POST["lotId"];
	$string = $_POST["string"];
	
	$tableName = $wpdb->prefix . 'scwacpbm_prices';
	$check = explode("@", $string);
	foreach($check as $price){
		$p = explode("#", $price);
		$rs = $wpdb->get_results("SELECT * from $tableName where type = '".$p[0]."' and lotid = ".$lotId);
		if($rs){
			$wpdb->update($tableName, array(
				'price' => $p[1],
				'pricetype' => $p[2]
			),array(
				'type' => $p[0],
				'lotid' => $lotId
			));
		}else{
			$wpdb->insert( $tableName,
				array( 
					'type' => $p[0],
					'price' => $p[1],
					'pricetype' => $p[2],
					'lotid' => $lotId
			));
		}
	}
	echo 1;
}elseif($task == "add_profiletoproduct"){
	$profileid = $_POST["profileid"];
	$productid = $_POST["productid"];
	
	$tableName = $wpdb->prefix . 'scwacpbm_products';
	$rs = $wpdb->get_results("SELECT * from $tableName where productid = ".$productid);
	if($rs){
		echo $wpdb->update($tableName, array(
			'lotid' => $profileid
		),array(
			'productid' => $productid
		));
	}else{
		echo $wpdb->insert( $tableName,
			array( 
				'lotid' => $profileid,
				'productid' => $productid
		));
	}
}elseif($task == "check_seats"){
	$seats = $_POST["seats"];
	$datefrom = $_POST["datefrom"];
	$dateto = $_POST["dateto"];
	$proid = $_POST["proid"];
	$posttype = filter_var($_POST["posttype"], FILTER_SANITIZE_STRING);
	
	$_SESSION["datefrom".$proid] = $datefrom;
	$_SESSION["dateto".$proid] = $dateto;
	$_SESSION["seats".$proid] = $seats;
	
	if($posttype == "post"){
		$tableProducts = $wpdb->prefix . 'scwacpbm_products';
		$tableSlots = $wpdb->prefix . 'scwacpbm_slots';
		$tablePrices = $wpdb->prefix . 'scwacpbm_prices';
		
		$selectedLot = $wpdb->get_results( "SELECT * FROM $tableProducts where productid = ".$proid );
		$lotid = $selectedLot[0]->lotid;
		
		$totalPrice = 0;
		if($lotid){
			
			$datefrom = $_SESSION["datefrom".$proid];
			$dateto = $_SESSION["dateto".$proid];
			$seats = $_SESSION["seats".$proid];
			
			if($seats){
				$sessSeats = explode("@", $seats);
				
				foreach($sessSeats as $s){
					$checSlot = $wpdb->get_results( "SELECT * FROM $tableSlots where lotid = ".$lotid." and label = '".$s."'" );
					$typeid = $checSlot[0]->type;
					
					$checPrice = $wpdb->get_results( "SELECT * FROM $tablePrices where lotid = ".$lotid." and type = '".$typeid."'" );
					if(isset($checPrice[0]->price)) $price = $checPrice[0]->price;
					else $price = 0;
					if(isset($checPrice[0]->pricetype)) $pricetype = $checPrice[0]->pricetype;
					else $pricetype = "";
					
					if($datefrom && $dateto){
						if($pricetype == "hour"){
							$hours = round((strtotime($dateto) - strtotime($datefrom))/3600, 1);
							$totalPrice += $price * $hours;
						}elseif($pricetype == "day"){
							$seconds = strtotime($dateto) - strtotime($datefrom);
							$days = ceil($seconds / 86400);
							
							if($days == 0)
								$totalPrice += $price;
							else
								$totalPrice += $price * $days;
						}else{
							$totalPrice += $price;
						}
					}elseif($datefrom && !$dateto){
						if($pricetype == "one")
							$totalPrice += $price;
					}
				}
			}
		}
		
		echo $totalPrice;
	}
}elseif($task == "check_schedule"){
	$schedule = $_POST["schedule"];
	$proid = $_POST["proid"];
	
	$tableOrders = $wpdb->prefix . 'scwacpbm_orders';
	$tableTypeofslot = $wpdb->prefix . 'scwacpbm_typeofslot';
	$tableProducts = $wpdb->prefix . 'scwacpbm_products';
	$tableSlots = $wpdb->prefix . 'scwacpbm_slots';
	
	$rs = $wpdb->get_results("SELECT * from $tableOrders where proid = ".$proid." and (('".$schedule."' between datefrom and dateto) or datefrom = '".$schedule."')");
	
	$checkLot = $wpdb->get_results("SELECT * from $tableProducts where productid = ".$proid);
	$lotid = $checkLot[0]->lotid;
	$checkType = $wpdb->get_results("SELECT * from $tableTypeofslot where lotid = ".$lotid." and isbooked = 1");
	$typeid = $checkType[0]->id;
	$checkBooked = $wpdb->get_results("SELECT * from $tableSlots where lotid = ".$lotid." and type = ".$typeid);
	
	$list = array();
	if($rs){
		foreach($rs as $r){
			$slots = explode("@", $r->slots);
			foreach($slots as $sl){
				if($sl){
					array_push($list, $sl);
				}
			}
		}
	}
	if($checkBooked){
		foreach($checkBooked as $bk){
			array_push($list, $bk->label);
		}
	}
	
	echo json_encode($list, 1);
}elseif($task == "delete_booked_seats"){
	$sid = $_POST["sid"];
	
	$tableName = $wpdb->prefix . 'scwacpbm_orders';
	echo $wpdb->delete( $tableName, array(
		'id' => $sid
	));
}elseif($task == "send_mail"){
	$name = $_POST["name"];
	$address = $_POST["address"];
	$email = $_POST["email"];
	$phone = $_POST["phone"];
	$note = $_POST["note"];
	$proId = $_POST["proId"];
	$total = $_POST["total"];
	$seats = $_POST["seats"];
	$datefrom = $_POST["datefrom"];
	$dateto = $_POST["dateto"];
	
	$adminEmail = get_option( 'admin_email' );
	
	$subject = 'Order Information';
	$body = 'Order information<br>';
	$body .= 'Seats: '.str_replace("@", " ", $seats).'<br>';
	$body .= 'Date From: '.$datefrom.'<br>';
	if($dateto)
		$body .= 'Date To: '.$dateto.'<br>';
	$body .= 'Name: '.$name.'<br>';
	$body .= 'Address: '.$address.'<br>';
	$body .= 'Email: '.$email.'<br>';
	$body .= 'Phone: '.$phone.'<br>';
	$body .= 'Note: '.$note.'<br>';
	$body .= 'Total: '.$total.'<br>';
	$headers = array('Content-Type: text/html; charset=UTF-8');
	 
	echo wp_mail( array($email, $adminEmail), $subject, $body, $headers );
	
	$seatsnew = explode("@", $seats);
	
	$orderTable = $wpdb->prefix . 'scwacpbm_orders';
	$wpdb->insert( $orderTable,
		array( 
			'orderid' => "",
			'proid' => $proId,
			'slots' => $seats,
			'datefrom' => $datefrom,
			'dateto' => $dateto,
			'name' => $name,
			'address' => $address,
			'email' => $email,
			'phone' => $phone,
			'note' => $note,
			'total' => $total
		) 
	);
}elseif($task == "check_schedule2"){
	$schedule = $_POST["schedule"];
	$datefrom = $_POST["datefrom"];
	$proid = $_POST["proid"];
	
	$tableOrders = $wpdb->prefix . 'scwacpbm_orders';
	$tableTypeofslot = $wpdb->prefix . 'scwacpbm_typeofslot';
	$tableProducts = $wpdb->prefix . 'scwacpbm_products';
	$tableSlots = $wpdb->prefix . 'scwacpbm_slots';
	
	$rs = $wpdb->get_results("SELECT * from $tableOrders where proid = ".$proid." and ((datefrom between '".$datefrom."' and '".$schedule."') or (dateto between '".$datefrom."' and '".$schedule."'))");
	
	$checkLot = $wpdb->get_results("SELECT * from $tableProducts where productid = ".$proid);
	$lotid = $checkLot[0]->lotid;
	$checkType = $wpdb->get_results("SELECT * from $tableTypeofslot where lotid = ".$lotid." and isbooked = 1");
	$typeid = $checkType[0]->id;
	$checkBooked = $wpdb->get_results("SELECT * from $tableSlots where lotid = ".$lotid." and type = ".$typeid);
	
	$list = array();
	if($rs){
		foreach($rs as $r){
			$slots = explode("@", $r->slots);
			foreach($slots as $sl){
				if($sl){
					array_push($list, $sl);
				}
			}
		}
	}
	if($checkBooked){
		foreach($checkBooked as $bk){
			array_push($list, $bk->label);
		}
	}
	
	echo json_encode($list, 1);
}