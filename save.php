<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Version information
 *
 * @package   mod_typorepo
 * @copyright 2020 bdecent gmbh <https://bdecent.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('lib.php');

require_login();

$language  = urldecode($_GET["L"]);
$url       = get_config('typorepo', 'pageurl').urldecode($_GET["pageid"]).'&L='.$language;
$linksaved = get_string('linksaved', 'typorepo');

echo <<<EOT
<html>
    <head>
        <script language="javascript">
            var mform1 = parent.document.getElementsByClassName('mform')[0];
            if( typeof mform1.url != 'undefined' )           { mform1.url.value = '$url'; }
            if( typeof mform1.config_url != 'undefined' )    { mform1.config_url.value = '$url'; }
        </script>
    </head>
    <body>
        <p style="font-family: arial, verdana, serif;">$linksaved</p>
    </body>
</html>
EOT;