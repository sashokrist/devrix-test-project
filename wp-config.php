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
define( 'DB_NAME', 'test_wp_project' );

/** Database username */
define( 'DB_USER', 'admin' );

/** Database password */
define( 'DB_PASSWORD', 'Strong_Admin_Passw0rd!');

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
define( 'AUTH_KEY',         '39F4xj0~-us7K6cBphYAzS&IR.0fQXo*o6s#S/2opBGY-{VM[1BP`$NgUNkja@:$' );
define( 'SECURE_AUTH_KEY',  '@dt 8t<sz.!Qb{UlvBl1I4eWB6WI$KRHw(PmKj~Dk.X&)G.Tz 5C,#:3D/.<!>sT' );
define( 'LOGGED_IN_KEY',    'U?*b*h;RNwt}Lf)@`NqDf35kY%wMBz ~SkA@+{gnC>H1oo?q94yxk^K95$T;w:Gc' );
define( 'NONCE_KEY',        'dEoucU=UFlS/=`{t/p ?;^`>9AheBLy0vyN]/ZfCuSl,)9|o_Kh^,t&z,^P>KWa,' );
define( 'AUTH_SALT',        '[2*5-y{()O,[p`vA1zxjJ&a &,vwMXGuyO:QBmvA:,>Zk4 +c>w+R_K(K_ma[h;A' );
define( 'SECURE_AUTH_SALT', '%5s1|]Db_)nPL)z9J%,jFCW$oiYJ:EN[1OxAL&MpIeLqd4|Yp3YpbQmezf%eYg=Q' );
define( 'LOGGED_IN_SALT',   'QyGVEHq@Br-6:!1JwJfqC;S[Q$l|GG9$z!Z%4#=Iy:v}!ples|WL}P=j9q,K}wc2' );
define( 'NONCE_SALT',       '#F{w7p]#gTT&t]TOui0/bAL(1hs!x;%5oI.tthT%:?Qgq*n&D$_>%?REPF9*MBYN' );

/* Write plugins/themes directly, no FTP */
define('FS_METHOD', 'direct');

/* Put WP’s temp files in a writable place */
define('WP_TEMP_DIR', __DIR__ . '/wp-content/tmp');

/* Optional: explicit chmod defaults */
define('FS_CHMOD_DIR',  0775);
define('FS_CHMOD_FILE', 0664);

if ( ! defined( 'WP_USE_THEMES' ) ) {
	define( 'WP_USE_THEMES', true );
}
if ( ! defined( 'WP_BLOCK_THEME' ) ) {
	define( 'WP_BLOCK_THEME', true );
}

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
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'SCRIPT_DEBUG', true );
define( 'SAVEQUERIES', true );

/* Add any custom values between this line and the "stop editing" line. */

// Custom debug settings for development
@ini_set( 'log_errors', 1 );
@ini_set( 'error_log', __DIR__ . '/wp-content/debug.log' );

// Fix connectivity issues
define( 'WP_AUTO_UPDATE_CORE', false );
define( 'AUTOMATIC_UPDATER_DISABLED', true );

// Disable WordPress.org API checks that might cause "offline" errors
define( 'WP_CACHE', false );
define( 'DISABLE_WP_CRON', false );

// Force WordPress to use local URLs and prevent external API calls
define( 'WP_SITEURL', 'http://localhost/devrix-test-project' );
define( 'WP_HOME', 'http://localhost/devrix-test-project' );

// Disable external API calls that might cause "offline" errors
define( 'WP_HTTP_BLOCK_EXTERNAL', true );
define( 'WP_HTTP_BLOCK_EXTERNAL_HOSTS', 'api.wordpress.org' );



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
