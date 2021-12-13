<?php
/**
 * Class Item
 *
*/

if ( ! defined( 'ABSPATH' ) ) {
    die( '-1' );
}

class Item
{
    public static function register()
    {
        $instance = new self;
        add_action('init', [$instance, 'registerPostType']);
        add_action('rest_api_init', [$instance, 'registerAPIRoutes']);
    }

    public function registerPostType()
    {
        $labels = [
            'name' => _x( 'Items', 'post type general name' ),
            'singular_name' => _x( 'Item', 'post type singular name' ),
            'add_new' => _x( 'Add New', 'Items' ),
            'add_new_item' => __( 'Add New Item' ),
            'edit_item' => __( 'Edit Item' ),
            'new_item' => __( 'New Item' ),
            'all_items' => __( 'All Items' ),
            'view_item' => __( 'View Item' ),
            'search_items' => __( 'Search items' ),
            'not_found' => __( 'No item found' ),
            'not_found_in_trash' => __( 'No item found in the trash' ),
            'parent_item_colon' => '',
            'menu_name' => 'Items',
            'featured_image'        => __( 'Item image' ),
            'set_featured_image'    => __( 'Set item image' ),
            'remove_featured_image' => __( 'Remove item image' ),
            'use_featured_image'    => __( 'Use as item image' ),
        ];

        $args = [
            'labels' => $labels,
            'description' => 'List of all items',
            'public' => true,
            'menu_position' => 3,
            'supports' => array( 'title', 'editor' ),
            'has_archive' => true,
            'publicly_queryable'  => false,
            'menu_icon' => 'dashicons-shield',
            'map_meta_cap' => true,
        ];
        
        register_post_type( 'items', $args );
    }

    public function registerAPIRoutes()
    {
        register_rest_route( 'items', '/get-all', array(
            'methods' => 'GET',
            'callback' => array($this,'ga_get_all_items'),
            'permission_callback' => function () {
                return true;
            },
        ));

        register_rest_route( 'items', '/get', array(
            'methods' => 'GET',
            'callback' => array($this,'ga_get_item'),
            'permission_callback' => function () {
                return true;
            },
        ));
        
        register_rest_route( 'items', '/create', array(
            'methods' => 'POST',
            'callback' => array($this,'ga_create_item'),
            'permission_callback' => function () {
                return true;
            },
        ));

        register_rest_route( 'items', '/edit', array(
            'methods' => 'POST',
            'callback' => array($this,'ga_edit_item'),
            'permission_callback' => function () {
                return true;
            },
        ));

        register_rest_route( 'items', '/delete', array(
            'methods' => 'GET',
            'callback' => array($this,'ga_delete_item'),
            'permission_callback' => function () {
                return true;
            },
        ));
    }

    public function ga_create_item( WP_REST_Request $request )
    {
        global $wpdb;

        if (empty($request->get_body())) {
           
            $response = new WP_REST_Response(['message' => 'Unauthorized']);
            $response->set_status(401);
            return $response;
        }

        $body = json_decode($request->get_body(), true);

        if (empty($body) || !isset($body['username']) || !isset($body['password']) || empty($body['username']) || empty($body['password'])) {
            
            $response = new WP_REST_Response(['message' => 'Unauthorized']);
            $response->set_status(401);
            return $response;
        }

        $username = $body['username'];
        $password = $body['password'];
        $title = $request->get_param('title');
        $description = $request->get_param('description');

        if (empty($title) || empty($username) || empty($password)) {
          
            $response = new WP_REST_Response(['message' => 'Required parameters not found']);
            $response->set_status(404);
            return $response;
        }

        $user = Helper::authentication($username, $password);

        if (empty($user)) {
           
            $response = new WP_REST_Response(['message' => 'Unauthorized']);
            $response->set_status(401);
            return $response;
        }

        if (empty($description)) {
            
            $description = ' ';
        }

        $item_id = wp_insert_post(
            array(
                'comment_status' => 'close',
                'ping_status'    => 'close',
                'post_author'    => $user->ID,
                'post_title'     => $title,
                'post_name'      => $title,
                'post_status'    => 'publish',
                'post_content'   => $description,
                'post_type'      => 'items'
            )
        );

        $response = new WP_REST_Response(['message' => 'Item created succesfully', 'item' => get_post( $item_id )]);
        $response->set_status(200);
        return $response;
    }

    public function ga_get_item( WP_REST_Request $request )
    {
        global $wpdb;

        if (empty($request->get_body())) {
             
            $response = new WP_REST_Response(['message' => 'Unauthorized']);
            $response->set_status(401);
            return $response;
        }

        $body = json_decode($request->get_body(), true);

        if (empty($body) || !isset($body['username']) || !isset($body['password']) || empty($body['username']) || empty($body['password'])) {
             
            $response = new WP_REST_Response(['message' => 'Unauthorized']);
            $response->set_status(401);
            return $response;
        }

        $username = $body['username'];
        $password = $body['password'];
        $item_id = $request->get_param('item_id');

        if (empty($item_id) || empty($username) || empty($password)) {
             
            $response = new WP_REST_Response(['message' => 'Required parameters not found']);
            $response->set_status(404);
            return $response;
        }

        $user = Helper::authentication($username, $password);

        if (empty($user)) {
             
            $response = new WP_REST_Response(['message' => 'Unauthorized']);
            $response->set_status(401);
            return $response;
        }

        $item = get_post( $item_id );

        if (empty($item)) {
             
            $response = new WP_REST_Response(['message' => 'Item not found']);
            $response->set_status(404);
            return $response;
        }

        $response = new WP_REST_Response(['item' => $item]);
        $response->set_status(200);
        return $response;
    }

    public function ga_delete_item( WP_REST_Request $request )
    {
        global $wpdb;

        if (empty($request->get_body())) {
             
            $response = new WP_REST_Response(['message' => 'Unauthorized']);
            $response->set_status(401);
            return $response;
        }

        $body = json_decode($request->get_body(), true);

        if (empty($body) || !isset($body['username']) || !isset($body['password']) || empty($body['username']) || empty($body['password'])) {
             
            $response = new WP_REST_Response(['message' => 'Unauthorized']);
            $response->set_status(401);
            return $response;
        }

        $username = $body['username'];
        $password = $body['password'];
        $item_id = $request->get_param('item_id');

        if (empty($item_id) || empty($username) || empty($password)) {
             
            $response = new WP_REST_Response(['message' => 'Required parameters not found']);
            $response->set_status(404);
            return $response;
        }

        $user = Helper::authentication($username, $password);

        if (empty($user)) {
             
            $response = new WP_REST_Response(['message' => 'Unauthorized']);
            $response->set_status(401);
            return $response;
        }

        $item = get_post( $item_id );

        if (empty($item)) {
             
            $response = new WP_REST_Response(['message' => 'Item not found']);
            $response->set_status(404);
            return $response;
        }

        wp_delete_post( $item_id, true );

        $response = new WP_REST_Response(['message' => 'Item deleted successfully']);
        $response->set_status(200);
        return $response;
    }

    public function ga_edit_item( WP_REST_Request $request )
    {
        global $wpdb;

        if (empty($request->get_body())) {
             
            $response = new WP_REST_Response(['message' => 'Unauthorized']);
            $response->set_status(401);
            return $response;
        }

        $body = json_decode($request->get_body(), true);

        if (empty($body) || !isset($body['username']) || !isset($body['password']) || empty($body['username']) || empty($body['password'])) {
             
            $response = new WP_REST_Response(['message' => 'Unauthorized']);
            $response->set_status(401);
            return $response;
        }

        $username = $body['username'];
        $password = $body['password'];
        $item_id = $request->get_param('item_id');
        $title = $request->get_param('title');
        $description = $request->get_param('description');

        if (empty($item_id) || empty($title) || empty($username) || empty($password)) {
             
            $response = new WP_REST_Response(['message' => 'Required parameters not found']);
            $response->set_status(404);
            return $response;
        }

        $user = Helper::authentication($username, $password);

        if (empty($user)) {
             
            $response = new WP_REST_Response(['message' => 'Unauthorized']);
            $response->set_status(401);
            return $response;
        }

        $item = get_post( $item_id );

        if (empty($item)) {
             
            $response = new WP_REST_Response(['message' => 'Item not found']);
            $response->set_status(404);
            return $response;
        }

        if (empty($description)) {
             
            $description = ' ';
        }

        $item_id = wp_update_post(
            array(
                'ID' => $item_id,
                'comment_status' => 'close',
                'ping_status'    => 'close',
                'post_author'    => 1,
                'post_title'     => $title,
                'post_name'      => $title,
                'post_status'    => 'publish',
                'post_content'   => $description,
                'post_type'      => 'items'
            )
        );

        $response = new WP_REST_Response(['message' => 'Item updated succesfully', 'item' => get_post( $item_id )]);
        $response->set_status(200);
        return $response;
    }

    public function ga_get_all_items( WP_REST_Request $request )
    {
        global $wpdb;

        if (empty($request->get_body())) {
             
            $response = new WP_REST_Response(['message' => 'Unauthorized']);
            $response->set_status(401);
            return $response;
        }

        $body = json_decode($request->get_body(), true);

        if (empty($body) || !isset($body['username']) || !isset($body['password']) || empty($body['username']) || empty($body['password'])) {
             
            $response = new WP_REST_Response(['message' => 'Unauthorized']);
            $response->set_status(401);
            return $response;
        }

        $username = $body['username'];
        $password = $body['password'];

        if (empty($username) || empty($password)) {
             
            $response = new WP_REST_Response(['message' => 'Required parameters not found']);
            $response->set_status(404);
            return $response;
        }

        $user = Helper::authentication($username, $password);

        if (empty($user)) {
             
            $response = new WP_REST_Response(['message' => 'Unauthorized']);
            $response->set_status(401);
            return $response;
        }

        $items = array();
        
        $paged = $request->get_param( 'page' );
        $paged = ( isset( $paged ) || ! ( empty( $paged ) ) ) ? $paged : 1;

        $posts = get_posts( array(
                'paged' => $paged,
                'posts_per_page' => 10,
                'post_type' => array( 'items' ),
                // 'author' => $user->ID,
            )
        ); 

        foreach( $posts as $post )
        {
            $items[] = get_post( $post->ID );
        }

        $response = new WP_REST_Response(['items' => $items, 'page' => $paged]);
        $response->set_status(200);
        return $response;
    }
}