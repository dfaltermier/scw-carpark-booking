
var upload_image_button=false;
jQuery(document).ready(function(){

	var width = jQuery("#wpbody-content").width() - 42;

	jQuery('.scwacpbm_lotbg_con_upload').click(function(){
        upload_image_button =true;
        formfieldID = jQuery(this).prev('input');

		tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
        if(upload_image_button==true){
			var oldFunc = window.send_to_editor;
			window.send_to_editor = function(html) {
				imgurl = jQuery('img', html).attr('src');
				jQuery(formfieldID).val(imgurl);
				tb_remove();
				window.send_to_editor = oldFunc;
			}
        }
        upload_image_button=false;
    });

	////////
	jQuery(".scwacpbm_add_button").click(function(){
		var parkName = jQuery(".scwacpbm_add_name").val();
		if(parkName){
			jQuery.ajax({
				url: "../wp-content/plugins/scw-carpark-booking/helper.php",
				data: {
					parkName : parkName,
					task : "add_park"
				},
				type: 'POST',
				beforeSend: function(data){
					jQuery(".scwacpbm_add_button").append(' <i class="fa fa-refresh fa-spin addparkspin"></i>');
				},
				success: function(data){
					jQuery(".addparkspin").remove();
					if(data == "1")
						location.reload();
					else
						alert(data);
				}
			});
		}
	});

	///////
	jQuery(".scwacpbm_lot").each(function(lotkey, lotval){
		var elthis = jQuery(this);

		elthis.find(".scwacpbm_mapping_listpreview").css("width", width+"px");

		elthis.find(".fa-angle-double-right").click(function(){
			if(elthis.children(".scwacpbm_lot_content").is(":visible")){
				elthis.children(".scwacpbm_lot_content").slideUp();
				setCookie("status"+lotkey, "close", 1);
			}else{
				elthis.children(".scwacpbm_lot_content").slideDown();
				setCookie("status"+lotkey, "open", 1);
			}
		});
		var checkStatus = getCookie("status"+lotkey);
		if(checkStatus == "open"){
			elthis.children(".scwacpbm_lot_content").slideDown();
		}
		////
		elthis.find(".scwacpbm_lot_head_delete").click(function(){
			var r = confirm("This lot will be delete, are you sure?");
			if(r == true){
				var lotId = elthis.children(".scwacpbm_lot_id").val();
				jQuery.ajax({
					url: "../wp-content/plugins/scw-carpark-booking/helper.php",
					data: {
						lotId : lotId,
						task : "delete_lot"
					},
					type: 'POST',
					beforeSend: function(data){
						elthis.find(".scwacpbm_lot_head_delete").append(' <i class="fa fa-refresh fa-spin deletelot_spin"></i>');
					},
					success: function(data){
						jQuery(".deletelot_spin").remove();
						if(data == "1")
							elthis.remove();
						else
							alert(data);
					}
				});
			}else{
				return false;
			}
		});

		//////
		elthis.find(".scwacpbm_lot_content_editname_save").click(function(){
			var newLotname = elthis.find(".scwacpbm_lot_content_editname_name").val();
			if(newLotname){
				var lotId = elthis.children(".scwacpbm_lot_id").val();
				jQuery.ajax({
					url: "../wp-content/plugins/scw-carpark-booking/helper.php",
					data: {
						lotId : lotId,
						newLotname: newLotname,
						task : "save_lot_name"
					},
					type: 'POST',
					beforeSend: function(data){
						elthis.find(".scwacpbm_lot_content_editname_save").append(' <i class="fa fa-refresh fa-spin savelotname_spin"></i>');
					},
					success: function(data){
						jQuery(".savelotname_spin").remove();
						if(data == "1"){
							alert("Saved!");
							elthis.find(".scwacpbm_lot_head_name").text(newLotname);
						}else
							alert("Error!");
					}
				});
			}
		});

		//////
		elthis.find(".scwacpbm_lotbg_save").click(function(){
			var color = elthis.find(".scwacpbm_lotbg_con_color").val();
			var bg = elthis.find(".scwacpbm_lotbg_con_image").val();
			var lotId = elthis.children(".scwacpbm_lot_id").val();

			jQuery.ajax({
				url: "../wp-content/plugins/scw-carpark-booking/helper.php",
				data: {
					lotId : lotId,
					color: color,
					bg: bg,
					task : "save_lot_bg"
				},
				type: 'POST',
				beforeSend: function(data){
					elthis.find(".scwacpbm_lotbg_save").append(' <i class="fa fa-refresh fa-spin savelotbg_spin"></i>');
				},
				success: function(data){
					jQuery(".savelotbg_spin").remove();
					if(data == "1"){
						alert("Saved!");
					}else
						alert("Error!");
				}
			});
		});

		//////
		elthis.find(".scwacpbm_lotsize_save").click(function(){
			var width = elthis.find(".scwacpbm_lotsize_width").val();
			var height = elthis.find(".scwacpbm_lotsize_height").val();
			var lotId = elthis.children(".scwacpbm_lot_id").val();

			jQuery.ajax({
				url: "../wp-content/plugins/scw-carpark-booking/helper.php",
				data: {
					lotId : lotId,
					width: width,
					height: height,
					task : "save_lot_size"
				},
				type: 'POST',
				beforeSend: function(data){
					elthis.find(".scwacpbm_lotsize_save").append(' <i class="fa fa-refresh fa-spin savelotsize_spin"></i>');
				},
				success: function(data){
					jQuery(".savelotsize_spin").remove();
					if(data == "1"){
						alert("Saved!");
					}else
						alert("Error!");
				}
			});
		});

		////////
		elthis.find(".scwacpbm_lottype_add_button").click(function(){
			var typename = elthis.find(".scwacpbm_lottype_add_name").val();
			var typecolor = elthis.find(".scwacpbm_lottype_add_color").val();
			var isbooked = elthis.find(".scwacpbm_lottype_add_check_input").is(":checked")?"1":"0";
			var lotId = elthis.children(".scwacpbm_lot_id").val();

			if(typename){
				jQuery.ajax({
					url: "../wp-content/plugins/scw-carpark-booking/helper.php",
					data: {
						lotId : lotId,
						typename: typename,
						typecolor: typecolor,
						isbooked: isbooked,
						task : "add_type"
					},
					type: 'POST',
					beforeSend: function(data){
						elthis.find(".scwacpbm_lottype_add_button").append(' <i class="fa fa-refresh fa-spin addtype_spin"></i>');
					},
					success: function(data){
						jQuery(".addtype_spin").remove();
						if(data == "1"){
							location.reload();
						}else
							alert("Error!");
					}
				});
			}
		});

		////////
		elthis.find(".scwacpbm_lottype_item").each(function(){
			var thistype = jQuery(this);

			thistype.children(".scwacpbm_lottype_item_save").click(function(){
				var thistypeid = thistype.children(".scwacpbm_lottype_item_id").val();
				var thistypename = thistype.children(".scwacpbm_lottype_item_name").val();
				var thistypecolor = thistype.children(".scwacpbm_lottype_item_color").val();
				var thisisbooked = thistype.find(".scwacpbm_lottype_item_check_input").is(":checked")?"1":"0";
				var lotId = elthis.children(".scwacpbm_lot_id").val();

				jQuery.ajax({
					url: "../wp-content/plugins/scw-carpark-booking/helper.php",
					data: {
						thistypeid : thistypeid,
						thistypename: thistypename,
						thistypecolor: thistypecolor,
						thisisbooked: thisisbooked,
						lotId: lotId,
						task : "save_type"
					},
					type: 'POST',
					beforeSend: function(data){
						thistype.children(".scwacpbm_lottype_item_save").append(' <i class="fa fa-refresh fa-spin savetype_spin"></i>');
					},
					success: function(data){
						jQuery(".savetype_spin").remove();
						if(data == "1"){
							alert("Saved!");
						}else
							alert("Error!");
					}
				});
			});
			thistype.children(".scwacpbm_lottype_item_del").click(function(){
				var r = confirm("This type will be delete, are you sure?");
				if(r == true){
					var thistypeid = thistype.children(".scwacpbm_lottype_item_id").val();
					jQuery.ajax({
						url: "../wp-content/plugins/scw-carpark-booking/helper.php",
						data: {
							thistypeid : thistypeid,
							task : "delete_type"
						},
						type: 'POST',
						beforeSend: function(data){
							thistype.children(".scwacpbm_lottype_item_del").append(' <i class="fa fa-refresh fa-spin deletetype_spin"></i>');
						},
						success: function(data){
							jQuery(".deletetype_spin").remove();
							if(data == "1")
								thistype.remove();
							else
								alert(data);
						}
					});
				}else{
					return false;
				}
			});
		});

		//////
		elthis.find(".scwacpbm_mapping_add_button").click(function(){
			var lotId = elthis.find(".scwacpbm_lot_id").val();
			var label = elthis.find(".scwacpbm_mapping_add_label").val();
			var type = elthis.find(".scwacpbm_mapping_add_type").val();
			var width = elthis.find(".scwacpbm_mapping_add_width").val();
			var height = elthis.find(".scwacpbm_mapping_add_height").val();
			var mleft = elthis.find(".scwacpbm_mapping_add_cx").val();
			var mtop = elthis.find(".scwacpbm_mapping_add_cy").val();
			var tilt = elthis.find(".scwacpbm_mapping_add_tilt").val();

			if(label){
				jQuery.ajax({
					url: "../wp-content/plugins/scw-carpark-booking/helper.php",
					data: {
						lotId : lotId,
						label : label,
						type : type,
						width : width,
						height : height,
						mleft : mleft,
						mtop : mtop,
						tilt : tilt,
						task : "add_slot"
					},
					type: 'POST',
					beforeSend: function(data){
						elthis.find(".scwacpbm_mapping_add_button").append(' <i class="fa fa-refresh fa-spin addslot_spin"></i>');
					},
					success: function(data){
						jQuery(".addslot_spin").remove();
						if(data == "1")
							alert("Added!");
						else
							alert(data);
					}
				});
			}
		});
		elthis.find(".scwacpbm_mapping_ref_button").click(function(){
			location.reload();
		});

		////////
		elthis.find(".scwacpbm_slot").each(function(){
			var thisslot = jQuery(this);

			thisslot.children(".scwacpbm_slot_save").click(function(){
				var thisslotid = thisslot.children(".scwacpbm_slot_id").val();
				var thisslotlabel = thisslot.children(".scwacpbm_slot_label").val();
				var thisslottype = thisslot.children(".scwacpbm_slot_type").val();
				var thisslotwidth = thisslot.children(".scwacpbm_slot_width").val();
				var thisslotheight = thisslot.children(".scwacpbm_slot_height").val();
				var thisslotcx = thisslot.children(".scwacpbm_slot_cx").val();
				var thisslotcy = thisslot.children(".scwacpbm_slot_cy").val();
				var thisslottilt = thisslot.children(".scwacpbm_slot_tilt").val();

				jQuery.ajax({
					url: "../wp-content/plugins/scw-carpark-booking/helper.php",
					data: {
						thisslotid : thisslotid,
						thisslotlabel: thisslotlabel,
						thisslottype: thisslottype,
						thisslotwidth: thisslotwidth,
						thisslotheight: thisslotheight,
						thisslotcx: thisslotcx,
						thisslotcy: thisslotcy,
						thisslottilt: thisslottilt,
						task : "save_slot"
					},
					type: 'POST',
					beforeSend: function(data){
						thisslot.children(".scwacpbm_slot_save").append(' <i class="fa fa-refresh fa-spin saveslot_spin"></i>');
					},
					success: function(data){
						jQuery(".saveslot_spin").remove();
						if(data == "1"){
							alert("Saved!");
						}else
							alert("Error!");
					}
				});
			});
			thisslot.children(".scwacpbm_slot_del").click(function(){
				var r = confirm("This slot will be delete, are you sure?");
				if(r == true){
					var thisslotid = thisslot.children(".scwacpbm_slot_id").val();
					jQuery.ajax({
						url: "../wp-content/plugins/scw-carpark-booking/helper.php",
						data: {
							thisslotid : thisslotid,
							task : "delete_slot"
						},
						type: 'POST',
						beforeSend: function(data){
							thisslot.children(".scwacpbm_slot_del").append(' <i class="fa fa-refresh fa-spin deleteslot_spin"></i>');
						},
						success: function(data){
							jQuery(".deleteslot_spin").remove();
							if(data == "1")
								thisslot.remove();
							else
								alert(data);
						}
					});
				}else{
					return false;
				}
			});
		});

		///////
		elthis.find(".scwacpbm_lotprice_save").click(function(){
			var string = "";
			var lotId = elthis.find(".scwacpbm_lot_id").val();

			elthis.find(".scwacpbm_lotprice_item").each(function(){
				var id = jQuery(this).children(".scwacpbm_lotprice_item_id").val();
				var price = jQuery(this).children(".scwacpbm_lotprice_item_price").val();
				var pricetype = jQuery(this).children(".scwacpbm_lotprice_item_type").val();

				if(string)
					string += "@"+id+"#"+price+"#"+pricetype;
				else
					string += id+"#"+price+"#"+pricetype;
			});

			jQuery.ajax({
				url: "../wp-content/plugins/scw-carpark-booking/helper.php",
				data: {
					lotId: lotId,
					string : string,
					task : "save_price"
				},
				type: 'POST',
				beforeSend: function(data){
					elthis.find(".scwacpbm_lotprice_save").append(' <i class="fa fa-refresh fa-spin saveprice_spin"></i>');
				},
				success: function(data){
					jQuery(".saveprice_spin").remove();
					if(data == "1")
						alert("Saved!");
					else
						alert("Error!");
				}
			});
		});

        /**
         * Configure Fixed Dates
         */
        // Date/time format of our 'to' and 'from' form input fields.
        var dateTimeFormat = 'd-m-Y H:i';

        // Init the 'to/from' date/time pickers.
        jQuery('.scwacpbm_fixeddates_from_input').datetimepicker({
            format: dateTimeFormat,
            step: 15,
            minDate: 0,
            minTime: 0
        });

        jQuery('.scwacpbm_fixeddates_to_input').datetimepicker({
            format: dateTimeFormat,
            step: 15,
            minDate: 0,
            minTime: 0
        });

        jQuery(".scwacpbm_fixeddates_save").on('click', function() {
            var fromDate = jQuery(".scwacpbm_fixeddates_from_input").val().trim() || '';
            var toDate   = jQuery(".scwacpbm_fixeddates_to_input").val().trim() || '';
            var heading  = jQuery(".scwacpbm_fixeddates_heading_input").val().trim() || '';

            jQuery.ajax({
                url: "../wp-content/plugins/scw-carpark-booking/helper.php",
                data: {
                    task:    "save_fixed_dates",
                    lotId:   lotId,
                    from:    fromDate,
                    to:      toDate,
                    heading: heading
                },
                type: 'POST',
                beforeSend: function(data) {
                    jQuery(".scwacpbm_fixeddates_save").append(' <i class="fa fa-refresh fa-spin fixeddatesspin"></i>');
                },
                success: function(data) {
                    jQuery(".fixeddatesspin").remove();

                    if (data == "1") {
						alert("Saved!");
                    }
                    else {
						alert("Error!");
                    }
                }
            });
        });
	}); // end jQuery(".scwacpbm_lot").each
});

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}