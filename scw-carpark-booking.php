<?php
/**
* Plugin Name: Advance Car Park Booking Management for WooCommerce
* Plugin URI: http://codecanyon.net/user/smartcms
* Description: car park booking for woocommerce product
* Version: 1.8
* Author: SmartCms Team
* Author URI: http://codecanyon.net/user/smartcms
* License: GPLv2 or later
*/

define ( 'SCWACPBM', plugin_dir_url(__FILE__));

function scwacpbm_session() {
  session_start();
}
add_action('wp_loaded','scwacpbm_session');

register_activation_hook(__FILE__, 'scwacpbm_install');
global $wnm_db_version;
$wnm_db_version = "1.0";

function scwacpbm_install(){
    global $wpdb;
    global $wnm_db_version;

    $parkLotsTB = $wpdb->prefix . 'scwacpbm_parklots';
    $typesTB = $wpdb->prefix . 'scwacpbm_typeofslot';
    $slotsTB = $wpdb->prefix . 'scwacpbm_slots';
    $pricesTB = $wpdb->prefix . 'scwacpbm_prices';
    $fixeddatesTB = $wpdb->prefix . 'scwacpbm_fixed_dates';
    $productsTB = $wpdb->prefix . 'scwacpbm_products';
    $ordersTB = $wpdb->prefix . 'scwacpbm_orders';

    $charset_collate = $wpdb->get_charset_collate();

    $parkLotsSql = "CREATE TABLE $parkLotsTB (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `parklotname` varchar(255) DEFAULT NULL,
        `lotcolor` varchar(255) DEFAULT NULL,
        `lotbg` varchar(255) DEFAULT NULL,
        `width` varchar(255) DEFAULT NULL,
        `height` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) $charset_collate;";

    $typesSql = "CREATE TABLE $typesTB (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `lotid` int(11) DEFAULT NULL,
        `typename` varchar(255) DEFAULT NULL,
        `typecolor` varchar(255) DEFAULT NULL,
        `isbooked` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) $charset_collate;";

    $slotsSql = "CREATE TABLE $slotsTB (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `lotid` int(11) DEFAULT NULL,
        `label` varchar(255) DEFAULT NULL,
        `type` varchar(255) DEFAULT NULL,
        `width` varchar(255) DEFAULT NULL,
        `height` varchar(255) DEFAULT NULL,
        `mleft` varchar(255) DEFAULT NULL,
        `mtop` varchar(255) DEFAULT NULL,
        `tilt` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) $charset_collate;";

    $pricesSql = "CREATE TABLE $pricesTB (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `lotid` int(11) DEFAULT NULL,
        `type` int(11) DEFAULT NULL,
        `price` varchar(255) DEFAULT NULL,
        `pricetype` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) $charset_collate;";

    $fixedDatesSql = "CREATE TABLE $fixeddatesTB (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `lotid` int(11) DEFAULT NULL,
        `from` varchar(255) DEFAULT NULL,
        `to` varchar(255) DEFAULT NULL,
        `heading` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`),
        FOREIGN KEY (lotid) REFERENCES {$parkLotsTB}(id)
    ) $charset_collate;";

    $productsSql = "CREATE TABLE $productsTB (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `lotid` int(11) DEFAULT NULL,
        `productid` int(11) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) $charset_collate;";

    $ordersSql = "CREATE TABLE $ordersTB (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `orderid` int(11) DEFAULT NULL,
        `proid` int(11) DEFAULT NULL,
        `slots` varchar(255) DEFAULT NULL,
        `datefrom` varchar(255) DEFAULT NULL,
        `dateto` varchar(255) DEFAULT NULL,
        `name` varchar(255) DEFAULT NULL,
        `address` varchar(255) DEFAULT NULL,
        `email` varchar(255) DEFAULT NULL,
        `phone` varchar(255) DEFAULT NULL,
        `note` varchar(255) DEFAULT NULL,
        `total` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($parkLotsSql);
    dbDelta($typesSql);
    dbDelta($slotsSql);
    dbDelta($pricesSql);
    dbDelta($fixedDatesSql);
    dbDelta($productsSql);
    dbDelta($ordersSql);

    add_option("wnm_db_version", $wnm_db_version);
}

add_action( 'admin_menu', 'smartcms_scwacpbm_admin_menu' );
function smartcms_scwacpbm_admin_menu() {
    add_menu_page(
        'Car Park Booking',
        'Car Park Booking',
        'manage_options',
        'scwacpbm-car-park',
        'smartcms_scwacpbm_options_page'
    );
}
function smartcms_scwacpbm_options_page(  ) {
    ?>
<form action='options.php' method='post'>
    <h2>SmartCms Car Park Booking</h2>
    <?php
        settings_fields( 'pluginSCWACPBMPage' );
        do_settings_sections( 'pluginSCWACPBMPage' );
        submit_button();
        ?>
</form>
<?php
}

add_action( 'admin_init', 'smartcms_scwacpbm_settings_init' );
function smartcms_scwacpbm_settings_init() {
    register_setting( 'pluginSCWACPBMPage', 'smartcms_scwacpbm_settings' );
    add_settings_section(
        'smartcms_pluginPage_section', __( '', 'wordpress' ), '', 'pluginSCWACPBMPage'
    );
    add_settings_field(
        '','',
        'smartcms_scwacpbm_parameters',
        'pluginSCWACPBMPage',
        'smartcms_pluginPage_section'
    );
}
function smartcms_scwacpbm_parameters(){
    $options = get_option( 'smartcms_scwacpbm_settings' );
    global $wpdb;

    wp_enqueue_script('jquery');
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_enqueue_style('thickbox');

    wp_register_style('scwacpbm-font-css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css');
    wp_enqueue_style('scwacpbm-font-css');

    wp_register_script('scwacpbm-adminscript', SCWACPBM .'js/admin.js');
    wp_enqueue_script('scwacpbm-adminscript');

    wp_register_script('scwacpbm-adminscript-datetimepicker', SCWACPBM .'datetimepicker/jquery.datetimepicker.full.min.js');
    wp_enqueue_script('scwacpbm-adminscript-datetimepicker');
    wp_register_style('scwacpbm-adminstyle-datetimepicker', SCWACPBM .'datetimepicker/jquery.datetimepicker.css');
    wp_enqueue_style('scwacpbm-adminstyle-datetimepicker');

    wp_register_style('scwacpbm-admincss', SCWACPBM .'css/admin.css');
    wp_enqueue_style('scwacpbm-admincss');

    $parkLotsTB = $wpdb->prefix . 'scwacpbm_parklots';
    $parkLots = $wpdb->get_results( "SELECT * FROM $parkLotsTB" );

    $pricesTB = $wpdb->prefix . 'scwacpbm_prices';
    $fixeddatesTB = $wpdb->prefix . 'scwacpbm_fixed_dates';
?>
    <div class="wrap">
        <div class="scwacpbm_content">
            <div class="scwacpbm_add">
                <div class="scwacpbm_add_head">Add a Parking Lot/Floor</div>
                <input class="scwacpbm_add_name" placeholder="Parking lot Name">
                <span class="scwacpbm_add_button"><i class="fa fa-plus" aria-hidden="true"></i> ADD</span>
            </div>
            <div class="scwacpbm_lots">
                <?php
                        if($parkLots){
                            foreach($parkLots as $lot){
                                $typesTB = $wpdb->prefix . 'scwacpbm_typeofslot';
                                $types = $wpdb->get_results( "SELECT * FROM $typesTB where lotid = ".$lot->id );

                                $slotsTB = $wpdb->prefix . 'scwacpbm_slots';
                                $slots = $wpdb->get_results( "SELECT * FROM $slotsTB where lotid = ".$lot->id );
                                ?>
                <div class="scwacpbm_lot">
                    <input class="scwacpbm_lot_id" value="<?php echo $lot->id ?>" type="hidden">
                    <span class="scwacpbm_lot_head">
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                        <span class="scwacpbm_lot_head_name"><?php echo $lot->parklotname ?></span>
                        <span class="scwacpbm_lot_head_delete"><i class="fa fa-trash" aria-hidden="true"></i></span>
                    </span>
                    <span class="scwacpbm_lot_content">
                        <span class="scwacpbm_lot_content_editname">
                            <span class="scwacpbm_lot_content_editname_head">Edit Name</span>
                            <input class="scwacpbm_lot_content_editname_name" value="<?php echo $lot->parklotname ?>">
                            <span class="scwacpbm_lot_content_editname_save"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</span>
                        </span>
                        <span class="scwacpbm_lotbg">
                            <span class="scwacpbm_lotbg_label">Lot Background</span>
                            <span class="scwacpbm_lotbg_con">
                                <input type="color" class="scwacpbm_lotbg_con_color" value="<?php echo $lot->lotcolor ?>">
                                <span class="scwacpbm_lotbg_con_or">OR</span>
                                <span class="scwacpbm_lotbg_con_bgpreview">
                                    <?php
                                                        if($lot->lotbg){
                                                            ?><img src="<?php echo $lot->lotbg ?>"><?php
                                                        }
                                                    ?>
                                </span>
                                <input type="text" class="scwacpbm_lotbg_con_image" value="<?php echo $lot->lotbg ?>">
                                <span class="scwacpbm_lotbg_con_upload">Upload Image</span>
                            </span>
                            <span class="scwacpbm_lotbg_save"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</span>
                        </span>
                        <span class="scwacpbm_lotsize">
                            <span class="scwacpbm_lotsize_label">Lot Size</span>
                            <input class="scwacpbm_lotsize_width" placeholder="Width (px)" value="<?php echo $lot->width ?>">
                            <input class="scwacpbm_lotsize_height" placeholder="Height (px)" value="<?php echo $lot->height ?>">
                            <span class="scwacpbm_lotsize_save"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</span>
                        </span>
                        <span class="scwacpbm_lottype">
                            <span class="scwacpbm_lottype_head">Types of Slot</span>
                            <span class="scwacpbm_lottype_add">
                                <span class="scwacpbm_lottype_add_head">Add a type of Slot</span>
                                <input class="scwacpbm_lottype_add_name" placeholder="Name of type">
                                <input class="scwacpbm_lottype_add_color" type="color">
                                <span class="scwacpbm_lottype_add_check">
                                    <label>Is Booked Type?</label>
                                    <input type="checkbox" class="scwacpbm_lottype_add_check_input">
                                </span>
                                <span class="scwacpbm_lottype_add_button"><i class="fa fa-plus" aria-hidden="true"></i> ADD</span>
                            </span>
                            <span class="scwacpbm_lottype_items">
                                <?php
                                                    if($types){
                                                        foreach($types as $type){
                                                            ?>
                                <span class="scwacpbm_lottype_item">
                                    <input value="<?php echo $type->id ?>" type="hidden" class="scwacpbm_lottype_item_id">
                                    <input value="<?php echo $type->typename ?>" name="scwacpbm_lottype_item_name<?php echo $lot->id ?>" class="scwacpbm_lottype_item_name">
                                    <input value="<?php echo $type->typecolor ?>" name="scwacpbm_lottype_item_color<?php echo $lot->id ?>" class="scwacpbm_lottype_item_color" type="color">
                                    <span class="scwacpbm_lottype_item_check">
                                        <label>Is Booked Type?</label>
                                        <input <?php if($type->isbooked) echo "checked='checked'" ?> type="radio" class="scwacpbm_lottype_item_check_input" name="scwacpbm_lottype_item_check_input<?php echo $lot->id ?>">
                                    </span>
                                    <span class="scwacpbm_lottype_item_save"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</span>
                                    <span class="scwacpbm_lottype_item_del"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</span>
                                </span>
                                <?php
                                                        }
                                                    }
                                                ?>
                            </span>
                        </span>
                        <span class="scwacpbm_lotprice">
                            <span class="scwacpbm_lotprice_head">Price Manage</span>
                            <span class="scwacpbm_lotprice_con">
                                <?php
                                                    if($types){
                                                        foreach($types as $type){
                                                            $typeid = $type->id;
                                                            $checkType = $wpdb->get_results( "SELECT * FROM $pricesTB where type = ".$typeid." and lotid = ".$lot->id );
                                                            if(isset($checkType[0]->price)) $typeprice = $checkType[0]->price;
                                                            else $typeprice = "";
                                                            if(isset($checkType[0]->pricetype)) $typepricetype = $checkType[0]->pricetype;
                                                            else $typepricetype = "";
                                                            ?>
                                <span class="scwacpbm_lotprice_item">
                                    <input class="scwacpbm_lotprice_item_id" value="<?php echo $type->id ?>" type="hidden">
                                    <span class="scwacpbm_lotprice_item_name"><?php echo $type->typename ?></span>
                                    <input class="scwacpbm_lotprice_item_price" value="<?php echo $typeprice ?>" placeholder="Price">
                                    <select class="scwacpbm_lotprice_item_type">
                                        <option <?php if($typepricetype == "hour") echo "selected='selected'" ?> value="hour">Per Hour</option>
                                        <option <?php if($typepricetype == "day") echo "selected='selected'" ?> value="day">Per Day</option>
                                        <option <?php if($typepricetype == "one") echo "selected='selected'" ?> value="one">One Time</option>
                                    </select>
                                </span>
                                <?php
                                                        }
                                                    }
                                                ?>
                            </span>
                            <span class="scwacpbm_lotprice_save"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</span>
                        </span>

                        <?php
                            // Display Fixed Dates
                            $fixedDates       = $wpdb->get_results("SELECT * from {$fixeddatesTB} where lotid = {$lot->id}");
                            $fixedDateFrom    = isset( $fixedDates[0]->from ) ? $fixedDates[0]->from :  '';
                            $fixedDateTo      = isset( $fixedDates[0]->to ) ? $fixedDates[0]->to :  '';
                            $fixedDateHeading = isset( $fixedDates[0]->heading ) ? $fixedDates[0]->heading :  '';
                        ?>
                        <div class="scwacpbm_fixeddates">
                            <div class="scwacpbm_fixeddates_head">Fixed Dates (Optional)</div>
                            <div class="scwacpbm_fixeddates_description">Required date format: Day-Month-Year Hour:Minutes</div>
                            <div class="scwacpbm_fixeddates_container">
                                <div class="scwacpbm_fixeddates_from">
                                    <div class="scwacpbm_fixeddates_from_label">From: </div>
                                    <input class="scwacpbm_fixeddates_from_input"
                                        placeholder="31-12-2023 12:30"
                                        value="<?php echo $fixedDateFrom; ?>">
                                </div>
                                <div class="scwacpbm_fixeddates_to">
                                    <div class="scwacpbm_fixeddates_to_label">To: </div>
                                    <input class="scwacpbm_fixeddates_to_input"
                                        placeholder="30-1-2024 1:20"
                                        value="<?php echo $fixedDateTo; ?>">
                                </div>
                                <div class="scwacpbm_fixeddates_heading">
                                    <div class="scwacpbm_fixeddates_label">Heading to Display:</div>
                                    <input class="scwacpbm_fixeddates_heading_input"
                                        placeholder="Reservation Dates From November 27 - January 30, 2024"
                                        value="<?php echo $fixedDateHeading; ?>">
                                </div>

                                <span class="scwacpbm_fixeddates_save"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</span>
                            </div>
                        </div>

                        <span class="scwacpbm_mapping">
                            <span class="scwacpbm_mapping_header">Slots Mapping</span>
                            <span class="scwacpbm_mapping_add">
                                <span class="scwacpbm_mapping_add_head">Add a Slot</span>
                                <input class="scwacpbm_mapping_add_label" placeholder="Label of Slot">
                                <select class="scwacpbm_mapping_add_type">
                                    <option value="">-- Select a Type --</option>
                                    <?php
                                                        if($types){
                                                            foreach($types as $type){
                                                                ?><option value="<?php echo $type->id ?>"><?php echo $type->typename ?></option><?php
                                                            }
                                                        }
                                                    ?>
                                </select>
                                <input class="scwacpbm_mapping_add_width" placeholder="Width (px)">
                                <input class="scwacpbm_mapping_add_height" placeholder="Height (px)">
                                <input class="scwacpbm_mapping_add_cx" placeholder="Margin Left (px)">
                                <input class="scwacpbm_mapping_add_cy" placeholder="Margin Top (px)">
                                <input class="scwacpbm_mapping_add_tilt" placeholder="Tilt (Degrees)">
                                <span class="scwacpbm_mapping_add_button"><i class="fa fa-plus" aria-hidden="true"></i> ADD</span>
                                <span class="scwacpbm_mapping_ref_button"><i class="fa fa-refresh" aria-hidden="true"></i> Reload Data</span>
                            </span>
                            <span class="scwacpbm_mapping_list">
                                <?php
                                                    if($slots){
                                                        foreach($slots as $slot){
                                                            ?>
                                <span class="scwacpbm_slot">
                                    <input class="scwacpbm_slot_id" value="<?php echo $slot->id ?>" type="hidden">
                                    <input class="scwacpbm_slot_label" value="<?php echo $slot->label ?>">
                                    <select class="scwacpbm_slot_type" name="scwacpbm_slot_type<?php echo $slot->id ?>">
                                        <option value="">-- Select a Type --</option>
                                        <?php
                                                                        if($types){
                                                                            foreach($types as $type){
                                                                                ?><option <?php if($type->id == $slot->type) echo "selected='selected'" ?> value="<?php echo $type->id ?>"><?php echo $type->typename ?></option><?php
                                                                            }
                                                                        }
                                                                    ?>
                                    </select>
                                    <input class="scwacpbm_slot_width" value="<?php echo $slot->width ?>">
                                    <input class="scwacpbm_slot_height" value="<?php echo $slot->height ?>">
                                    <input class="scwacpbm_slot_cx" value="<?php echo $slot->mleft ?>">
                                    <input class="scwacpbm_slot_cy" value="<?php echo $slot->mtop ?>">
                                    <input class="scwacpbm_slot_tilt" value="<?php echo $slot->tilt ?>">
                                    <span class="scwacpbm_slot_save"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</span>
                                    <span class="scwacpbm_slot_del"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</span>
                                </span>
                                <?php
                                                        }
                                                    }
                                                ?>
                            </span>
                            <span class="scwacpbm_mapping_listpreview">
                                <span class="scwacpbm_mapping_preview" style="width: <?php echo $lot->width ?>px; height: <?php echo $lot->height ?>px">
                                    <?php
                                                        if($lot->lotbg){
                                                            ?><img class="scwacpbm_mapping_preview_image" src="<?php echo $lot->lotbg ?>"><?php
                                                        }else{
                                                            ?><span style="background: <?php echo $lot->lotcolor ?>" class="scwacpbm_mapping_preview_color"><?php echo $lot->lotcolor ?></span><?php
                                                        }
                                                    ?>
                                    <span class="scwacpbm_mapping_preview_slots">
                                        <?php
                                                        if($slots){
                                                            foreach($slots as $slot){
                                                                $typeid = $slot->type;
                                                                $checkType = $wpdb->get_results( "SELECT typecolor FROM $typesTB where id = ".$typeid );
                                                                $bgcolor = $checkType[0]->typecolor;
                                                                ?>
                                        <span class="scwacpbm_mapping_preview_slot" style="background: <?php echo $bgcolor ?>;
                                                                width: <?php echo $slot->width ?>px;
                                                                height: <?php echo $slot->height ?>px;
                                                                line-height: <?php echo $slot->height ?>px;
                                                                left: <?php echo $slot->mleft ?>px;
                                                                top: <?php echo $slot->mtop ?>px;
                                                                transform: rotate(<?php echo $slot->tilt ?>deg);">
                                            <span class="scwacpbm_mapping_preview_slot_label"><?php echo $slot->label ?></span>
                                        </span>
                                        <?php
                                                            }
                                                        }
                                                    ?>
                                    </span>
                                </span>
                            </span>
                        </span>
                    </span>
                </div>
                <?php
                            }
                        }
                    ?>
            </div>
        </div>
    </div>
<?php
}

add_action( 'widgets_init', 'scwacpbm_widgets' );
add_action( 'plugins_loaded', 'scwacpbm_load' );

function scwacpbm_widgets(){
    register_widget('scwacpbm_class');
}
function scwacpbm_load(){
    global $mfpd;
    $mfpd = new scwacpbm_class();
}
class scwacpbm_class extends WP_Widget{
    function __construct(){
        parent::__construct(
              'scwacpbm_id',
              'SmartCms Car Parking Lot',
              array(
                  'description' => ''
              )
        );
        add_action( 'add_meta_boxes', array( $this, 'scwacpbm_add_tab_admin_product' ), 10, 2 );
    }
    function scwacpbm_add_tab_admin_product($post_type, $post)
    {
        global $wp_meta_boxes;
        $wp_meta_boxes[ 'product' ][ 'normal' ][ 'core' ][ 'scwacpbm' ][ 'title' ] = "SmartCms Car Parking Lot";
        $wp_meta_boxes[ 'product' ][ 'normal' ][ 'core' ][ 'scwacpbm' ][ 'args' ] = "";
        $wp_meta_boxes[ 'product' ][ 'normal' ][ 'core' ][ 'scwacpbm' ][ 'id' ] = "scwacpbm";
        $wp_meta_boxes[ 'product' ][ 'normal' ][ 'core' ][ 'scwacpbm' ][ 'callback' ] = "scwacpbm_add_tab_admin_product_display";
    }
}

function scwacpbm_add_tab_admin_product_display(){
    global $wpdb;

    $postId = $_GET['post'];

    if($postId){
        wp_register_script('scwacpbm-product-script', SCWACPBM .'js/product.js');
        wp_enqueue_script('scwacpbm-product-script');
        wp_register_style('scwacpbm-product-css', SCWACPBM .'css/product.css');
        wp_enqueue_style('scwacpbm-product-css');

        $tableParkLots = $wpdb->prefix . 'scwacpbm_parklots';
        $tableOrders = $wpdb->prefix . 'scwacpbm_orders';

        $lots = $wpdb->get_results( "SELECT * FROM $tableParkLots" );

        $tableProducts = $wpdb->prefix . 'scwacpbm_products';
        $checkPro = $wpdb->get_results("SELECT * from $tableProducts where productid = ".$postId);
        if(isset($checkPro[0]->lotid)) $lotid = $checkPro[0]->lotid;
        else $lotid = "";
        ?>
<div class="scwacpbm_content">
    <input type="hidden" class="scwacpbm_proid" value="<?php echo $postId ?>">
    <div class="scwacpbm_profile">
        <span class="scwacpbm_profile_add">Add Profile</span>
        <select class="scwacpbm_profile_choose">
            <option value="">-- Choose a Parking Lot --</option>
            <?php
                        foreach($lots as $pro){
                            ?><option value="<?php echo $pro->id ?>"><?php echo $pro->parklotname ?></option><?php
                        }
                    ?>
        </select>
        <span class="scwacpbm_profile_save">Save</span>
    </div>
    <?php
                if($lotid){
                    $parkLot = $wpdb->get_results( "SELECT * FROM $tableParkLots where id = ".$lotid );
                    $lotname = $parkLot[0]->parklotname;

                    $bookedseats = $wpdb->get_results( "SELECT * FROM $tableOrders where proid = ".$postId );
                    ?>
    <div class="scwacpbm_content_body">
        <span class="scwacpbm_content_body_name"><?php echo $lotname ?></span>
        <div class="scwacpbm_content_body_booked">
            <div class="scwacpbm_content_body_booked_header">Booked Slots Manage</div>
            <?php
                                if($bookedseats){
                                    foreach($bookedseats as $seatsitem){
                                        ?>
            <div class="scwacpbm_content_body_booked_item">
                <input class="scwacpbm_content_body_booked_item_id" type="hidden" value="<?php echo $seatsitem->id ?>">
                <span class="scwacpbm_content_body_booked_item_orderid">Order: <a class="" href="<?php echo get_site_url().$seatsitem->orderid ?>"><?php echo $seatsitem->orderid ?></a></span>
                <span class="scwacpbm_content_body_booked_item_seats"><?php echo str_replace("@", " ", $seatsitem->slots) ?></span>
                <span class="scwacpbm_content_body_booked_item_from"><?php echo $seatsitem->datefrom ?></span>
                <span class="scwacpbm_content_body_booked_item_to"><?php if($seatsitem->dateto) echo " to ".$seatsitem->dateto ?></span>
                <span class="scwacpbm_content_body_booked_item_delete">Delete</span>
            </div>
            <?php
                                    }
                                }
                            ?>
        </div>
    </div>
    <?php
                }
            ?>
</div>
<?php
    }
}

add_action('woocommerce_after_single_product', 'scwacpbm_fontend_single');
function scwacpbm_fontend_single(){
    global $product;
    global $wpdb;
    $proId = $product->id;

    $tableProducts = $wpdb->prefix . 'scwacpbm_products';
    $tableTypes = $wpdb->prefix . 'scwacpbm_typeofslot';
    $tablePrices = $wpdb->prefix . 'scwacpbm_prices';
    $tableLots = $wpdb->prefix . 'scwacpbm_parklots';
    $tableSlots = $wpdb->prefix . 'scwacpbm_slots';

    $selectedLot = $wpdb->get_results( "SELECT * FROM $tableProducts where productid = ".$proId );
    $lotid = $selectedLot[0]->lotid;

    $currencyS = get_woocommerce_currency_symbol( $currency );

    if($lotid){
        wp_register_script('scwacpbm-script-datetimepicker', SCWACPBM .'datetimepicker/jquery.datetimepicker.full.min.js');
        wp_enqueue_script('scwacpbm-script-datetimepicker');
        wp_register_style('scwacpbm-style-datetimepicker', SCWACPBM .'datetimepicker/jquery.datetimepicker.css');
        wp_enqueue_style('scwacpbm-style-datetimepicker');

        wp_register_script('scwacpbm-script-frontend', SCWACPBM .'js/front.js');
        wp_enqueue_script('scwacpbm-script-frontend');
        wp_register_style('scwacpbm-style-frontend', SCWACPBM .'css/front.css');
        wp_enqueue_style('scwacpbm-style-frontend');

        $types = $wpdb->get_results( "SELECT * FROM $tableTypes where lotid = ".$lotid );
        $lotdata = $wpdb->get_results( "SELECT * FROM $tableLots where id = ".$lotid );
        $slots = $wpdb->get_results( "SELECT * FROM $tableSlots where lotid = ".$lotid );

        ?>
<div class="scwacpbm_content" style="display: none">
    <input type="hidden" value="<?php echo SCWACPBM ?>" class="scwacpbm_url">
    <input type="hidden" value="<?php echo $proId ?>" class="product_id">
    <input type="hidden" value="<?php echo $lotid ?>" class="profileid">
    <input type="hidden" value="<?php echo $lotdata[0]->width ?>" class="width_config">
    <input type="hidden" value="<?php echo $lotdata[0]->height ?>" class="height_config">

    <div class="scwacpbm_types">
        <?php
                    if($types){
                        foreach($types as $type){
                            $selectPrice = $wpdb->get_results( "SELECT * FROM $tablePrices where type = ".$type->id );
                            if(isset($selectPrice[0]->price)) $price = $selectPrice[0]->price;
                            else $price = "";
                            if(isset($selectPrice[0]->pricetype)) $pricetype = $selectPrice[0]->pricetype;
                            else $pricetype = "";
                            ?>
        <div class="scwacpbm_type" style="background: <?php echo $type->typecolor ?>">
            <span class="scwacpbm_type_name"><?php echo $type->typename ?></span><br>
            <?php if($price){ ?>
            <span class="scwacpbm_type_price"><?php echo $currencyS.$price." per ".$pricetype ?></span>
            <?php } ?>
        </div>
        <?php
                            if($type->isbooked){
                                ?><input type="hidden" class="scwacpbm_type_booked" value="<?php echo $type->typecolor ?>"><?php
                            }
                        }
                    }
                ?>
    </div>
    <div class="scwacpbm_date">
        <span class="scwacpbm_date_head">Choose Date & Time</span>
        <span class="scwacpbm_date_from">
            <span class="scwacpbm_date_from_label">From</span><br>
            <input class="scwacpbm_date_from_input" name="scwacpbm_date_from_input" type="text">
        </span>
        <span class="scwacpbm_date_to">
            <span class="scwacpbm_date_to_label">To</span><br>
            <input class="scwacpbm_date_to_input" name="scwacpbm_date_to_input" type="text">
        </span>
    </div>
    <div class="scwacpbm_map">
        <div class="scwacpbm_map_head">Choose your Spots</div>
        <div class="scwacpbm_map_bg" style="width: <?php echo $lotdata[0]->width ?>px; height: <?php echo $lotdata[0]->height ?>px">
            <?php
                        if($lotdata[0]->lotbg){
                            ?><img class="scwacpbm_map_bg_img" src="<?php echo $lotdata[0]->lotbg ?>"><?php
                        }else{
                            ?><span class="scwacpbm_map_bg_color" style="background: <?php echo $lotdata[0]->lotcolor ?>">BG</span><?php
                        }
                    ?>
            <div class="scwacpbm_map_slots">
                <?php
                            if($slots){
                                foreach($slots as $slot){
                                    $typeid = $slot->type;
                                    $checkType = $wpdb->get_results( "SELECT * FROM $tableTypes where id = ".$typeid );
                                    $bgcolor = $checkType[0]->typecolor;

                                    $isbooked = $checkType[0]->isbooked;
                                    if($isbooked) $status = "seatbooked"; else $status = "seatavai";
                                    ?>
                <span objectdata="slot<?php echo $slot->label ?>" class="scwacpbm_map_slot <?php echo $status ?> " style="background: <?php echo $bgcolor ?>;
                                    width: <?php echo $slot->width ?>px;
                                    height: <?php echo $slot->height ?>px;
                                    line-height: <?php echo $slot->height ?>px;
                                    left: <?php echo $slot->mleft ?>px;
                                    top: <?php echo $slot->mtop ?>px;
                                    transform: rotate(<?php echo $slot->tilt ?>deg);">
                    <span class="scwacpbm_map_slot_label"><?php echo $slot->label ?></span>
                    <input type="hidden" value="<?php echo $bgcolor ?>" class="scwacpbm_map_slot_readcolor">
                </span>
                <?php
                                }
                            }
                        ?>
            </div>
        </div>
    </div>
</div>
<?php
    }
}

add_filter( 'woocommerce_cart_item_price', 'scwacpbm_change_product_price_display', 10, 3 );
function scwacpbm_change_product_price_display( $price, $product ){
    $customString = "";
    global $wpdb;
    $proId = $product["product_id"];

    $datefrom = $_SESSION["datefrom".$proId];
    $dateto = $_SESSION["dateto".$proId];
    $seats = $_SESSION["seats".$proId];

    $customString = "";

    if($seats){
        $sesSeats = explode("@", $seats);

        $customString .= "<br>Booked Slots:";
        foreach($sesSeats as $s){
            $customString .= " ".$s;
        }
    }
    if($datefrom){
        $customString .= "<br>From: ".$datefrom;
    }
    if($dateto){
        $customString .= "<br>To: ".$dateto;
    }
    return $price.$customString;
}

add_action( 'woocommerce_before_calculate_totals', 'scwacpbm_add_custom_price', 10, 1 );
function scwacpbm_add_custom_price( $cart_object ){
    global $wpdb;
    global $woocommerce;
    $woove = $woocommerce->version;

    if ( is_admin() && !defined('DOING_AJAX') )
        return;

    $tableProducts = $wpdb->prefix . 'scwacpbm_products';
    $tableSlots = $wpdb->prefix . 'scwacpbm_slots';
    $tablePrices = $wpdb->prefix . 'scwacpbm_prices';

    foreach ( $cart_object->get_cart() as $cart_item ){
        if( (float)$woove < 3 ){
            $proId = $cart_item['data']->id;
            $sale_price = $cart_item['data']->price;
        }else{
            $proId = $cart_item['data']->get_id();
            $cuprice = $cart_item['data']->get_data();
            $sale_price = $cuprice["sale_price"];
            if(!$sale_price) $sale_price = $cuprice["regular_price"];
        }
        $totalPrice = 0;

        $selectedLot = $wpdb->get_results( "SELECT * FROM $tableProducts where productid = ".$proId );
        $lotid = $selectedLot[0]->lotid;

        if($lotid){
            $datefrom = $_SESSION["datefrom".$proId];
            $dateto = $_SESSION["dateto".$proId];
            $seats = $_SESSION["seats".$proId];

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

        if($totalPrice)
            $cart_item['data']->set_price( $totalPrice / $cart_item['quantity'] );
    }
}

add_filter( 'woocommerce_order_item_name', 'scwacpbm_order_complete' , 10, 2 );
function scwacpbm_order_complete( $link, $item ){
    global $wpdb;
    global $wp;

    $proId = $item["product_id"];
    $order_id  = absint($wp->query_vars['order-received']);

    $customString = "";
    if($proId && $order_id){
        $orderTable = $wpdb->prefix . 'scwacpbm_orders';

        $datefrom = $_SESSION["datefrom".$proId];
        $dateto = $_SESSION["dateto".$proId];
        $seats = $_SESSION["seats".$proId];

        if($seats){
            $sesSeats = explode("@", $seats);

            $customString .= "<br>Booked Slots:";
            foreach($sesSeats as $s){
                $customString .= " ".$s;
            }
        }
        if($datefrom){
            $customString .= "<br>From: ".$datefrom;
        }
        if($dateto){
            $customString .= "<br>To: ".$dateto;
        }

        $checkOrder = $wpdb->get_results( "SELECT * FROM $orderTable where orderid = '".$order_id."'");
        if(!$checkOrder){
            $wpdb->insert( $orderTable,
                array(
                    'orderid' => $order_id,
                    'proid' => $proId,
                    'slots' => $seats,
                    'datefrom' => $datefrom,
                    'dateto' => $dateto
                )
            );
        }
    }

    //unset($_SESSION["seats".$proId]);
    //unset($_SESSION["datefrom".$proId]);
    //unset($_SESSION["dateto".$proId]);
    return $link.$customString."<br>";
}

add_action( 'woocommerce_before_order_itemmeta', 'scwacpbm_admin_edit_order', 10, 3 );
function scwacpbm_admin_edit_order( $item_id, $item, $product ){
    global $wpdb;
    $proId = $product->id;
    $postId = $_GET['post'];

    $orderSeatTable = $wpdb->prefix . 'scwacpbm_orders';
    $order = $wpdb->get_results("SELECT * from $orderSeatTable where proid = ".$proId." and orderid = '".$postId."'");

    $customString = "";
    if($order[0]->slots){
        $sesSeats = explode("@", $order[0]->slots);

        $customString .= "<br>Booked Slots:";
        foreach($sesSeats as $s){
            $customString .= " ".$s;
        }
    }
    if($order[0]->datefrom){
        $customString .= "<br>From: ".$order[0]->datefrom;
    }
    if($order[0]->dateto){
        $customString .= "<br>To: ".$order[0]->dateto;
    }

    echo $customString;
}

// wordpress post
add_action( 'add_meta_boxes', 'scwacpbm_add_tab_admin_post', 10, 2 );
function scwacpbm_add_tab_admin_post($post_type, $post){
    global $wp_meta_boxes;
    $wp_meta_boxes[ 'post' ][ 'normal' ][ 'core' ][ 'scwacpbm' ][ 'title' ] = "Car Park Booking";
    $wp_meta_boxes[ 'post' ][ 'normal' ][ 'core' ][ 'scwacpbm' ][ 'args' ] = "";
    $wp_meta_boxes[ 'post' ][ 'normal' ][ 'core' ][ 'scwacpbm' ][ 'id' ] = "scwacpbm";
    $wp_meta_boxes[ 'post' ][ 'normal' ][ 'core' ][ 'scwacpbm' ][ 'callback' ] = "scwacpbm_add_tab_admin_post_display";
}
function scwacpbm_add_tab_admin_post_display(){
    global $wpdb;
    $postId = $_GET['post'];

    if($postId && get_post_type($postId) == "post"){
        wp_register_script('scwacpbm-product-script', SCWACPBM .'js/product.js');
        wp_enqueue_script('scwacpbm-product-script');
        wp_register_style('scwacpbm-product-css', SCWACPBM .'css/product.css?v=1.1');
        wp_enqueue_style('scwacpbm-product-css');

        $tableParkLots = $wpdb->prefix . 'scwacpbm_parklots';
        $tableOrders = $wpdb->prefix . 'scwacpbm_orders';

        $lots = $wpdb->get_results( "SELECT * FROM $tableParkLots" );

        $tableProducts = $wpdb->prefix . 'scwacpbm_products';
        $checkPro = $wpdb->get_results("SELECT * from $tableProducts where productid = ".$postId);
        if(isset($checkPro[0]->lotid)) $lotid = $checkPro[0]->lotid;
        else $lotid = "";
        ?>
<div class="scwacpbm_content">
    <input type="hidden" class="scwacpbm_proid" value="<?php echo $postId ?>">
    <div class="scwacpbm_profile">
        <span class="scwacpbm_profile_add">Add Profile</span>
        <select class="scwacpbm_profile_choose">
            <option value="">-- Choose a Parking Lot --</option>
            <?php
                        foreach($lots as $pro){
                            ?><option value="<?php echo $pro->id ?>"><?php echo $pro->parklotname ?></option><?php
                        }
                    ?>
        </select>
        <span class="scwacpbm_profile_save">Save</span>
    </div>
    <?php
                if($lotid){
                    $parkLot = $wpdb->get_results( "SELECT * FROM $tableParkLots where id = ".$lotid );
                    $lotname = $parkLot[0]->parklotname;

                    $bookedseats = $wpdb->get_results( "SELECT * FROM $tableOrders where proid = ".$postId );
                    ?>
    <div class="scwacpbm_content_body">
        <span class="scwacpbm_content_body_name"><?php echo $lotname ?></span>
        <div class="scwacpbm_content_body_booked">
            <div class="scwacpbm_content_body_booked_header">Booked Slots Manage</div>
            <?php
                                if($bookedseats){
                                    foreach($bookedseats as $seatsitem){
                                        ?>
            <div class="scwacpbm_content_body_booked_item">
                <input class="scwacpbm_content_body_booked_item_id" type="hidden" value="<?php echo $seatsitem->id ?>">
                <span class="scwacpbm_content_body_booked_item_seats"><?php echo str_replace("@", " ", $seatsitem->slots) ?></span>
                <span class="scwacpbm_content_body_booked_item_from"><?php echo $seatsitem->datefrom ?></span>
                <span class="scwacpbm_content_body_booked_item_to"><?php if($seatsitem->dateto) echo " to ".$seatsitem->dateto ?></span>
                <?php if($seatsitem->name){ ?>
                <span class="scwacpbm_orders_item_name"><?php if($seatsitem->name) echo esc_attr($seatsitem->name) ?></span>
                <?php } ?>
                <?php if($seatsitem->address){ ?>
                <span class="scwacpbm_orders_item_address"><?php if($seatsitem->address) echo esc_attr($seatsitem->address) ?></span>
                <?php } ?>
                <?php if($seatsitem->email){ ?>
                <span class="scwacpbm_orders_item_email"><?php if($seatsitem->email) echo esc_attr($seatsitem->email) ?></span>
                <?php } ?>
                <?php if($seatsitem->phone){ ?>
                <span class="scwacpbm_orders_item_phone"><?php if($seatsitem->phone) echo esc_attr($seatsitem->phone) ?></span>
                <?php } ?>
                <?php if($seatsitem->note){ ?>
                <span class="scwacpbm_orders_item_note"><?php if($seatsitem->note) echo esc_attr($seatsitem->note) ?></span>
                <?php } ?>
                <span class="scwacpbm_orders_item_total"><?php echo esc_attr("$".$seatsitem->total) ?></span>
                <span class="scwacpbm_content_body_booked_item_delete">Delete</span>
            </div>
            <?php
                                    }
                                }
                            ?>
        </div>
    </div>
    <?php
                }
            ?>
</div>
<?php
    }
}

add_filter( 'the_content', 'scwacpbm_content' );
function scwacpbm_content($content){
    global $wpdb;
    global $post;
    $proId = $post->ID;

    $currencyS = "$";

    $tableProducts = $wpdb->prefix . 'scwacpbm_products';
    $tableTypes = $wpdb->prefix . 'scwacpbm_typeofslot';
    $tablePrices = $wpdb->prefix . 'scwacpbm_prices';
    $tableLots = $wpdb->prefix . 'scwacpbm_parklots';
    $tableSlots = $wpdb->prefix . 'scwacpbm_slots';

    $selectedLot = $wpdb->get_results( "SELECT * FROM $tableProducts where productid = ".$proId );
    $lotid = $selectedLot[0]->lotid;

    if($lotid && !is_admin() && get_post_type($proId)=="post"){
        ob_start();

        wp_register_script('scwacpbm-script-datetimepicker', SCWACPBM .'datetimepicker/jquery.datetimepicker.full.min.js');
        wp_enqueue_script('scwacpbm-script-datetimepicker');
        wp_register_style('scwacpbm-style-datetimepicker', SCWACPBM .'datetimepicker/jquery.datetimepicker.css');
        wp_enqueue_style('scwacpbm-style-datetimepicker');

        wp_register_script('scwacpbm-script-frontend', SCWACPBM .'js/front.js?v=1.0');
        wp_enqueue_script('scwacpbm-script-frontend');
        wp_register_style('scwacpbm-style-frontend', SCWACPBM .'css/front.css?v=1.0');
        wp_enqueue_style('scwacpbm-style-frontend');

        $types = $wpdb->get_results( "SELECT * FROM $tableTypes where lotid = ".$lotid );
        $lotdata = $wpdb->get_results( "SELECT * FROM $tableLots where id = ".$lotid );
        $slots = $wpdb->get_results( "SELECT * FROM $tableSlots where lotid = ".$lotid );

        ?>
<div class="scwacpbm_content <?php echo get_post_type($proId) ?>" style="display: none">
    <input type="hidden" value="<?php echo SCWACPBM ?>" class="scwacpbm_url">
    <input type="hidden" value="<?php echo $proId ?>" class="product_id">
    <input type="hidden" value="<?php echo $lotid ?>" class="profileid">
    <input type="hidden" value="<?php echo $lotdata[0]->width ?>" class="width_config">
    <input type="hidden" value="<?php echo $lotdata[0]->height ?>" class="height_config">
    <input type="hidden" value="<?php echo esc_attr(get_post_type($proId)) ?>" class="scw_posttype">

    <div class="scwacpbm_types">
        <?php
                    if($types){
                        foreach($types as $type){
                            $selectPrice = $wpdb->get_results( "SELECT * FROM $tablePrices where type = ".$type->id );
                            if(isset($selectPrice[0]->price)) $price = $selectPrice[0]->price;
                            else $price = "";
                            if(isset($selectPrice[0]->pricetype)) $pricetype = $selectPrice[0]->pricetype;
                            else $pricetype = "";
                            ?>
        <div class="scwacpbm_type" style="background: <?php echo $type->typecolor ?>">
            <span class="scwacpbm_type_name"><?php echo $type->typename ?></span><br>
            <?php if($price){ ?>
            <span class="scwacpbm_type_price"><?php echo $currencyS.$price." per ".$pricetype ?></span>
            <?php } ?>
        </div>
        <?php
                            if($type->isbooked){
                                ?><input type="hidden" class="scwacpbm_type_booked" value="<?php echo $type->typecolor ?>"><?php
                            }
                        }
                    }
                ?>
    </div>
    <div class="scwacpbm_date">
        <span class="scwacpbm_date_head">Choose Date & Time</span>
        <span class="scwacpbm_date_from">
            <span class="scwacpbm_date_from_label">From</span><br>
            <input class="scwacpbm_date_from_input" name="scwacpbm_date_from_input" type="text">
        </span>
        <span class="scwacpbm_date_to">
            <span class="scwacpbm_date_to_label">To</span><br>
            <input class="scwacpbm_date_to_input" name="scwacpbm_date_to_input" type="text">
        </span>
    </div>
    <div class="scwacpbm_map">
        <div class="scwacpbm_map_head">Choose your Spots</div>
        <div class="scwacpbm_map_bg" style="width: <?php echo $lotdata[0]->width ?>px; height: <?php echo $lotdata[0]->height ?>px">
            <?php
                        if($lotdata[0]->lotbg){
                            ?><img class="scwacpbm_map_bg_img" src="<?php echo $lotdata[0]->lotbg ?>"><?php
                        }else{
                            ?><span class="scwacpbm_map_bg_color" style="background: <?php echo $lotdata[0]->lotcolor ?>">BG</span><?php
                        }
                    ?>
            <div class="scwacpbm_map_slots">
                <?php
                            if($slots){
                                foreach($slots as $slot){
                                    $typeid = $slot->type;
                                    $checkType = $wpdb->get_results( "SELECT * FROM $tableTypes where id = ".$typeid );
                                    $bgcolor = $checkType[0]->typecolor;

                                    $isbooked = $checkType[0]->isbooked;
                                    if($isbooked) $status = "seatbooked"; else $status = "seatavai";
                                    ?>
                <span objectdata="slot<?php echo $slot->label ?>" class="scwacpbm_map_slot <?php echo $status ?> " style="background: <?php echo $bgcolor ?>;
                                    width: <?php echo $slot->width ?>px;
                                    height: <?php echo $slot->height ?>px;
                                    line-height: <?php echo $slot->height ?>px;
                                    left: <?php echo $slot->mleft ?>px;
                                    top: <?php echo $slot->mtop ?>px;
                                    transform: rotate(<?php echo $slot->tilt ?>deg);">
                    <span class="scwacpbm_map_slot_label"><?php echo $slot->label ?></span>
                    <input type="hidden" value="<?php echo $bgcolor ?>" class="scwacpbm_map_slot_readcolor">
                </span>
                <?php
                                }
                            }
                        ?>
            </div>
        </div>
    </div>

    <div class="scwacpbm_form">
        <div class="scwacpbm_total">
            <span>Total: $</span>
            <span class="scwacpbm_total_value">0</span>
        </div>
        <div class="scwacpbm_sendform">
            <div class="scwacpbm_form_item scw_form_name">
                <label>Name</label>
                <input class="scwacpbm_form_name_input" type="text">
            </div>
            <div class="scwacpbm_form_item scw_form_address">
                <label>Address</label>
                <input class="scwacpbm_form_address_input" type="text">
            </div>
            <div class="scwacpbm_form_item scw_form_email">
                <label>Email</label>
                <input class="scwacpbm_form_email_input" type="text">
            </div>
            <div class="scwacpbm_form_item scw_form_phone">
                <label>Phone</label>
                <input class="scwacpbm_form_phone_input" type="text">
            </div>
            <div class="scwacpbm_form_item scw_form_note">
                <label>Note</label>
                <textarea class="scwacpbm_form_note_input"></textarea>
            </div>
            <div class="scwacpbm_form_item"><span class="scwacpbm_form_submit">Submit</span></div>
        </div>
    </div>
</div>
<?php
        $string = ob_get_contents();
        ob_end_clean();
        $content .= $string;
    }
    return $content;
}