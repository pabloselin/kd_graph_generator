<?php
/*
Plugin Name: Kit Digital: Graph generator
Plugin URI: http://www.pluriversidadnomade.net
Description: Plugin que implementa un grafo a partir de las etiquetas del sitio
Version: 0.2.2
Author: Pablo Selín Carrasco Armijo
Author URI: http://pablosel.in
License: GPL2
*/

define('KD_PLUGIN_VERSION', '0.0.1');

function kd_scripts()
{
    wp_enqueue_script('kd_graph', plugins_url('kd_graph_generator/public/bundle.js', __FILE__), array(), KD_PLUGIN_VERSION, true);
    wp_enqueue_style('kd_graph', plugins_url('kd_graph_generator/public/bundle.css', __FILE__), array(), KD_PLUGIN_VERSION);
}

add_action('wp_enqueue_scripts', 'kd_scripts');

function kd_content_endpoint()
{
    // get all tags
    $graph_data = array();
    $used_ids = array();
    $used_links = array();
    //get all content
    $items = get_posts(
        array(
            'posts_per_page' => -1,
            'post_type' => 'any'
        )
    );

    foreach ($items as $item) {
        $item_id = $item->ID;
        $item_tags = wp_get_post_tags($item_id);


        foreach ($item_tags as $item_tag) {
            if (!in_array($item_tag->term_id, $used_ids)) {

                $used_ids[] = $item_tag->term_id;

                $graph_data['items'][] = array(
                    'id' => (string)$item_tag->term_id,
                    'name' => $item_tag->name,
                    'link' => get_term_link($item_tag->term_id, 'post_tag'),
                    'value' => 20,
                    'symbolSize' => 20,
                );
            }



            //recursive linking
            foreach ($item_tags as $subitem_tags) {
                if ($item_tag->term_id != $subitem_tags->term_id) {
                    //check if reversed link exists
                    if (!in_array([$subitem_tags->term_id, $item_tag->term_id], $used_links)) {
                        $graph_data['links'][] = array(
                            'source' => (string)$item_tag->term_id,
                            'target' => (string)$subitem_tags->term_id,
                            'value'  => 1
                        );

                        $used_links[] = [$item_tag->term_id, $subitem_tags->term_id];
                    }
                }
            }

            array_unique($graph_data['links']);
        }
    }


    return $graph_data;
}


//make custom wordpress rest api endpoint
add_action('rest_api_init', function () {
    register_rest_route('kd_graph/v1', 'tagdata', array(
        'methods'   => 'GET',
        'callback'  => 'kd_content_endpoint'
    ));
});
