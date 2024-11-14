<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'testwork789' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'Now{EVG1_)xJA=w*%XuMWosF}qjaov}P4Eezf{ Rp&ZBS@qgv]wMO=mrO3Rx#O]3' );
define( 'SECURE_AUTH_KEY',  'q6]D(Fx4t;kL6&cdbN.LmYGbKvWa^{8 i!mMkrFsCw7;,^ YoQ&j]oeu_n;Ds;ek' );
define( 'LOGGED_IN_KEY',    'V7!n~cXU<d4k[JyBE[Y5R.%BB(P 7oV:/wB,~TF%pG.YwHJg7VkY2R-3)B0=#jZ8' );
define( 'NONCE_KEY',        'U+!fR:!WgZUl7Wg g4~z&x+}<;CbiR.Yq[PP`8/?e>R/B$,7PSYM7Z <L~|A*Tu8' );
define( 'AUTH_SALT',        'h~biR]hz]tRZz=0KjB!$Pu,# -k_SCV1-X+&6xt J*iSNEr/vVNF6*NDtBD2#Xa;' );
define( 'SECURE_AUTH_SALT', 'UF5,1%#{Qo=y{ M8,O5`l>?j-9rImI]%`@pY-k-pV[(?>yyVh ,v]P#=tVKn6iry' );
define( 'LOGGED_IN_SALT',   'm=JEO^%OjJF9/ng`7d.%}9:!v,jN!,_SDaS5ubIONMXA{M6bd5&eD/h9v>=1l.::' );
define( 'NONCE_SALT',       'Qzr>#x0Vezy!k1<p#tqU`7*jVTU},!/3P(ds}Zic{->5^sZqU%<as^zD)0Zp):GJ' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );
define('WP_DEBUG_LOG', true);
  define('WP_DEBUG_DISPLAY', true);
/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
//if ( ! defined( 'ABSPATH' ) ) {
//	define( 'ABSPATH', __DIR__ . '/' );
//}

/** Absolute path to the WordPress directory. */

if ( !defined('ABSPATH') )

define('ABSPATH', dirname(__FILE__) . '/');

define('CONCATENATE_SCRIPTS', false);

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

