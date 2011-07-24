<?php
/**
 * i-MSCP - internet Multi Server Control Panel
 *
 * @copyright   2001-2006 by moleSoftware GmbH
 * @copyright   2006-2010 by ispCP | http://isp-control.net
 * @copyright   2010-2011 by i-msCP | http://i-mscp.net
 * @version     SVN: $Id$
 * @link        http://i-mscp.net
 * @author      ispCP Team
 * @author      i-MSCP Team
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
 *
 * Portions created by the ispCP Team are Copyright (C) 2006-2010 by
 * isp Control Panel. All Rights Reserved.
 *
 * Portions created by the i-MSCP Team are Copyright (C) 2010-2011 by
 * i-MSCP a internet Multi Server Control Panel. All Rights Reserved.
 */

/************************************************************************************
 * Main script
 */

// Include core library
require 'imscp-lib.php';

iMSCP_Events_Manager::getInstance()->dispatch(iMSCP_Events::onResellerScriptStart);

check_login(__FILE__);

/** @var $cfg iMSCP_Config_Handler_File */
$cfg = iMSCP_Registry::get('config');

$userId = $_SESSION['user_id'];

// Checks if support ticket system is activated and if the reseller can access to it
if (!hasTicketSystem($userId)) {
    redirectTo('index.php');
}

if (isset($_POST['uaction'])) {
    if (empty($_POST['subject'])) {
        set_page_message(tr('Please specify message subject.'));
    } elseif (empty($_POST['user_message'])) {
        set_page_message(tr('Please type your message.'));
    } else {
        createTicket($userId, $_SESSION['user_created_by'],
                     $_POST['urgency'], $_POST['subject'], $_POST['user_message'], 2);
        redirectTo('ticket_system.php');
    }
}

$userdata = array(
    'OPT_URGENCY_1' => '',
    'OPT_URGENCY_2' => '',
    'OPT_URGENCY_3' => '',
    'OPT_URGENCY_4' => '');

if (isset($_POST['urgency'])) {
    $userdata['URGENCY'] = intval($_POST['urgency']);
} else {
    $userdata['URGENCY'] = 2;
}

switch ($userdata['URGENCY']) {
    case 1:
        $userdata['OPT_URGENCY_1'] = $cfg->HTML_SELECTED;
        break;
    case 3:
        $userdata['OPT_URGENCY_3'] = $cfg->HTML_SELECTED;
        break;
    case 4:
        $userdata['OPT_URGENCY_4'] = $cfg->HTML_SELECTED;
        break;
    default:
        $userdata['OPT_URGENCY_2'] = $cfg->HTML_SELECTED;
}

$userdata['SUBJECT'] = isset($_POST['subject']) ? clean_input($_POST['subj'], true) : '';
$userdata['USER_MESSAGE'] = isset($_POST['user_message'])
    ? clean_input($_POST['user_message'], true) : '';

$tpl = new iMSCP_pTemplate();
$tpl->define_dynamic(array('page' => $cfg->RESELLER_TEMPLATE_PATH . '/ticket_create.tpl',
                          'page_message' => 'page',
                          'logged_from' => 'page'));

$tpl->assign(array(
                  'THEME_CHARSET' => tr('encoding'),
                  'TR_TICKET_PAGE_TITLE' => tr('i-MSCP - Reseller / Support Ticket System / New Ticket'),
                  'THEME_COLOR_PATH' => "../themes/{$cfg->USER_INITIAL_THEME}",
                  'ISP_LOGO' => layout_getUserLogo(),
                  'TR_SUPPORT_SYSTEM' => tr('Support Ticket System'),
                  'TR_NEW_TICKET' => tr('New ticket'),
                  'TR_LOW' => tr('Low'),
                  'TR_MEDIUM' => tr('Medium'),
                  'TR_HIGH' => tr('High'),
                  'TR_VERY_HIGH' => tr('Very high'),
                  'TR_URGENCY' => tr('Priority'),
                  'TR_EMAIL' => tr('Email'),
                  'TR_SUBJECT' => tr('Subject'),
                  'TR_YOUR_MESSAGE' => tr('Your message'),
                  'TR_SEND_MESSAGE' => tr('Send message'),
                  'TR_OPEN_TICKETS' => tr('Open tickets'),
                  'TR_CLOSED_TICKETS' => tr('Closed tickets')));

$tpl->assign($userdata);

gen_reseller_mainmenu($tpl, $cfg->RESELLER_TEMPLATE_PATH . '/main_menu_ticket_system.tpl');
gen_reseller_menu($tpl, $cfg->RESELLER_TEMPLATE_PATH . '/menu_ticket_system.tpl');
gen_logged_from($tpl);
generatePageMessage($tpl);

$tpl->parse('PAGE', 'page');

iMSCP_Events_Manager::getInstance()->dispatch(iMSCP_Events::onResellerScriptEnd,
                                              new iMSCP_Events_Response($tpl));

$tpl->prnt();
unsetMessages();
