<?php

$strings = 'tinyMCE.addI18n({' . _WP_Editors::$mce_locale . ':{
    carton:{
        insert: "' . esc_js( __( 'Insert Shortcode', 'carton' ) ) . '",
        price_button: "' . esc_js( __( 'Product price/cart button', 'carton' ) ) . '",
        product_by_sku: "' . esc_js( __( 'Product by SKU/ID', 'carton' ) ) . '",
        products_by_sku: "' . esc_js( __( 'Products by SKU/ID', 'carton' ) ) . '",
        product_categories: "' . esc_js( __( 'Product categories', 'carton' ) ) . '",
        products_by_cat_slug: "' . esc_js( __( 'Products by category slug', 'carton' ) ) . '",
        recent_products: "' . esc_js( __( 'Recent products', 'carton' ) ) . '",
        featured_products: "' . esc_js( __( 'Featured products', 'carton' ) ) . '",
        shop_messages: "' . esc_js( __( 'Shop Messages', 'carton' ) ) . '",
        pages: "' . esc_js( __( 'Pages', 'carton' ) ) . '",
        cart: "' . esc_js( __( 'Cart', 'carton' ) ) . '",
        checkout: "' . esc_js( __( 'Checkout', 'carton' ) ) . '",
        order_tracking: "' . esc_js( __( 'Order tracking', 'carton' ) ) . '",
        my_account: "' . esc_js( __( 'My Account', 'carton' ) ) . '",
        edit_address: "' . esc_js( __( 'Edit Address', 'carton' ) ) . '",
        change_password: "' . esc_js( __( 'Change Password', 'carton' ) ) . '",
        view_order: "' . esc_js( __( 'View Order', 'carton' ) ) . '",
        pay: "' . esc_js( __( 'Pay', 'carton' ) ) . '",
        thankyou: "' . esc_js( __( 'Thankyou', 'carton' ) ) . '",
    }
}})';