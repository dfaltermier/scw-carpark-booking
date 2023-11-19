
jQuery(document).ready(function(){
	
	var productid = jQuery(".scwacpbm_proid").val();
	
	jQuery(".scwacpbm_profile_save").click(function(){
		var profileid = jQuery(".scwacpbm_profile_choose").val();
		
		jQuery.ajax({
			url: "../wp-content/plugins/scw-carpark-booking/helper.php",
			data: {
				profileid : profileid,
				productid: productid,
				task : "add_profiletoproduct"
			},
			type: 'POST',
			beforeSend: function(data){
				jQuery(".scwacpbm_profile").css("opacity", "0.5");
			},
			success: function(data){
				jQuery(".scwacpbm_profile").css("opacity", "1");
				if(data)
					location.reload();
				else
					alert("Error!");
			}
		});
	});
	
	jQuery(".scwacpbm_content_body_booked_item").each(function(){
		var elthis = jQuery(this);
		
		elthis.children(".scwacpbm_content_body_booked_item_delete").click(function(){
			var r = confirm("These booked seats be removed, are you sure?");
			if(r == true){
				var sid = elthis.children(".scwacpbm_content_body_booked_item_id").val();
				jQuery.ajax({
					url: "../wp-content/plugins/scw-carpark-booking/helper.php",
					data: {
						sid : sid,
						task : "delete_booked_seats"
					},
					type: 'POST',
					beforeSend: function(data){
						elthis.css("opacity", "0.5");
					},
					success: function(data){
						elthis.css("opacity", "1");
						if(data)
							elthis.remove();
						else
							alert("Error!");
					}
				});
			}else{
				return false;
			}
		});
	});
});