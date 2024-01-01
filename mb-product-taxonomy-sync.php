<?php
/*
 * Plugin Name: MB Synchronize Product Taxonomy
 * Description: This plugin synchronizes all product mb-category and filter from a database
 * Version: 0.0.1
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * Author: CanSoft
 * Author URI: https://cansoft.com/
 */

 //require_once( plugin_dir_path( __FILE__ ) . '/inc/all-function/mb-icitem-product-sync.php');
 require_once( plugin_dir_path( __FILE__ ) . '/inc/api/fetch-all-products-data-from-j3_mjshop_table.php');
 require_once( plugin_dir_path( __FILE__ ) . '/inc/all-function/get-product-id-by-sku-meta-value.php');

 function mbpi_all_assets(){
    wp_enqueue_script('mbpc-main-script', plugin_dir_url( __FILE__ ) . '/assets/admin/js/script.js', null, time(), true);
}
add_action( 'admin_enqueue_scripts', 'mbpi_all_assets' );
 // Function to remove featured images from products

function mb_products_mb_category_sync() {

        if (isset($_GET['img-page'])) {
            $page = $_GET['img-page'] ?? 1;
            $start = microtime(true);
            // Get all the posts of type 'product' (customize post type as needed).
            $allproducts = fetch_all_products_sku_data_from_sku_table($page);
            //dd($allproducts);
            //$product_id = get_product_id_by_sku_meta_value_mj3($product["model"]);
            //$arraychyn = array_chunk($allproducts, 25);
            foreach ($allproducts as $product) {
                $product_id = get_product_id_by_sku_meta_value_mj3($product["model"]);
                $productCategories = $product["categories"];
                if ($product_id) {
                        // echo "product found";
                    if (count($productCategories)) {
                        
                        foreach ($productCategories as $pCate) {
                            $catName = $pCate["name"];
                            $catName1 = $pCate["parent"]["name"];
                            $catName2 = $pCate["parent"]["parent"]["name"];
                            $catName3 = $pCate["parent"]["parent"]["parent"]["name"];

                            $parentName1 = $pCate["parent"];
                            $parentName2 = $pCate["parent"]["parent"];
                            $parentName3 = $pCate["parent"]["parent"]["parent"];
                            

                            if ($parentName3) {
                                $parent_3_category_term = term_exists($catName3, 'mb-category');

                                if (!$parent_3_category_term) {
                                   $parent_3_category_id = wp_insert_term($catName3, 'mb-category')['term_id'];

                                   // Update custom taxonomy terms.
                                    wp_set_object_terms($product_id, $parent_3_category_id, 'mb-category', true);
                                }else{
                                    $parent_3_category_id = (int)$parent_3_category_term["term_id"];

                                    wp_set_object_terms($product_id, $parent_3_category_id, 'mb-category', true);
                                }

                                
                            }

                            if ($parentName2) {
                                $parent_2_category_term = term_exists($catName2, 'mb-category');

                                if (!$parent_2_category_term) {

                                    if ($parentName3) {
                                        $parent_2_category_id = wp_insert_term($catName2, 'mb-category', array('parent' => $parent_3_category_id))['term_id'];
                                    }else{

                                        $parent_2_category_id = wp_insert_term($catName2, 'mb-category')['term_id'];
                                    }
                                    // Update custom taxonomy terms.
                                    wp_set_object_terms($product_id, $parent_2_category_id, 'mb-category', true);
                                }else{
                                    $parent_2_category_id = (int)$parent_2_category_term["term_id"];
                                    // if ($parentName3) {
                                    //     $parent_2_category_id = wp_update_term($parent_2_category_id, 'mb-category', array('parent' => $parent_3_category_id));
                                    //     //wp_set_object_terms($product_id, $parent_2_category_id, 'mb-category', true);
                                    // }
                                    wp_set_object_terms($product_id, $parent_2_category_id, 'mb-category', true);
                                }
                                
                            }//parentName2 end

                            if ($parentName1) {
                                $parent_1_category_term = term_exists($catName1, 'mb-category');

                                if (!$parent_1_category_term) {

                                    if ($parentName2) {
                                        $parent_1_category_id = wp_insert_term($catName1, 'mb-category', array('parent' => $parent_2_category_id))['term_id'];
                                    }else{

                                        $parent_1_category_id = wp_insert_term($catName1, 'mb-category')['term_id'];
                                    }
                                        // Update custom taxonomy terms.
                                        wp_set_object_terms($product_id, $parent_1_category_id, 'mb-category', true);
                                }else{
                                    $parent_1_category_id = (int)$parent_1_category_term["term_id"];
                                    // if ($parentName2) {
                                    //     $parent_1_category_id = wp_update_term($parent_1_category_id, 'mb-category', array('parent' => $parent_2_category_id));
                                    // }
                                        
                                    wp_set_object_terms($product_id, $parent_1_category_id, 'mb-category', true);
                                }
                                
                            }//parentName1 end

                            if ($catName) {
                                $cat_term = term_exists($catName, 'mb-category');
                                if (!$cat_term) {
                                    if ($parentName1) {
                                        $category_id = wp_insert_term($catName, 'mb-category', array('parent' => $parent_1_category_id))['term_id'];
                                    }else{

                                        $category_id = wp_insert_term($catName, 'mb-category')['term_id'];
                                    }

                                    wp_set_object_terms($product_id, $category_id, 'mb-category', true);
                                }else{
                                    $category_id = (int)$cat_term["term_id"];
                                    // if ($parentName1) {
                                    //     $category_id = wp_update_term($category_id, 'mb-category', array('parent' => $parent_1_category_id));
                                    // }
                                    
                                    wp_set_object_terms($product_id, $category_id, 'mb-category', true);
                                }

                            }else{
                                echo "Category not found";
                            }

                        }
                    }
                }

                //dd($product_id);
            }


            $total = microtime(true) - $start;
            echo "Total Execution time: " . $total;

            if(! count($allproducts)){

                wp_redirect( admin_url( "/edit.php?post_type=product&page=product-image-sync" ) );
                exit();
            }
        }


    ?>
    <div class="wrap">
        <h1>This Page for Synchronize all product images</h1><br>
        <div class="d-flex">
            <form method="GET">
                    <input type="hidden" name="img-page" value="1">
                    <input type="hidden" name="post_type" value="product">
                    <input type="hidden" name="page" value="product-image-sync">
                <?php 
                    submit_button('Start Product Image MOVE Now', 'primary', 'mb-product-image-move');
                ?>
            </form>

            <form method="POST">
                <?php 
                    submit_button('Start Product Image Cron Now', 'primary', 'mb-product-image-sync-cron');
                ?>
            </form>
        </div>
    </div>
    <?php 
}

function mb_products_filter_sync() {

        if (isset($_GET['filter-page'])) {
            $page = $_GET['filter-page'] ?? 1;
            $start = microtime(true);
            // Get all the posts of type 'product' (customize post type as needed).
            $allproducts = fetch_all_products_sku_data_from_sku_table($page);
            //dd($allproducts);
            //$product_id = get_product_id_by_sku_meta_value_mj3($product["model"]);
            //$arraychyn = array_chunk($allproducts, 25);
            foreach ($allproducts as $product) {

                $product_id = get_product_id_by_sku_meta_value_mj3($product["model"]);
                $productFilter = $product["filters"];
                if ($product_id) {
                        // echo "product found";
                    if (count($productFilter)) {
                        foreach ($productFilter as $filter) {
                            $filterParentName = $filter["group"]["name"];
                            $filterName = $filter["name"];
                            //dd($filterName);
                            

                            if ($filterParentName) {
                                $parent_term = term_exists($filterParentName, 'filter');

                                if (!$parent_term) {
                                   $parent_id = wp_insert_term($filterParentName, 'filter')['term_id'];
                                   // Update custom taxonomy terms.
                                    wp_set_object_terms($product_id, $parent_id, 'filter', true);
                                }else{
                                    $parent_id = (int)$parent_term["term_id"];

                                    wp_set_object_terms($product_id, $parent_id, 'filter', true);
                                }
                            }

                            if ($filterName) {
                                $filter_term = term_exists($filterName, 'filter');
                                if (!$filter_term) {

                                    if ($filterParentName) {
                                        $filter_id = wp_insert_term($filterName, 'filter', array('parent' => $parent_id))['term_id'];
                                    }else{
                                        $filter_id = wp_insert_term($filterName, 'filter')['term_id'];
                                    }

                                    wp_set_object_terms($product_id, $filter_id, 'filter', true);
                                }else{
                                    $filter_id = (int)$filter_term["term_id"];
                                    
                                    wp_set_object_terms($product_id, $filter_id, 'filter', true);
                                }

                            }else{
                                echo "Filter not found";
                            }

                        }
                    }
                }

                //dd($product_id);
            }


            $total = microtime(true) - $start;
            echo "Total Execution time: " . $total;

            if(! count($allproducts)){

                wp_redirect( admin_url( "/edit.php?post_type=product&page=product-image-sync" ) );
                exit();
            }
        }


    ?>
    <div class="wrap">
        <h1>This Page for Synchronize all product images</h1><br>
        <div class="d-flex">
            <form method="GET">
                    <input type="hidden" name="filter-page" value="1">
                    <input type="hidden" name="post_type" value="product">
                    <input type="hidden" name="page" value="product-filter-sync">
                <?php 
                    submit_button('Start Product Filter Sync', 'primary', 'mb-product-filter-sync');
                ?>
            </form>
        </div>
    </div>
    <?php 
}

function mb_product_taxonomy_sync_menu_pages() {
    add_submenu_page(
        'mb_syncs',
        'Product Mb-Category Sync',
        'Product Mb-Category Sync',
        'manage_options',
        'product-mb-category-sync',
        'mb_products_mb_category_sync'
    );

    add_submenu_page(
        'mb_syncs',
        'Product Filter Sync',
        'Product Filter Sync',
        'manage_options',
        'product-filter-sync',
        'mb_products_filter_sync'
    );
}
add_action('admin_menu', 'mb_product_taxonomy_sync_menu_pages', 999);
