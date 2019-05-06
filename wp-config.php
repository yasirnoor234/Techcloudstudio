<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'businesspoint' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'rj_8Rm)9CvJ: =j`-[$uu[ro$W+SCSB9kVS5073,]2DQvI(ZX)x-Sm-#wx`b%S !' );
define( 'SECURE_AUTH_KEY',  'b>u1+W8(a,M7O+H>7SCwIaS$5Uqf^Ee,7v2 D]4Gm9%1x9M}FF-}7ou/$MJsc8V.' );
define( 'LOGGED_IN_KEY',    '6%D~Cb?]b`ioweR+x/v&ulhrGd^&G@NP?BrdyafzZ?10[FZI:xu]KZ s|L=fO/D$' );
define( 'NONCE_KEY',        'WDbXi3Vc}<<O|1<TD6oG=sO+HMcypEw^G[CF@m%C|!6OJ~*]>K8F3Bhp/p}0sz{.' );
define( 'AUTH_SALT',        '&Odxrfp9GWP-ZkBTmry=[>v|c.T-7mF9yrgVc 8/d!#oVF]kN5p(YUi/RIU&jj<6' );
define( 'SECURE_AUTH_SALT', '+XVLk|>(yXc9OXa/=aSe&`!rvRT@zrT,I&x;bWh05xH~TJ1*OJ`fW4r wHqY^H;9' );
define( 'LOGGED_IN_SALT',   '8WDEDrV75R]6K:[jV48]LDrJ<UWax017^hALBmi&S4/TB}I?Xo4.`B K=*-H6;9t' );
define( 'NONCE_SALT',       'U]c`tRdnKp]$q^#2W(^+<eiWud}*fBotZtJ#{^0vJd;.x?|;|V}`Msx?pQHyhD{2' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
