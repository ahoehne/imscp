<?php
/**
 * i-MSCP - internet Multi Server Control Panel
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
 * The Original Code is "VHCS - Virtual Hosting Control System".
 *
 * The Initial Developer of the Original Code is moleSoftware GmbH.
 * Portions created by Initial Developer are Copyright (C) 2001-2006
 * by moleSoftware GmbH. All Rights Reserved.
 *
 * Portions created by the ispCP Team are Copyright (C) 2006-2010 by
 * isp Control Panel. All Rights Reserved.
 *
 * Portions created by the i-MSCP Team are Copyright (C) 2010-2013 by
 * i-MSCP - internet Multi Server Control Panel. All Rights Reserved.
 *
 * @category    i-MSCP
 * @package        iMSCP_Core
 * @subpackage    Orderpanel
 * @copyright   2001-2006 by moleSoftware GmbH
 * @copyright   2006-2010 by ispCP | http://isp-control.net
 * @copyright   2010-2013 by i-MSCP | http://i-mscp.net
 * @author      ispCP Team
 * @author      i-MSCP Team
 * @link        http://i-mscp.net
 */

/************************************************************************************
 * Script functions
 */

/**
 * Translate payment period
 *
 * @param string $paymentPeriod
 * @return string
 */
function translatePaymentPeriod($paymentPeriod)
{
	switch ($paymentPeriod) {
		case 'monthly':
			return tr('Monthly');
			break;
		case 'annually':
			return tr('Annually');
			break;
		case 'biennially':
			return tr('Biennially');
			break;
		case 'triennially';
			return tr('Triennially');
			break;
		default:
			return tr('Unknown');
	}
}

/**
 * Generates chart.
 *
 * @param iMSCP_pTemplate $tpl Template engine
 * @param int $userId User unique identifier
 * @param int $planId Plan unique identifier
 * @return void
 */
function generateChart($tpl, $userId, $planId)
{
	/** @var $cfg iMSCP_Config_Handler_File */
	$cfg = iMSCP_Registry::get('config');

	if (isset($cfg->HOSTING_PLANS_LEVEL) && $cfg->HOSTING_PLANS_LEVEL == 'admin') {
		$query = "SELECT * FROM `hosting_plans` WHERE `id` = ?";
		$stmt = exec_query($query, $planId);
	} else {
		$query = "SELECT * FROM `hosting_plans` WHERE `reseller_id` = ? AND `id` = ?";
		$stmt = exec_query($query, array($userId, $planId));
	}

	if (!$stmt->recordCount()) {
		redirectTo('index.php');
	} else {
		$price = $stmt->fields['price'];
		$setupFee = $stmt->fields['setup_fee'];
		$currency = $stmt->fields['value'];
		$vat = $stmt->fields['vat'];
		$subtotal = 0;
		$totalRecurring = 0;

		if ($price == 0 || $price == '') {
			$price = tr('Free of charge');
			$paymenPeriod = 'Free of charge';
		} else {
			$subtotal += $price;
			$totalRecurring = sprintf('%.02f', round($subtotal * (1 + $vat / 100), 2));
			$price .= ' ' . $currency . ' (<small>' . tr('Excl. tax') . '</small>)';
			$paymenPeriod = tohtml(translatePaymentPeriod($stmt->fields['payment']));
		}

		if ($setupFee == 0 || $setupFee == '') {
			$setupFee = tr('Free of charge');
		} else {
			$subtotal += $setupFee;
			$setupFee .= $currency . ' (<small>' . tr('Excl. tax') . '</small>)';
			;
		}

		$totalVat = sprintf('%.02f', round(round($subtotal * (1 + $vat / 100), 2) - $subtotal, 2));
		$totalDueToday = sprintf('%.02f', round($subtotal + $totalVat, 2));

		$tpl->assign(
			array(
				'PRICE' => $price,
				'SETUP_FEE' => $setupFee,
				'SUBTOTAL' => sprintf('%.02f', $subtotal) . ' ' . $currency,
				'VAT' => $vat,
				'TOTAL_VAT' => $totalVat . ' ' . $currency,
				'TOTAL_DUE_TODAY' => $totalDueToday . ' ' . $currency,
				'TOTAL_RECURRING' => $totalRecurring . ' ' . $currency,
				'PAYMENT_PERIOD' => $paymenPeriod,
				'TR_PACKAGE_NAME' => tohtml($stmt->fields['name'])));

		if ($stmt->fields['tos'] != '') {
			$tpl->assign(
				array(
					'TR_TOS_PROPS' => tr('Terms of Service'),
					'TR_TOS_ACCEPT' => tr('I Accept The Terms of Service'),
					'TOS' => tohtml($stmt->fields['tos'])));

			$_SESSION['order_panel_tos'] = true;
		} else {
			$tpl->assign('TOS_FIELD', '');
			$_SESSION['order_panel_tos'] = false;
		}
	}
}

/**
 * Genetates user personal data.
 *
 * @param iMSCP_pTemplate $tpl Template engine.
 *
 * @return void
 */
function generateUserPersonalData($tpl)
{
	$firstname = (isset($_SESSION['order_panel_fname'])) ? $_SESSION['order_panel_fname'] : '';
	$lastname = (isset($_SESSION['order_panel_lname'])) ? $_SESSION['order_panel_lname'] : '';
	$company = (isset($_SESSION['order_panel_firm'])) ? $_SESSION['order_panel_firm'] : '';
	$zip = (isset($_SESSION['order_panel_zip'])) ? $_SESSION['order_panel_zip'] : '';
	$city = (isset($_SESSION['order_panel_city'])) ? $_SESSION['order_panel_city'] : '';
	$state = (isset($_SESSION['order_panel_state'])) ? $_SESSION['order_panel_state'] : '';
	$country = (isset($_SESSION['order_panel_country'])) ? $_SESSION['order_panel_country'] : '';
	$street1 = (isset($_SESSION['order_panel_street1'])) ? $_SESSION['order_panel_street1'] : '';
	$street2 = (isset($_SESSION['order_panel_street2'])) ? $_SESSION['order_panel_street2'] : '';
	$phone = (isset($_SESSION['order_panel_phone'])) ? $_SESSION['order_panel_phone'] : '';
	$fax = (isset($_SESSION['order_panel_fax'])) ? $_SESSION['order_panel_fax'] : '';
	$email = (isset($_SESSION['order_panel_email'])) ? $_SESSION['order_panel_email'] : '';
	$gender = (isset($_SESSION['order_panel_gender']))
		? get_gender_by_code($_SESSION['order_panel_gender']) : get_gender_by_code('');

	$tpl->assign(
		array(
			'VL_USR_NAME' => tohtml($firstname),
			'VL_LAST_USRNAME' => tohtml($lastname),
			'VL_USR_FIRM' => tohtml($company),
			'VL_USR_POSTCODE' => tohtml($zip),
			'VL_USR_GENDER' => tohtml($gender),
			'VL_USRCITY' => tohtml($city),
			'VL_USRSTATE' => tohtml($state),
			'VL_COUNTRY' => tohtml($country),
			'VL_STREET1' => tohtml($street1),
			'VL_STREET2' => tohtml($street2),
			'VL_PHONE' => tohtml($phone),
			'VL_FAX' => tohtml($fax),
			'VL_EMAIL' => tohtml($email)
		)
	);
}

/************************************************************************************
 * Main script
 */

// Include needed libraries
require 'imscp-lib.php';

iMSCP_Events_Manager::getInstance()->dispatch(iMSCP_Events::onOrderPanelScriptStart);

/** @var $cfg iMSCP_Config_Handler_File */
$cfg = iMSCP_Registry::get('config');

if (isset($_SESSION['order_panel_user_id']) && isset($_SESSION['order_panel_plan_id'])) {
	$userId = $_SESSION['order_panel_user_id'];
	$hostingPlanId = $_SESSION['order_panel_plan_id'];
} else {
	showBadRequestErrorPage();
}

$tpl = new iMSCP_pTemplate();
$tpl->define_no_file('layout', implode('', gen_purchase_haf($userId)));

$tpl->define_dynamic(
	array(
		'page' => 'orderpanel/chart.tpl',
		'page_message' => 'page',
		'tos_field' => 'page'
	)
);

$tpl->assign(
	array(
		'THEME_CHARSET' => tr('encoding'),
		'TR_PAGE_TITLE' => tr('Order Panel / Chart'),
		'YOUR_CHART' => tr('Your Chart'),
		'TR_COSTS' => tr('Costs'),
		'TR_PRICE' => tr('Price'),
		'TR_SETUP_FEE' => tr('Setup Fee'),
		'TR_SUBTOTAL' => tr('Subtotal'),
		'TR_VAT' => tr('Vat'),
		'TR_TOTAL_DUE_TODAY' => tr('Total Due Today'),
		'TR_TOTAL_RECURRING' => tr('Total Recurring'),
		'TR_CANCEL' => tr('Cancel'),
		'TR_CONTINUE' => tr('Purchase'),
		'TR_CHANGE' => tr('Change'),
		'TR_FIRSTNAME' => tr('First name'),
		'TR_LASTNAME' => tr('Last name'),
		'TR_GENDER' => tr('Gender'),
		'TR_COMPANY' => tr('Company'),
		'TR_POST_CODE' => tr('Zip/Postal code'),
		'TR_CITY' => tr('City'),
		'TR_STATE' => tr('State/Province'),
		'TR_COUNTRY' => tr('Country'),
		'TR_STREET1' => tr('Street 1'),
		'TR_STREET2' => tr('Street 2'),
		'TR_EMAIL' => tr('Email'),
		'TR_PHONE' => tr('Phone'),
		'TR_FAX' => tr('Fax'),
		'TR_PERSONAL_DATA' => tr('Personal data'),
		'TR_CAPCODE' => tr('Security code'),
		'TR_IMGCAPCODE_DESCRIPTION' => tr('To avoid abuse, we ask you to write the combination of letters on the above picture.'),
		'TR_IMGCAPCODE' => '<img src="/imagecode.php" width="' .
			$cfg->LOSTPASSWORD_CAPTCHA_WIDTH . '" height="' .
			$cfg->LOSTPASSWORD_CAPTCHA_HEIGHT . '" border="0" alt="captcha image" />',
		'CANCEL_URI' => $_SESSION['order_panel_cancel_uri'],
	)
);

generateChart($tpl, $userId, $hostingPlanId);
generateUserPersonalData($tpl);
generatePageMessage($tpl);

$tpl->parse('LAYOUT_CONTENT', 'page');

iMSCP_Events_Manager::getInstance()->dispatch(iMSCP_Events::onOrderPanelScriptEnd, array('templateEngine' => $tpl));

$tpl->prnt();

unsetMessages();
