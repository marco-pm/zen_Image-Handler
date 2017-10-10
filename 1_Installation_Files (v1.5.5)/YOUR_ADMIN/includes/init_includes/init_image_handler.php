<?php 
// -----
// Part of the "Image Handler" plugin, v5.0.0 and later, by Cindy Merkin a.k.a. lat9 (cindy@vinosdefrutastropicales.com)
// Copyright (c) 2017 Vinos de Frutas Tropicales
//
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

define('IH_CURRENT_VERSION', '5.0.0-beta1');

// -----
// Wait until an admin is logged in before seeing if any initialization steps need to be performed.
// That ensures that "someone" will see the plugin's installation/update messages!
//
if (isset($_SESSION['admin_id'])) {
    // -----
    // Determine the configuration group associated with "Images"; if not found, use the standard
    // configuration group ID of 4.
    //
    $configurationGroupTitle = 'Images';
    $configuration = $db->Execute(
        "SELECT configuration_group_id 
           FROM " . TABLE_CONFIGURATION_GROUP . " 
          WHERE configuration_group_title = '$configurationGroupTitle' 
          LIMIT 1"
    );
    $cgi = ($configuration->EOF) ? 4 : $configuration->fields['configuration_group_id'];

    // ----
    // Perform the plugin's initial install, if not currently present.
    //
    if (!defined('IH_VERSION')) {
        // -----
        // Create the "base" configuration items for Image Handler's initial installation.
        //
        $configuration_items = array(
            array(
                'IH_RESIZE', 
                'no', 
                1001, 
                array('yes', 'no'),
                'IH resize images',
                'Select either -no- which is old Zen-Cart behaviour or -yes- to activate automatic resizing and caching of images. --Note: If you select -no-, all of the Image Handler specific image settings will be unavailable including: image filetype selection, background colors, compression, image hover, and watermarking-- If you want to use ImageMagick you have to specify the location of the <strong>convert</strong> binary in <em>includes/extra_configures/bmz_image_handler_conf.php</em>.'
            ),
            array(
                'SMALL_IMAGE_FILETYPE', 
                'no_change', 
                1011, 
                array('gif', 'jpg', 'png', 'no_change'),
                'IH small images filetype',
                'Select one of -jpg-, -gif- or -png-. Older versions of Internet Explorer -v6.0 and older- will have issues displaying -png- images with transparent areas. You better stick to -gif- for transparency if you MUST support older versions of Internet Explorer. However -png- is a MUCH BETTER format for transparency. Use -jpg- or -png- for larger images. -no_change- is old zen-cart behavior, use the same file extension for small images as uploaded image'
            ),
            array(
                'SMALL_IMAGE_BACKGROUND', 
                '255:255:255', 
                1021, 
                false,
                'IH small images background',
                'If converted from an uploaded image with transparent areas, these areas become the specified color. Set to -transparent- to keep transparency.'
            ),
            array(
                'SMALL_IMAGE_QUALITY', 
                85, 
                1031, 
                false,
                'IH small images compression quality',
                'Specify the desired image quality for small jpg images, decimal values ranging from 0 to 100. Higher is better quality and takes more space. Default is 85 which is ok unless you have very specific needs.'
            ),
            array(
                'WATERMARK_SMALL_IMAGES', 
                'no', 
                1041, 
                array('no', 'yes'),
                'IH small images watermark',
                'Set to -yes-, if you want to show watermarked small images instead of unmarked small images.'
            ),
            array(
                'ZOOM_SMALL_IMAGES', 
                'yes', 
                1051, 
                array('no', 'yes'),
                'IH small images zoom on hover'
            ),
            array(
                'ZOOM_IMAGE_SIZE', 
                'Medium', 
                1061, 
                array('Medium', 'Large'),
                'IH small images zoom on hover size',
                'Set to -Medium-, if you want to the zoom on hover display to use the medium sized image. Otherwise, to use the large sized image on hover, set to -Large-'
             ),
            array(
                'MEDIUM_IMAGE_FILETYPE', 
                'no_change', 
                1071, 
                array('gif', 'jpg', 'png', 'no_change'),
                'IH medium images filetype',
                'Select one of -jpg-, -gif- or -png-. Older versions of Internet Explorer -v6.0 and older- will have issues displaying -png- images with transparent areas. You better stick to -gif- for transparency if you MUST support older versions of Internet Explorer. However -png- is a MUCH BETTER format for transparency. Use -jpg- or -png- for larger images. -no_change- is old zen-cart behavior, use the same file extension for medium images as uploaded image-s.'
            ),
            array(
                'MEDIUM_IMAGE_BACKGROUND', 
                '255:255:255', 
                1081, 
                false,
                'IH medium images background',
                'If converted from an uploaded image with transparent areas, these areas become the specified color. Set to -transparent- to keep transparency.'
            ),
            array(
                'MEDIUM_IMAGE_QUALITY', 
                85, 
                1091, 
                false,
                'IH medium images compression quality',
                'Specify the desired image quality for medium jpg images, decimal values ranging from 0 to 100. Higher is better quality and takes more space. Default is 85 which is ok unless you have very specific needs.'
            ),
            array(
                'WATERMARK_MEDIUM_IMAGES', 
                'no', 
                1101, 
                array('no', 'yes'),
                'IH medium images watermark',
                'Set to -yes-, if you want to show watermarked medium images instead of unmarked medium images.'
            ),
            array(
                'LARGE_IMAGE_FILETYPE', 
                'no_change', 
                1111, 
                array('gif', 'jpg', 'png', 'no_change'),
                'IH large images filetype',
                'Select one of -jpg-, -gif- or -png-. Older versions of Internet Explorer -v6.0 and older- will have issues displaying -png- images with transparent areas. You better stick to -gif- for transparency if you MUST support older versions of Internet Explorer. However -png- is a MUCH BETTER format for transparency. Use -jpg- or -png- for larger images. -no_change- is old zen-cart behavior, use the same file extension for large images as uploaded image-s.'
            ),
            array(
                'LARGE_IMAGE_BACKGROUND', 
                '255:255:255', 
                1121, 
                false,
                'IH large images background',
                'If converted from an uploaded image with transparent areas, these areas become the specified color. Set to -transparent- to keep transparency.'
            ),
            array(
                'LARGE_IMAGE_QUALITY', 
                85, 
                1131, 
                false,
                'IH large images compression quality',
                'Specify the desired image quality for large jpg images, decimal values ranging from 0 to 100. Higher is better quality and takes more space. Default is 85 which is ok unless you have very specific needs.'
            ),
            array(
                'WATERMARK_LARGE_IMAGES', 
                'no', 
                1141, 
                array('no', 'yes'),
                'IH large images watermark',
                'Set to -yes-, if you want to show watermarked large images instead of unmarked large images.'
            ),
            array(
                'LARGE_IMAGE_MAX_WIDTH', 
                750, 
                1151, 
                false,
                'IH large images maximum width',
                'Specify a maximum width for your large images. If width and height are empty or set to 0, no resizing of large images is done.'
            ),
            array(
                'LARGE_IMAGE_MAX_HEIGHT', 
                550, 
                1161, 
                false,
                'IH large images maximum height',
                'Specify a maximum height for your large images. If width and height are empty or set to 0, no resizing of large images is done.'
            ),
            array(
                'WATERMARK_GRAVITY', 
                'Center', 
                1171, 
                array('Center', 'NorthWest', 'North', 'NorthEast', 'East', 'SouthEast', 'South', 'SouthWest', 'West'),
                'IH watermark gravity',
                'Select the position for the watermark relative to the image-s canvas. Default is <strong>Center</Strong>.'
            )
        );
        foreach ($configuration_items as $menu_item) {
            $config_key = $menu_item[0];
            $config_default = $menu_item[1];
            $sort = $menu_item[2];
            $config_values = $menu_item[3];
            $config_title = $menu_item[4];
            $config_descr = $menu_item[5];
            
            $set_function = 'NULL';
            if (is_array($config_values)) {
                $value_string = '';
                foreach ($values as $value) {
                    $value_string .= "''" . $value . "'',";
                }
                $set_function = "'zen_cfg_select_option(array(" . substr($value_string, 0, -1) . ",'";
            }
            $db->Execute(
                "INSERT INTO " . TABLE_CONFIGURATION . "
                    (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function)
                 VALUES
                    ('$config_title', '$config_key', '$config_default', '$config_desc', $cgi, $sort_order, now(), NULL, $set_function)"
            );
        }
        
        // -----
        // Create a configuration item that will display the plugin's current version.
        //
        $db->Execute(
            "INSERT INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function)
             VALUES
                ('IH version', 'IH_VERSION', '" . IH_CURRENT_VERSION . "', 'Displays the currently-installed version of <em>Image Handler</em>.', $cgi, 1000, now(), NULL, 'trim(')"
        );
        define ('IH_VERSION', '0.0.0');
        
        // -----
        // Remove "legacy" Image Handler configuration items.
        //
        $db->Execute(
            "DELETE FROM " . TABLE_CONFIGURATION . "
              WHERE configuration_key IN (
                'ZOOM_GRAVITY', 'SMALL_IMAGE_HOTZONE', 'ZOOM_MEDIUM_IMAGES', 'MEDIUM_IMAGE_HOTZONE',
                'ADDITIONAL_IMAGE_FILETYPE', 'ADDITIONAL_IMAGE_BACKGROUND', 'SHOW_UPLOADED_IMAGES'
              )"
        );
        
        // -----
        // Display a message to the current admin, letting them know that the plugin's been installed.
        //
        $messageStack->add(sprintf(IH_TEXT_MESSAGE_INSTALLED, IH_CURRENT_VERSION), 'success');
      
        // -----
        // Register the Image Handler tool within the Zen Cart admin menus.
        //
        zen_register_admin_page('configImageHandler4', 'BOX_TOOLS_IMAGE_HANDLER', 'FILENAME_IMAGE_HANDLER', '', 'tools', 'Y', 14);
    }

    // -----
    // Update the configuration table to reflect the current version, if it's not already set.
    //
    // Note: This update also "moves" the Image-Handler version value from a hidden configuration group to the
    //       "Images" configuration for pre-v5.0.0 updates.
    //
    if (IH_VERSION != IH_CURRENT_VERSION) {
        $db->Execute(
            "UPDATE " . TABLE_CONFIGURATION . " 
                SET configuration_value = '" . IH_CURRENT_VERSION . "',
                    configuration_group_id = $cgi,
                    sort_order = 1000
              WHERE configuration_key = 'IH_VERSION'
              LIMIT 1"
        );
        if (IH_VERSION != '0.0.0') {
            $messageStack->add(sprintf(IH_TEXT_MESSAGE_UPDATED, IH_VERSION, IH_CURRENT_VERSION), 'success');
        }
    }
}