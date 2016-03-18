<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */
// KSK - Start session
if (!session_id()) {     session_start(); }

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 't9z');

/** Имя пользователя MySQL */
define('DB_USER', 'root');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', '');

/** Имя сервера MySQL */
define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8mb4');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'CMRS%SOqeAgMPOtmiOP5++gcYQ)H|fPW1~KvPqw[f2f^(~2,k&xz&3`kosb-RgLV');
define('SECURE_AUTH_KEY',  ')G-[H>Soi1-TN]xAiE9|&)=m:<C$ =l`-{_*NqK6S[mV+I Ny)7mVd&?A6_t9y:]');
define('LOGGED_IN_KEY',    '-&O?>RjJ|?KQ,!9a(4i#LODy3S?jxm2k1lL^h85m}wC,2Uv%i;+vt64fdbqH]mC6');
define('NONCE_KEY',        'T8h1l,+7?5!mf;W!`;(*`I|#|F(,|9lWv*b35 >;X+.no%b>+Xsj(t&FJu3tp~7x');
define('AUTH_SALT',        'm)OL^fig+O@5q7M+s6i,koZv[~YpnP,K8PZQPFDF{V5<D%ZsmWK5R]wX-f>bK:Y^');
define('SECURE_AUTH_SALT', '.r=?5O-@hYw[!n;{uxEDgnRQ~42A 1^b;}c|@Nv- 3jP<puth|VDV,-#5H0nb_<(');
define('LOGGED_IN_SALT',   '>w5PtsMsQhziVP@Hq:@Ikl%ujo[*gH;s?$ p4W+F@J;WX:oATJ/uIrp17f=v[b]e');
define('NONCE_SALT',       'fN;-t3;BWQ%P` g.Zb+PM,yG;[~AlZ!4Jlq(!};fV!2+bu6k&(+!pJ,d&u:Z%:Ob');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 * 
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
