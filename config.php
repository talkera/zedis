<?php
define('ROOT_PATH', __DIR__);
define('RUNTIME_PATH', ROOT_PATH.'/protected/runtime');
define('WORK_PATH', RUNTIME_PATH.'/work');

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'zedis');
define('DB_PORT', 3306);
define('DB_USER', 'zedis');
define('DB_PWD', 'zedis');

define('SVN_BIN', '/usr/local/subversion/bin/svn');
define('GIT_BIN', 'git');
define('RSYNC_BIN', '/usr/bin/rsync');