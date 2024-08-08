<?php
/**
 * Plugin Name: WooCommerce Price per Unit
 * Description: Display the price per unit of a product, under the price. 
 * Works with variable products and on sale products
 * + with "WooCommerce Product Bundles".
 * Requires WooCommerce and Meta Box extensions. 
 * Please adapt source code to your needs.
 * Author: Baptiste Mourey
 * Version 0.5: changed hook + debug issue for variable product
 * Version 0.4: added "WooCommerce Product Bundles" support
 * Version 0.3: debug issue #1
 * Version 0.2: moved from div to p.
 * Version 0.1: first commit.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Hook to display the price per unit after the SKU
add_action('woocommerce_product_meta_start', 'ppu_display', 10);

function ppu_display()
{
    global $product;

    // Retrieve the custom fields for the unit of measurement (e.g., kg, l) and the coefficient (e.g., 0.100 for 100g), which are used to calculate the price per unit.
    $unit = get_post_meta(get_the_ID(), 'kw_UNITE', true);
    $coeff = (float) get_post_meta(get_the_ID(), 'kw_COEFF_UNITE', true);

    // Validate coefficient
    if ( empty($coeff) || $coeff <= 0 ) {
        return;
    }

    // Get product prices
    $reg_price = (float) $product->get_regular_price();
    $sale_price = (float) $product->get_sale_price();

    // Check if it's a variable product
    if ( $product->is_type('variable') ) {
        $var_min_price = (float) $product->get_variation_price('min', true);
        $var_reg_price = (float) $product->get_variation_regular_price('min', true);
        
        // Display the price per unit
        if ( $product->is_on_sale() ) {
            echo '<span class="sku_wrapper" style="display: block;"><del>' . wc_price($var_reg_price / $coeff) . ' / ' . esc_html($unit) . '</del> ' . wc_price($var_min_price / $coeff) . ' / ' . esc_html($unit) . '</span>';
        } else {
            echo '<span class="sku_wrapper" style="display: block;">' . wc_price($var_min_price / $coeff) . ' / ' . esc_html($unit) . '</span>';
        }
    } elseif ( $product->is_type('bundle') ) {
        $bundle_reg_price = (float) get_post_meta(get_the_ID(), '_regular_price', true);
        $bundle_sale_price = (float) get_post_meta(get_the_ID(), '_sale_price', true);
        
        if ( $product->is_on_sale() ) {
            echo '<span class="sku_wrapper" style="display: block;"><del>' . wc_price($bundle_reg_price / $coeff) . ' / ' . esc_html($unit) . '</del> ' . wc_price($bundle_sale_price / $coeff) . ' / ' . esc_html($unit) . '</span>';
        } else {
            echo '<span class="sku_wrapper" style="display: block;">' . wc_price($bundle_reg_price / $coeff) . ' / ' . esc_html($unit) . '</span>';
        }
    } else {
        if ( $product->is_on_sale() ) {
            echo '<span class="sku_wrapper" style="display: block;"><del>' . wc_price($reg_price / $coeff) . ' / ' . esc_html($unit) . '</del> ' . wc_price($sale_price / $coeff) . ' / ' . esc_html($unit) . '</span>';
        } else {
            echo '<span class="sku_wrapper" style="display: block;">' . wc_price($reg_price / $coeff) . ' / ' . esc_html($unit) . '</span>';
        }
    }
}

add_filter( 'rwmb_meta_boxes', 'ppu_meta_box' );

function ppu_meta_box( $meta_boxes ) {
    $meta_boxes[] = [
        'title'      => esc_html__( 'Prix par unité', 'online-generator' ),
        'id'         => 'price_per_unit',
        'post_types' => ['product'],
        'context'    => 'normal',
        'fields'     => [
            [
                'type'        => 'text',
                'name'        => esc_html__( 'Coefficient (poids net)', 'online-generator' ),
                'desc'        => esc_html__( 'Doit être un chiffre avec un "." à la place de la virgule. Ex pour 100g : 0.100', 'online-generator' ),
                'placeholder' => esc_html__( '0.100', 'online-generator' ),
                'id'          => 'kw_COEFF_UNITE',
            ],
            [
                'type'        => 'text',
                'name'        => esc_html__( 'Unité (kg, l, unité)', 'online-generator' ),
                'placeholder' => esc_html__( 'kg ou l ou unité', 'online-generator' ),
                'id'          => 'kw_UNITE',
            ],
            [
                'type' => 'heading',
                'name' => esc_html__( 'Que font ces données ?', 'online-generator' ),
                'desc' => esc_html__( 'Elles permettent d\'afficher le prix au kg / l / unité d\'un produit sous la forme suivante : [(prix min. du produit) / (coefficient)] € / [unité]', 'online-generator' ),
            ],
        ],
    ];

    return $meta_boxes;
}
?>
