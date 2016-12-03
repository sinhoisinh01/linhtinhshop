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
define('DB_NAME', 'linhtinhshop');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'QS0FQ*h#SwFR_O#FL%JvsKumngwvW(1<BGtOAYyL5)Au.1e|;GK0$Y&HSuIAZ23S');
define('SECURE_AUTH_KEY',  'T&Bw<1Jy%|DI60;jKDW|{z|owBFrS>#a&H|{Ro!7ji;3:F-Z2KZ;NX*.)4+) Qk!');
define('LOGGED_IN_KEY',    '=<.5VBDid+g>D8zkm+VqFtbZ*.fVvt Bbw%Me^fT*#bQ-gQ^x%/+gM9)ClbvQt?H');
define('NONCE_KEY',        'A*~4K3U~<9_dfdGnwQGh)Ls/=85a/5rgi,sk&aDka!e%yo3In4s;g%+vhxu: b+C');
define('AUTH_SALT',        'F>rGiQl2aXs71:H6`4BPH=T3TN_.cPn!(Fu/Y v`+Eu_2dw&z:y>*H?X<z~[.HLa');
define('SECURE_AUTH_SALT', 'Zf=%.RPyQdeK}!?F |&s8j*>) eCF4)ikDxGc5uALZ{tjDX0yrcwM[^#M[<n#LND');
define('LOGGED_IN_SALT',   '6dZLZD Twf7}4hBB^hyTM5iQaBS^=&.:%%lrQO)i#atJaJVm{a}`~#z+4y*(t!2>');
define('NONCE_SALT',       'X/pVGqS9u3IRDILZn^^)j4hVxrAFp+Zq$ /;bjMWv5qqc#626sDypSzGl:(T_nmd');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
