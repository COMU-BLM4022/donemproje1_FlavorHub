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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'proje_flavorhub' );

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
define( 'AUTH_KEY',         '@{KkzLwn8Cs~jG` wyS2}Y(Mh-hl71__0;5iIq^+<s=mEd79iKM``5Gfe5TfZgyl' );
define( 'SECURE_AUTH_KEY',  'YKq<c^,J-<BxQtW)D&@S7IcUT2c?aqc-x%!o-69f}Ka=CD3H{I@5dXQ7BcN}~QOe' );
define( 'LOGGED_IN_KEY',    '#O&JLLKiHJ?@o{gPc/tmZW$I#L|Hq9QLCK5>c9 ]QCIDgew#}[rAyIn!3WDcCuKz' );
define( 'NONCE_KEY',        'XB><YX>Wko|XXH[._Lz?pSu32x]{i4CE@fssAMJOL<ZK*?]:aAK@ajjfMzn07/nl' );
define( 'AUTH_SALT',        'l7F)05~jcR~BxN8Zkhr7V#`I>qAo[6qL)%B91.`{TY4,B,.~{!PnaHG?P6wuTZ,R' );
define( 'SECURE_AUTH_SALT', 'Z)OPg77}bHTWuvWxddk-[QJ~_FY-!W_`=AZ}s^i/:I`F;?A^2h$ix6b3}~=4OU*<' );
define( 'LOGGED_IN_SALT',   '=?YzJh=_((xe_k.b25Co,[]haJKrI,fVINQ8p[~4ZlRFd-8945/xxB!ehf]E~@4S' );
define( 'NONCE_SALT',       'So(n3Lqkfu3^M|sFLN+@s<m1qHh2l7xoTy:$,J40#J`6x|voy3NB%C.Qu<m+7;0G' );

/**#@-*/

/**
 * WordPress database table prefix.
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
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
