<?php
/**
 * i-MSCP a internet Multi Server Control Panel
 *
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
 * The Original Code is "ispCP - ISP Control Panel".
 *
 * The Initial Developer of the Original Code is ispCP Team.
 * Portions created by Initial Developer are Copyright (C) 2006-2010 by
 * isp Control Panel. All Rights Reserved.
 * Portions created by the i-MSCP Team are Copyright (C) 2010 by
 * i-MSCP a internet Multi Server Control Panel. All Rights Reserved.
 *
 * @category i-MSCP
 * @copyright 2006-2010 by ispCP | http://isp-control.net
 * @copyright 2006-2010 by ispCP | http://i-mscp.net
 * @author ispCP Team
 * @author i-MSCP Team
 * @version SVN: $Id: Database.php 3702 2010-11-16 14:20:55Z thecry $
 * @link http://i-mscp.net i-MSCP Home Site
 * @license http://www.mozilla.org/MPL/ MPL 1.1
 */

require '../include/imscp-lib.php';

check_login(__FILE__);

/**
 * @var $cfg iMSCP_Config_Handler_File
 */
$cfg = iMSCP_Registry::get('Config');

if (isset($_GET['id']) AND is_numeric($_GET['id'])) {
	$query="
		SELECT
			`software_id`,
			`reseller_id`,
			`software_archive`,
			`software_depot`
		FROM
			`web_software`
		WHERE
			`software_id` = ?
	";
	$rs = exec_query($sql, $query, $_GET['id']);
	if($rs->fields['software_depot'] == "yes") {
		$filename = $cfg->GUI_SOFTWARE_DEPOT_DIR."/".$rs->fields['software_archive']."-".$rs->fields['software_id'].".tar.gz";
	}else{
		$filename = $cfg->GUI_SOFTWARE_DIR."/".$rs->fields['reseller_id']."/".$rs->fields['software_archive']."-".$rs->fields['software_id'].".tar.gz";
	}
	if (file_exists($filename)) {
		header("Cache-Control: public, must-revalidate");
		header("Pragma: hack");
		header("Content-Type: application/octet-stream");
		header("Content-Length: " .(string)(filesize($filename)) );
		header('Content-Disposition: attachment; filename="'.$rs->fields['software_archive'].'.tar.gz"');
		header("Content-Transfer-Encoding: binary\n");

		$fp = fopen($filename, 'rb');
		$buffer = fread($fp, filesize($filename));
		fclose ($fp);
		print $buffer;
	} else {
		set_page_message(tr('File does not exist. %1$s.tar.gz', $rs->fields['software_archive']));
		header('Location: software_manage.php');
	}
} else {
	set_page_message(tr('Wrong software id.'));
	header('Location: software_manage.php');
}
