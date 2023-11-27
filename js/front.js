
jQuery(document).ready(function () {

    // Date/time format of our 'to' and 'from' form input fields.
    var dateTimeFormat = 'd-m-Y H:i';

    var contentArea = jQuery(".scwacpbm_content");

    jQuery("form.cart").before(contentArea.show());
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
     * Check for previous slot reservations.
     *
     * @param string  schedule  Single date value ('to' or 'from')
     * @param string  dateFrom  Always the 'from' date value. 'schedule' is the 'to' value.
     */
    function checkSchedule(schedule, dateFrom) {
        var datefrom = jQuery('.scwacpbm_date_from_input').val();

        // Begin by assuming we are checking the 'to' date.
        var data = {
            task: "check_schedule",
            schedule: schedule,
            proid: proid
        };

        // Modify if we're checking the 'from' date also.
        if (dateFrom) {
            data.task = 'check_schedule2';
            data.datefrom = dateFrom;
        }

        // Check for previous slot reservations.
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
     * Attach datetimepicker behavior to each 'to/from' input fields.
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
         * Callback functions for our datetimepickers when the date and times
         * are selected.
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

        // Init the 'to/from' date/time pickers.
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
     * Force our to/from date input fields to static values which the user cannot change.
     * There will be no datetimepicker.
     *
     * @param string fromDate    Reservation start date
     * @param string toDate      Reservation end date
     * @param string heading     Display label with reservation dates
     */
    function initFixedDates(fromDate, toDate, heading) {
        // The label replaces the 'Choose Date & Time' label normally displayed when the
        // datetimepickers are active.
        jQuery('.scwacpbm_date_head').empty().html(heading);

        // We'll add CSS that will make the 'from' and 'to' date input fields
        // disappear from the screen. We don't want them distracting the user.
        var css = {
            border: '0',
            clip: 'rect(0 0 0 0)',
            height: '1px',
            margin: '-1px',
            overflow: 'hidden',
            padding: '0',
            position: 'absolute',
            width: '1px'
        };

        // Now, hide the 'from' and 'to' input fields.
        jQuery('.scwacpbm_date_to')
            .attr('aria-hidden', 'true')
            .css(css);

        jQuery('.scwacpbm_date_from')
            .attr('aria-hidden', 'true')
            .css(css);

        // Keep the input fields now that they are offscreen, but make them
        // readonly and non-tabbable. They still need to be populated with
        // our 'to/from' dates so that the form can be submitted and
        // checkSchedule() below can read the value from the input fields.
        jQuery('.scwacpbm_date_from_input')
            .val(fromDate)
            .attr('readonly', 'readonly')
            .attr('tabindex', '-1');

        jQuery('.scwacpbm_date_to_input')
            .val(toDate)
            .attr('readonly', 'readonly')
            .attr('tabindex', '-1');

        // Check if any slots are already reserved. This will make an AJAX call
        // and change the color of any that are reserved.
        checkSchedule(toDate, fromDate);
    }

    /**
     * Initialize our reservation dates.
     */
    function initDates() {
        // Get the fixed dates from the backend.
        jQuery.ajax({
            type: "POST",
            url: url + "helper.php",
            data:  {
                task: "get_fixed_dates",
                proid: proid
            },
            success: function (data) {
                console.log(data[0]);

                // Initialize our fixed dates if we have them.
                if (data.length > 0) {
                    var from    = data[0]['from'] ?? '';
                    var to      = data[0]['to'] ?? '';
                    var heading = data[0]['heading'] ?? '';

                    if (from && to && heading) {
                        initFixedDates(from, to, heading);
                        return;
                    }
                }

                // Or, fallback to the default datepicker input fields.
                initDateTimePickers();
            },
            dataType: 'json'
        });
    }

    /**
     * Init the price label so that the displayed price increments with every parking
     * slot selected.
     */
    function initPriceLabel() {
        // Extract the text 'Available: $20 per one' from the yellow legend box.
        // Change the text to 'Available: $20 per spot.'
        var labelText = jQuery('.scwacpbm_type_price').text().replace('one', 'spot');
        jQuery('.scwacpbm_type_price').text(labelText);

        // Extract the price that is charged for each slot from the labelText.
        var priceArray = labelText.match(/\d+/);
        var price = typeof(priceArray) == 'object' ? parseInt(priceArray[0]) : 0;

        // Get a handle to the DOM element where the price is displayed. Init to $0.00.
        var priceTotalLabel = jQuery('.woocommerce-Price-amount bdi');
        priceTotalLabel.text('$0.00');

        // When a slot is clicked by the user, update the displayed price.
        jQuery(".scwacpbm_map_slot").on('click', function() {
            var numberOfSelectedSlots = jQuery(".scwacpbm_map_slots").find('.active').size();
            var totalPrice = numberOfSelectedSlots * price;
            priceTotalLabel.text('$' + totalPrice.toFixed(2));
        });
    }

    /**
     * Kick things off!
     */
    function init() {
        // Init the reservation dates.
        initDates();

        // Init the price label so that the displayed price increments with every parking
        // slot selected.
        initPriceLabel();
    }

    // Let's kick it!
    init();
});