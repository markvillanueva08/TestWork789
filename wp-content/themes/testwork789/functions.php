<?php
/**
 * Recommended way to include parent theme styles.
 * (Please see http://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme)
 *
 */  

add_action( 'wp_enqueue_scripts', 'testwork789_style' );
				function testwork789_style() {
					wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
					wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style') );
				}

 /**
 * Your code goes below.
 */
 // Register Custom Post Type: Cities
function register_cities_post_type() {

    $labels = [
        'name'               => _x('Cities', 'post type general name', 'text_domain'),
        'singular_name'      => _x('City', 'post type singular name', 'text_domain'),
        'menu_name'          => _x('Cities', 'admin menu', 'text_domain'),
        'name_admin_bar'     => _x('City', 'add new on admin bar', 'text_domain'),
        'add_new'            => _x('Add New', 'city', 'text_domain'),
        'add_new_item'       => __('Add New City', 'text_domain'),
        'new_item'           => __('New City', 'text_domain'),
        'edit_item'          => __('Edit City', 'text_domain'),
        'view_item'          => __('View City', 'text_domain'),
        'all_items'          => __('All Cities', 'text_domain'),
        'search_items'       => __('Search Cities', 'text_domain'),
        'parent_item_colon'  => __('Parent Cities:', 'text_domain'),
        'not_found'          => __('No cities found.', 'text_domain'),
        'not_found_in_trash' => __('No cities found in Trash.', 'text_domain')
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => ['slug' => 'cities'],
        'supports'           => ['title', 'editor', 'thumbnail'],
        'menu_icon'          => 'dashicons-location-alt',
        'show_in_rest'       => true,
        'taxonomies'=>array('country'),
    ];

    register_post_type('cities', $args);
}

add_action('init', 'register_cities_post_type');

// Register Custom Taxonomy: Countries for Cities
function register_countries_taxonomy() {

    $labels = [
        'name'              => _x('Countries', 'taxonomy general name', 'text_domain'),
        'singular_name'     => _x('Country', 'taxonomy singular name', 'text_domain'),
        'search_items'      => __('Search Countries', 'text_domain'),
        'all_items'         => __('All Countries', 'text_domain'),
        'parent_item'       => __('Parent Country', 'text_domain'),
        'parent_item_colon' => __('Parent Country:', 'text_domain'),
        'edit_item'         => __('Edit Country', 'text_domain'),
        'update_item'       => __('Update Country', 'text_domain'),
        'add_new_item'      => __('Add New Country', 'text_domain'),
        'new_item_name'     => __('New Country Name', 'text_domain'),
        'menu_name'         => __('Countries', 'text_domain'),
    ];

    $args = [
        'hierarchical'      => true,  // Set to true to make it work like categories
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'country'],
        'show_in_rest'       => true,
    ];

    register_taxonomy('country', ['cities'], $args);
}

add_action('init', 'register_countries_taxonomy');

// Add Meta Box for Cities Custom Fields
function cities_location() {
    add_meta_box(
        'cities_location_fields',   // Unique ID
        'City Location',                     // Box title
        'display_cities_location_fields',    // Callback function
        'cities',                         // Post type 
        'normal',                            // Context
        'high'                               // Priority
    );
}
add_action('add_meta_boxes', 'cities_location');

//Display Meta Box Content with Multiple Fields
function display_cities_location_fields($post) {
    // Retrieve current meta values if they exist
    $longitude = get_post_meta($post->ID, '_longitude', true);
    $latitude = get_post_meta($post->ID, '_latitude', true);
    
    
    // Nonce for security
    wp_nonce_field(basename(__FILE__), 'display_cities_location_fields_nonce');
    ?>
    <p>
        <label for="longitude">Longitude:</label>
        <input type="text" id="longitude" name="longitude" value="<?php echo esc_attr($longitude); ?>" />
    </p>
    <p>
        <label for="latitude">Latitude:</label>
        <input type="text" id="latitude" name="latitude" value="<?php echo esc_attr($latitude); ?>" />
    </p>
    <?php
}

//Save Multiple Custom Fields Data
function save_cities_fields_data($post_id) {
    // Check nonce for security
    if (!isset($_POST['display_cities_location_fields_nonce']) || !wp_verify_nonce($_POST['display_cities_location_fields_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    // Prevent autosave interference
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
    if (!current_user_can('edit_post', $post_id)) return $post_id;

    // Save each field
    if (isset($_POST['longitude'])) {
        update_post_meta($post_id, '_longitude', sanitize_text_field($_POST['longitude']));
    }
    if (isset($_POST['latitude'])) {
        update_post_meta($post_id, '_latitude', sanitize_text_field($_POST['latitude']));
    }
}
add_action('save_post', 'save_cities_fields_data');
 
// Create the Widget Class
class City_Weather_Widget extends WP_Widget {

    // Constructor
    public function __construct() {
        $widget_ops = array(
			'classname'                   => 'cities-widget',
			'description'                 => __( 'Displays the weather for a city from the Cities CPT' ),
			'customize_selective_refresh' => true,
			'show_instance_in_rest'       => true,
		);
        parent::__construct(
            'city_weather_widget',                 // Base ID
            'City Weather Widget',                 // Widget Name
            $widget_ops
        );
    }

   
    
    // Frontend display of the widget
    public function widget($args, $instance) {
        echo $args['before_widget'];

        // Get city ID and API key from the widget instance
        $city_id = !empty($instance['city_id']) ? $instance['city_id'] : '';
        $api_key = !empty($instance['api_key']) ? $instance['api_key'] : '';

        if ($city_id && $api_key) {
            // Get city name
            $city = get_post($city_id);
            if ($city) {
                $city_name = $city->post_title;

                // Get weather data from OpenWeatherMap API
                $weather_data = $this->get_weather_data($city_name, $api_key);

                if ($weather_data) {
                    echo '<h3>' . esc_html($city_name) . '</h3>';
                    echo '<p>Temperature: ' . esc_html($weather_data['temp']) . '°C</p>';
                } else {
                    echo '<p>Weather data unavailable.</p>';
                }
            }
        } else {
            echo '<p>Please configure the widget settings.</p>';
        }

        echo $args['after_widget'];
    }

    // Backend widget form
    public function form($instance) {
        $city_id = !empty($instance['city_id']) ? $instance['city_id'] : '';
        $api_key = !empty($instance['api_key']) ? $instance['api_key'] : '';
        
        // Display API Key field
        ?>
        <p>
            <lab    el for="<?php echo $this->get_field_id('api_key'); ?>"><?php _e('OpenWeatherMap API Key:', 'text_domain'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('api_key'); ?>" name="<?php echo $this->get_field_name('api_key'); ?>" type="text" value="<?php echo esc_attr($api_key); ?>">
        </p>
        
        <?php
        // Query and display dropdown of cities from the CPT
        $cities = get_posts(['post_type' => 'cities', 'numberposts' => -1]);
        if ($cities) {
           $latitude = get_post_meta($city_id, "_latitude", true);
           $longitude = get_post_meta($city_id, "_longitude", true);

            ?>
            <p>
                <label for="<?php echo $this->get_field_id('city_id'); ?>"><?php _e('Select City:', 'text_domain'); ?></label>
                <select class="widefat" id="<?php echo $this->get_field_id('city_id'); ?>" name="<?php echo $this->get_field_name('city_id'); ?>">
                    <?php foreach ($cities as $city) { ?>
                        <option value="<?php echo $city->ID; ?>" <?php selected($city_id, $city->ID); ?>><?php echo $city->post_title; ?></option>
                    <?php } ?>
                </select>
                <?php echo $latitude; ?></br>
                <?php echo $longitude; ?>
            </p>
            <?php
        } else {
            echo '<p>No cities found in the Cities CPT.</p>';
        }
    }

    // Update widget settings
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['city_id'] = (!empty($new_instance['city_id'])) ? sanitize_text_field($new_instance['city_id']) : '';
        $instance['api_key'] = (!empty($new_instance['api_key'])) ? sanitize_text_field($new_instance['api_key']) : '';
        return $instance;
    }

    // Function to get weather data from OpenWeatherMap API
    private function get_weather_data($city_name, $api_key) {
        $url = "https://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city_name) . "&appid=" . $api_key . "&units=metric";
        
        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['main']['temp'])) {
            return [
                'temp' => $data['main']['temp']
            ];
        } else {
            return false;
        }
    }
}

//  Register the Widget
function register_city_weather_widget() {
    register_widget('City_Weather_Widget');
}
add_action('widgets_init', 'register_city_weather_widget');


// AJAX handler for loading cities
function load_cities_ajax_handler() {
    global $wpdb;

    // Get the search term
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

    // Query cities and countries using $wpdb
    $query = "SELECT cities.ID, cities.post_title AS city_name, country_terms.name AS country_name
              FROM {$wpdb->prefix}posts AS cities
              INNER JOIN {$wpdb->prefix}term_relationships AS rel ON (cities.ID = rel.object_id)
              INNER JOIN {$wpdb->prefix}term_taxonomy AS tax ON (rel.term_taxonomy_id = tax.term_taxonomy_id)
              INNER JOIN {$wpdb->prefix}terms AS country_terms ON (tax.term_id = country_terms.term_id)
              WHERE cities.post_type = 'cities'
              AND tax.taxonomy = 'country'
              AND cities.post_status = 'publish'";

    // Add search filter if a search term is provided
    if (!empty($search)) {
        $query .= $wpdb->prepare(" AND cities.post_title LIKE %s", '%' . $wpdb->esc_like($search) . '%');
    }

    $results = $wpdb->get_results($query);

    // Display the table
    if ($results) {
        echo '<table>';
        echo '<thead><tr><th>Country</th><th>City</th><th>Temperature (°C)</th></tr></thead>';
        echo '<tbody>';

        foreach ($results as $row) {
            // Call external API to fetch temperature for each city
            $temperature = get_city_temperature($row->city_name); // Custom function for temperature

            echo '<tr>';
            echo '<td>' . esc_html($row->country_name) . '</td>';
            echo '<td>' . esc_html($row->city_name) . '</td>';
            echo '<td>' . esc_html($temperature) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No cities found.</p>';
    }

    wp_die(); // Terminate AJAX response
}
add_action('wp_ajax_load_cities', 'load_cities_ajax_handler');
add_action('wp_ajax_nopriv_load_cities', 'load_cities_ajax_handler');


function get_city_temperature($city_name) {
    $api_key = '4bf4dbe39f29251074903758c32dcb77';
    $url = "https://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city_name) . "&appid=" . $api_key . "&units=metric";

    $response = wp_remote_get($url);
    if (is_wp_error($response)) {
        return 'N/A';
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (isset($data['main']['temp'])) {
        return $data['main']['temp'];
    } else {
        return 'N/A';
    }
}
