
jQuery(document).ready(function () {

    // Date/time format of our 'to' and 'from' form input fields.
    var dateTimeFormat = 'd-m-Y H:i';

     // Force our to/from dates.
    var lockedFromDate = '25-11-2023 12:30';
    var lockedToDate   = '30-11-2023 16:30';

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

    /**
     *
     * @param string  schedule
     * @param string  dateFrom
     */
    function checkSchedule(schedule, dateFrom) {
        var datefrom = jQuery('.scwacpbm_date_from_input').val();

        // Begin by assuming we are checking the 'to' date.
        var data = {
            task: "check_schedule",
            schedule: schedule,
            proid: proid
        };

        // Modify if we're checking the 'from' date, afterall.
        if (dateFrom) {
            data.task = 'check_schedule2';
            data.datefrom = dateFrom;
        }

        // Check for previous reservations.
        jQuery.ajax({
            type: "POST",
            url: url + "helper.php",
            data: data,
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
                        jQuery(".scwacpbm_map_slot[objectdata='slot" + val + "']")
                            .css("background", bookedColor)
                            .addClass("seatbooked");
                    });
                }
            },
            dataType: 'json'
        });
    }

    /**
     *
     */
    function initDateTimePickers() {
        /**
         *
         * @param string schedule
         */
        function selectFromDateTime(schedule) {
            console.log(schedule);

            // Check for previous reservations.
            checkSchedule(schedule);

            // Extract just the date fields from 'd-m-Y H:i'
            var dateTime = schedule.split(" ");
            var date     = dateTime[0].split("-");

            // Set the minimum 'to' date to the selected 'from' date/time.
            jQuery('.scwacpbm_date_to_input')
                .datetimepicker(
                    'setOptions',
                    { minDate: date[2] + "/" + date[1] + "/" + date[0] }
                );
        }

        /**
         *
         * @param string schedule
         */
        function selectToDateTime(schedule) {
            var dateFrom = jQuery('.scwacpbm_date_from_input').val();

            // Don't allow user to set the 'to' date unless the 'from' date is already set.
            // Otherwise, check for previous reservations.
            if (! dateFrom) {
                jQuery('.scwacpbm_date_to_input').val("");
            }
            else {
                checkSchedule(schedule, dateFrom);
            }
        }

        jQuery('.scwacpbm_date_from_input').datetimepicker({
            format: dateTimeFormat,
            step: 15,
            minDate: 0,
            minTime: 0,
            onSelectTime: function (ct, $i) {
                selectFromDateTime($i[0].value);
            },
            onSelectDate: function (ct, $i) {
                selectFromDateTime($i[0].value);
            }
        });

        jQuery('.scwacpbm_date_to_input').datetimepicker({
            format: dateTimeFormat,
            step: 15,
            minDate: 0,
            minTime: 0,
            onSelectTime: function (ct, $i) {
                selectToDateTime($i[0].value);
            },
            onSelectDate: function (ct, $i) {
                selectToDateTime($i[0].value);
            }
        });
    }

    /**
     *
     */
    function init() {
        // Force our to/from dates.
        var isDateLocked = true;
        var lockedFromDate = '25-11-2023 12:30';
        var lockedToDate   = '30-11-2023 16:30';

        if ( isDateLocked ) {
            jQuery('.scwacpbm_date_from_input')
                .val(lockedFromDate)
                .attr('readonly', 'readonly');

            jQuery('.scwacpbm_date_to_input')
                .val(lockedToDate)
                .attr('readonly', 'readonly');

            checkSchedule(lockedToDate, lockedFromDate);
        }
        else {
            initDateTimePickers();
        }
    }

    init();
});