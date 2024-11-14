<?php
/* Template Name: Cities Table */

get_header(); ?>

<div class="cities-table-wrapper">
    <?php do_action('before_cities_table'); // Custom action hook before the table ?>

    <h2>City and Temperature List</h2>

    <!-- Search Field for Cities -->
    <input type="text" id="city-search" placeholder="Search for a city..." />
    <div id="cities-table-container">
        <!-- Table will be dynamically loaded here with AJAX -->
    </div>

    <?php do_action('after_cities_table'); // Custom action hook after the table ?>
</div>

<script type="text/javascript">
    // AJAX request for loading cities based on search input
    jQuery(document).ready(function($) {
        function loadCities(search = '') {
            $.ajax({
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                type: 'POST',
                data: {
                    action: 'load_cities',
                    search: search
                },
                success: function(response) {
                    $('#cities-table-container').html(response);
                }
            });
        }

        // Initial load of all cities
        loadCities();

        // Search functionality
        $('#city-search').on('keyup', function() {
            let search = $(this).val();
            loadCities(search);
        });
    });
</script>

<?php get_footer(); ?>
