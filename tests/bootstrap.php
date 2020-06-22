<?php
declare(strict_types=1);

/**
 * This file is part of api-maker.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/api-maker
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */

require_once 'vendor/autoload.php';

define('TESTS', __DIR__);
define('TEST_APP', TESTS . DS . 'test_app' . DS);
define('EXPECTED_FILES', TESTS . DS . 'expectedFiles' . DS);
define('TMP', sys_get_temp_dir() . DS . 'api-maker' . DS);

@mkdir(TMP, 0777, true);
