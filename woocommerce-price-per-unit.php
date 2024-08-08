<?php
/**
 * Plugin Name: WooCommerce Price per Unit
 * Description: Display the price per unit of a product, under the price. 
 * Works with variable products and on sale products
 * + with "WooCommerce Product Bundles".
 * Requires WooCommerce and Meta Box extensions. 
 * Please adapt source code to your needs.
 * Author: Baptiste Mourey
 * Version 0.4: added "WooCommerce Product Bundles" support
 * Version 0.3: debug issue #1
 * Version 0.2: moved from div to p.
 * Version 0.1: first commit.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**/
add_action('woocommerce_single_product_summary', 'price_per_unit', 0);
function price_per_unit()
{
    // if (current_user_can('manage_options')) {
        // echo 'test';
        global $woocommerce;
        global $product;
        $wc_ppu_unit = get_post_meta(get_the_id(), 'kw_UNITE', true); // 'kw_UNITE' is the name of your custom field for the unit.
        $wc_ppu_coefficient = get_post_meta(get_the_id(), 'kw_COEFF_UNITE', true); // 'kw_COEFF_UNITE' is the name of your custom field for the coefficient.
        $wc_ppu_regular_price = $product->get_regular_price();
        $wc_ppu_sale_price = $product->get_sale_price();
        // echo "$wc_ppu_unit<br/>";

        if (!empty($wc_ppu_coefficient) && is_product(true)) {
            if ($product->is_type('variable')) {
                $wc_ppu_variation_min_price = $product->get_variation_price();
                $wc_ppu_variation_regular_price = $product->get_variation_regular_price();
                if ($product->is_on_sale()) {
                    // echo 'variable soldé';
                    echo '<p class="product_meta"><del>' . number_format($wc_ppu_variation_regular_price / $wc_ppu_coefficient, 2, ",", " "). get_woocommerce_currency_symbol() . ' / ' . $wc_ppu_unit . '</del>
                <ins>' . number_format($wc_ppu_variation_min_price / $wc_ppu_coefficient, 2, ",", " "). get_woocommerce_currency_symbol() . ' / ' . $wc_ppu_unit . '</ins></p>' ;
                } else {
                    // echo 'variable pas soldé';
                    echo '<p class="product_meta"><ins>' . number_format($wc_ppu_variation_min_price / $wc_ppu_coefficient, 2, ",", " "). get_woocommerce_currency_symbol() . ' / ' . $wc_ppu_unit . '</ins></p>' ;
                }
            } elseif ($product->is_type('bundle')) {
                $wc_ppu_bundle_regular_price = get_post_meta(get_the_id(), '_regular_price', true);
                $wc_ppu_bundle_sale_price = get_post_meta(get_the_id(), '_sale_price', true);
                if ($product->is_on_sale()) {
                    // echo 'non-variable soldé';
                    echo '<p class="product_meta"><del>' . number_format($wc_ppu_bundle_regular_price / $wc_ppu_coefficient, 2, ",", " "). get_woocommerce_currency_symbol() . ' / ' . $wc_ppu_unit . '</del>
                <ins>' . number_format($wc_ppu_bundle_sale_price / $wc_ppu_coefficient, 2, ",", " "). get_woocommerce_currency_symbol() . ' / ' . $wc_ppu_unit . '</ins></p>' ;
                } else {
                    // echo 'non-variable pas soldé';
                    echo '<p class="product_meta"><ins>' . number_format($wc_ppu_bundle_regular_price / $wc_ppu_coefficient, 2, ",", " "). get_woocommerce_currency_symbol() . ' / ' . $wc_ppu_unit . '</ins></p>' ;
                }
            } else {
                if ($product->is_on_sale()) {
                    // echo 'non-variable soldé';
                    echo '<p class="product_meta"><del>' . number_format($wc_ppu_regular_price / $wc_ppu_coefficient, 2, ",", " "). get_woocommerce_currency_symbol() . ' / ' . $wc_ppu_unit . '</del>
                <ins>' . number_format($wc_ppu_sale_price / $wc_ppu_coefficient, 2, ",", " "). get_woocommerce_currency_symbol() . ' / ' . $wc_ppu_unit . '</ins></p>' ;
                } else {
                    // echo 'non-variable pas soldé';
                    echo '<p class="product_meta"><ins>' . number_format($wc_ppu_regular_price / $wc_ppu_coefficient, 2, ",", " "). get_woocommerce_currency_symbol() . ' / ' . $wc_ppu_unit . '</ins></p>' ;
                }
            }
        } else {
            return;
        };
    // }
}

add_filter( 'rwmb_meta_boxes', 'price_per_unit_meta_box' );

function price_per_unit_meta_box( $meta_boxes ) {

    $post = get_the_id();
    $wc_ppu_unit = get_post_meta($post, 'kw_UNITE', true); // 'kw_UNITE' is the name of your custom field for the unit.
    $wc_ppu_coefficient = get_post_meta($post, 'kw_COEFF_UNITE', true); // 'kw_COEFF_UNITE' is the name of your custom field for the unit.

    $meta_boxes[] = [
        'title'      => esc_html__( 'Prix par unité', 'online-generator' ),
        'id'         => 'untitled',
        'post_types' => ['product'],
        'context'    => 'normal',
        'fields'     => [
            [
                'type' => 'text',
                'name' => esc_html__( 'Coefficient (poids net)', 'online-generator' ),
                'desc'    => esc_html__( 'Doit être un chiffre avec un "." à la place de la virgule. Ex pour 100g : 0.100', 'online-generator' ),
                'placeholder' => esc_html__( '0.100', 'online-generator' ),
                'id'   => 'kw_COEFF_UNITE', // 'kw_COEFF_UNITE' is the name of your custom field for the unit.
            ],
            [
                'type' => 'text',
                'name' => esc_html__( 'Unité (kg, l, unité)', 'online-generator' ),
                'placeholder' => esc_html__( 'kg ou l ou unité', 'online-generator' ),
                'id'   => 'kw_UNITE', // 'kw_UNITE' is the name of your custom field for the unit.
            ],
            [
                'type' => 'heading',
                'name' => esc_html__( 'Que font ces données ?', 'online-generator' ),
                'desc'    => esc_html__( 'Elles permettent d\'afficher, le prix au kg / l / unité
                d\'un produit sous la forme suivante : [(prix min. du produit) / (coefficient)] € / [unité]  ', 'online-generator' ),
            ],
        ],
    ];

    return $meta_boxes;
}
