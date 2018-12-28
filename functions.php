add_action( 'rest_api_init', 'dt_register_api_hooks' );
function dt_register_api_hooks() {
  $namespace = 'get-evening-posts/v1';
  register_rest_route( $namespace, '/list-evening-posts/', array(
    'methods'  => 'POST',
    'callback' => 'get_evening_posts',
  ) );
}

function get_evening_posts() {
  if ( 0 || false === ( $return = get_transient( 'dt_all_posts' ) ) ) {
    $query     = apply_filters( 'get_posts_query', array(
      'numberposts' => -1,
      'post_type'   => 'post',
      'post_status' => 'publish',
      'category_name' => 'courses-itea',
    ) );
    $all_posts = get_posts( $query );
    $return    = array();
    foreach ( $all_posts as $post ) {
      $return[] = array(
        'ID'        => $post->ID,
        'title'     => $post->post_title,
        'uuid'  => get_post_meta($post->ID, 'uuid_for_itea_crm', true),
        'cc_uuid'  => get_post_meta($post->ID, 'date1-uuid', true),
        'post_modified' => $post->post_modified,
        'post_modified_gmt' => $post->post_modified_gmt,
      );
    }
    // cache for 10 minutes
    set_transient( 'all_posts', $return, apply_filters( 'posts_ttl', 60 * 10 ) );
  }
  $response = new WP_REST_Response( $return );
  $response->header( 'Access-Control-Allow-Origin', apply_filters( 'access_control_allow_origin', '*' ) );
  return $response;
}
