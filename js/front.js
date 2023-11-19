
jQuery(document).ready(function () {

    jQuery("form.cart").before(jQuery(".scwacpbm_content").show());
    var url = jQuery(".scwacpbm_url").val();
    var proid = jQuery(".product_id").val();
    var bookedColor = jQuery(".scwacpbm_type_booked").val();
    var posttype = jQuery(".scw_posttype").val();

    var width_config = jQuery(".width_config").val();
    var height_config = jQuery(".height_config").val();
    var current = jQuery(".scwacpbm_map_bg").width();
    var ptw = 100 / width_config * current;
    var newbh = height_config / 100 * ptw;
    jQuery(".scwacpbm_map_bg").css("height", newbh + "px");

    jQuery(".scwacpbm_map_slot").each(function () {
        var elthis = jQuery(this);
        var thisstyle = elthis.attr("style");
        var thisstyle = thisstyle.split(";");

        var thisw = elthis.width();
        var neww = thisw / 100 * ptw;

        var thish = elthis.height();
        var newh = thish / 100 * ptw;

        var thisl = thisstyle[4].split(":");
        var thisl = thisl[1].replace("px", "").trim();
        var newl = thisl / 100 * ptw;

        var thist = thisstyle[5].split(":");
        var thist = thist[1].replace("px", "").trim();
        var newt = thist / 100 * ptw;

        elthis.css({
            "width": neww + "px",
            "height": newh + "px",
            "line-height": newh + "px",
            "left": newl + "px",
            "top": newt + "px"
        });
    });

    jQuery('.scwacpbm_date_from_input').datetimepicker({
        format: 'd-m-Y H:i',
        step: 15,
        minDate: 0,
        minTime: 0,
        onSelectTime: function (ct, $i) {
            checkSchedule($i[0].value);
            var checkdate = $i[0].value.split(" ");
            var checkdate2 = checkdate[0].split("-");
            jQuery('.scwacpbm_date_to_input').datetimepicker('setOptions', { minDate: checkdate2[2] + "/" + checkdate2[1] + "/" + checkdate2[0] });
        },
        onSelectDate: function (ct, $i) {
            checkSchedule($i[0].value);
            //var datetime = $i[0].value.split(" ");
            //jQuery('.scwacpbm_date_from_input').val(datetime[0]);
            var checkdate = $i[0].value.split(" ");
            var checkdate2 = checkdate[0].split("-");
            jQuery('.scwacpbm_date_to_input').datetimepicker('setOptions', { minDate: checkdate2[2] + "/" + checkdate2[1] + "/" + checkdate2[0] });
        }
    });
    jQuery('.scwacpbm_date_to_input').datetimepicker({
        format: 'd-m-Y H:i',
        step: 15,
        minDate: 0,
        minTime: 0,
        onSelectTime: function (ct, $i) {
            //checkSchedule($i[0].value);
            var checkfrom = jQuery('.scwacpbm_date_from_input').val();
            if (!checkfrom)
                jQuery('.scwacpbm_date_to_input').val("");
            else
                checkSchedule2($i[0].value);
        },
        onSelectDate: function (ct, $i) {
            //checkSchedule($i[0].value);
            var checkfrom = jQuery('.scwacpbm_date_from_input').val();
            if (!checkfrom)
                jQuery('.scwacpbm_date_to_input').val("");
            else
                checkSchedule2($i[0].value);
        }
    });

    function checkSchedule2(schedule) {
        var datefrom = jQuery('.scwacpbm_date_from_input').val();
        jQuery.ajax({
            type: "POST",
            url: url + "helper.php",
            data: {
                task: "check_schedule2",
                schedule: schedule,
                datefrom: datefrom,
                proid: proid
            },
            beforeSend: function (data) {
                jQuery(".scwacpbm_map").css("opacity", "0.5");
            },
            success: function (data) {
                jQuery(".scwacpbm_map").css("opacity", "1");

                jQuery(".seatbooked").each(function () {
                    jQuery(this).removeClass("seatbooked");
                    var readcolor = jQuery(this).children(".scwacpbm_map_slot_readcolor").val();
                    jQuery(this).css("background", readcolor);
                });


                if (data.length > 0) {
                    jQuery.each(data, function (key, val) {
                        jQuery(".scwacpbm_map_slot[objectdata='slot" + val + "']").css("background", bookedColor).addClass("seatbooked");
                    });
                }
            },
            dataType: 'json'
        });
    }
    ////////////
    function checkSchedule(schedule) {
        jQuery.ajax({
            type: "POST",
            url: url + "helper.php",
            data: {
                task: "check_schedule",
                schedule: schedule,
                proid: proid
            },
            beforeSend: function (data) {
                jQuery(".scwacpbm_map").css("opacity", "0.5");
            },
            success: function (data) {
                jQuery(".scwacpbm_map").css("opacity", "1");

                jQuery(".seatbooked").each(function () {
                    jQuery(this).removeClass("seatbooked");
                    var readcolor = jQuery(this).children(".scwacpbm_map_slot_readcolor").val();
                    jQuery(this).css("background", readcolor);
                });

                if (data.length > 0) {
                    jQuery.each(data, function (key, val) {
                        jQuery(".scwacpbm_map_slot[objectdata='slot" + val + "']").css("background", bookedColor).addClass("seatbooked");
                    });
                }
            },
            dataType: 'json'
        });
    }

    ////////
    jQuery(".scwacpbm_map_slot").each(function () {
        var slot = jQuery(this);

        slot.click(function () {
            if (!slot.hasClass("seatbooked")) {
                if (jQuery(".scwacpbm_date_from_input").val()) {
                    if (slot.hasClass("active"))
                        slot.removeClass("active");
                    else
                        slot.addClass("active");

                    var seats = "";
                    jQuery(".scwacpbm_map_slot.active").each(function () {
                        if (seats)
                            seats += "@" + jQuery(this).children(".scwacpbm_map_slot_label").text().trim();
                        else
                            seats += jQuery(this).children(".scwacpbm_map_slot_label").text().trim();
                    });
                    var datefrom = jQuery(".scwacpbm_date_from_input").val();
                    var dateto = jQuery(".scwacpbm_date_to_input").val();

                    jQuery.ajax({
                        type: "POST",
                        url: url + "helper.php",
                        data: {
                            task: "check_seats",
                            seats: seats,
                            datefrom: datefrom,
                            dateto: dateto,
                            proid: proid,
                            posttype: posttype
                        },
                        beforeSend: function (data) {
                            jQuery(".scwacpbm_map").css("opacity", "0.5");
                        },
                        success: function (data) {
                            jQuery(".scwacpbm_map").css("opacity", "1");

                            if (posttype == "post") {
                                jQuery(".scwacpbm_total_value").text(data);
                            }
                        }
                    });
                } else
                    alert("Please choose date first!");
            }
        });
    });

    // wordpress post
    if (posttype == "post") {
        jQuery(".scwacpbm_form_submit").click(function () {
            var name = jQuery(".scwacpbm_form_name_input").val();
            var address = jQuery(".scwacpbm_form_address_input").val();
            var email = jQuery(".scwacpbm_form_email_input").val();
            var phone = jQuery(".scwacpbm_form_phone_input").val();
            var note = jQuery(".scwacpbm_form_note_input").val();
            var total = jQuery(".scwacpbm_total_value").text().trim();

            var seats = "";
            jQuery(".scwacpbm_map_slot.active").each(function () {
                if (seats)
                    seats += "@" + jQuery(this).children(".scwacpbm_map_slot_label").text().trim();
                else
                    seats += jQuery(this).children(".scwacpbm_map_slot_label").text().trim();
            });
            var datefrom = jQuery(".scwacpbm_date_from_input").val();
            var dateto = jQuery(".scwacpbm_date_to_input").val();

            if (seats) {
                jQuery.ajax({
                    url: url + "helper.php",
                    data: {
                        name: name,
                        address: address,
                        email: email,
                        phone: phone,
                        note: note,
                        proId: proid,
                        total: total,
                        seats: seats,
                        datefrom: datefrom,
                        dateto: dateto,
                        task: "send_mail"
                    },
                    type: 'POST',
                    beforeSend: function (data) {
                        jQuery(".scwacpbm_sendform").css("opacity", "0.5");
                    },
                    success: function (data) {
                        jQuery(".scwacpbm_sendform").css("opacity", "1");
                        if (data == "1")
                            alert("We got the order, will contact you soon!");
                        else
                            alert("Error!");
                    }
                });
            }
        });
    }
});