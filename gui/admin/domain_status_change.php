<?php
/**
 * i-MSCP a internet Multi Server Control Panel
 *
 * @copyright 	2001-2006 by moleSoftware GmbH
 * @copyright 	2006-2010 by ispCP | http://isp-control.net
 * @copyright 	2010 by i-MSCP | http://i-mscp.net
 * @version 	SVN: $Id$
 * @link 		http://i-mscp.net
 * @author 		ispCP Team
 * @author 		i-MSCP Team
 *
 * @license
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 *
 * The Original Code is "VHCS - Virtual Hosting Control System".
 *
 * The Initial Developer of the Original Code is moleSoftware GmbH.
 * Portions created by Initial Developer are Copyright (C) 2001-2006
 * by moleSoftware GmbH. All Rights Reserved.
 * Portions created by the ispCP Team are Copyright (C) 2006-2010 by
 * isp Control Panel. All Rights Reserved.
 * Portions created by the i-MSCP Team are Copyright (C) 2010 by
 * i-MSCP a internet Multi Server Control Panel. All Rights Reserved.
 */

require '../include/imscp-lib.php';

check_login(__FILE__);

$cfg = iMSCP_Registry::get('config');

if (!isset($_GET['domain_id'])) {
	user_goto('manage_users.php');
}

if (!is_numeric($_GET['domain_id'])) {
	user_goto('manage_users.php');
}

$domain_id = $_GET['domain_id'];

$query = "
	SELECT
		`domain_name`,
		`domain_status`
	FROM
		`domain`
	WHERE
		`domain_id` = ?
";

$rs = exec_query($sql, $query, $domain_id);

$location = 'admin';

if ($rs->fields['domain_status'] == $cfg->ITEM_OK_STATUS) {
	$action = "disable";
	change_domain_status($sql, $domain_id, $rs->fields['domain_name'], $action, $location);
} else if ($rs->fields['domain_status'] == $cfg->ITEM_DISABLED_STATUS) {
	$action = "enable";
	change_domain_status($sql, $domain_id, $rs->fields['domain_name'], $action, $location);
} else {
	user_goto('manage_users.php');
}