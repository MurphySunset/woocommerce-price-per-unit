<?php
/**
 * Plugin Name: Woocommerce Price per Unit
 * Description: Display the price per unit of a product, under the price. 
 * Works with variable products and on sale products.
 * Requires WooCommerce and Meta Box extensions. Please adapt source code to your needs.
 * Author: Baptiste Mourey
 * Version: 0.2
 * Version 0.2: moved from div to p.
 * Version 0.1: first commit.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_filter('woocommerce_get_price_html', 'price_per_unit');

function price_per_unit($price) {

    global $woocommerce;
    global $product;

    $post = get_the_id();
    $unit = get_post_meta($post, 'kw_UNITE', true); // 'kw_UNITE' is the name of your custom field for the unit.
    $coefficient = get_post_meta($post, 'kw_COEFF_UNITE', true); // 'kw_COEFF_UNITE' is the name of your custom field for the coefficient.
    $currency_symbol = get_woocommerce_currency_symbol();
    $regular_price = $product->get_regular_price();
    $sale_price = $product->get_sale_price();

    if (current_user_can('manage_options')) {
        if (!empty($coefficient) && is_product(true) ) {
            
            if ($product->is_type('variable')) {

                $variation_min_price = $product->get_variation_price();
                $variation_regular_price = $product->get_variation_regular_price();

                if ($product->is_on_sale()) {
                    return $price . '<p class="price-per-unit"><del>' . number_format($variation_regular_price / $coefficient, 2, ",", " "). $currency_symbol . ' / ' . $unit . '</del>
                    <ins>' . number_format($variation_min_price / $coefficient, 2, ",", " "). $currency_symbol . ' / ' . $unit . '</ins></p>' ;
                }
                else {
                    return $price . '<p class="price-per-unit"><ins>' . number_format($variation_min_price / $coefficient, 2, ",", " "). $currency_symbol . ' / ' . $unit . '</ins></p>' ;
                }
            } else {
                if ($product->is_on_sale()) {
                    return $price . '<p class="price-per-unit"><del>' . number_format($regular_price / $coefficient, 2, ",", " "). $currency_symbol . ' / ' . $unit . '</del>
                    <ins>' . number_format($sale_price / $coefficient, 2, ",", " "). $currency_symbol . ' / ' . $unit . '</ins></p>' ;
                }
                else {
                    return $price . '<p class="price-per-unit"><ins>' . number_format($regular_price / $coefficient, 2, ",", " "). $currency_symbol . ' / ' . $unit . '</ins></p>' ;
                }
            }
            
            
        } else {
            return $price ;
        }
    } else {
        return $price ;
    }
}


add_filter( 'rwmb_meta_boxes', 'price_per_unit_meta_box' );

function price_per_unit_meta_box( $meta_boxes ) {

    $post = get_the_id();
    $unit = get_post_meta($post, 'kw_UNITE', true); // 'kw_UNITE' is the name of your custom field for the unit.
    $coefficient = get_post_meta($post, 'kw_COEFF_UNITE', true); // 'kw_COEFF_UNITE' is the name of your custom field for the unit.

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