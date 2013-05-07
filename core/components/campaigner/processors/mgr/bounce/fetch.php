<?php
/**
 * Fetches bounce messages
 *
 * Messages which are returned to value of system setting 'RETURN PATH'
 * will be fetched and diagnosed. If meeting bounce message items will
 * be created. Furthermore there will be a console window popping up with
 * some information regarding the created items
 *
 * @var string
 * @todo  Look into Bounce parsing
 */

// // USE MODx external
// require_once dirname(__FILE__) . '../../../../../../../config.core.php';
// require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';

// include_once (MODX_CORE_PATH . "model/modx/modx.class.php");

// $modx= new modX();
// $modx->initialize('web');

// // get the model
// $campaigner = $modx->getService('campaigner', 'Campaigner', $modx->getOption('core_path'). 'components/campaigner/model/campaigner/');
// if (!($campaigner instanceof Campaigner)) return;

// Watch out for this header
$CUSTOM_HEADER = 'X-Campaigner-Mail-ID';
$start_index = isset($_GET['start_index']) ? $_GET['start_index'] : 1;

// Include bounce class
require_once($modx->getOption('core_path') . 'components/campaigner/cron/bounce_driver.class.php');
$bouncehandler = new Bouncehandler();

$debug_bounce = $_GET['debug_bounce'];

// Aufbauen der IMAP-Connection';
$inbox = imap_open("{finch.arvixe.com:143}", "anti@herooutoftime.com", "ibelod");

$bounce_messages = array();

// Let's make some tests to check the base
// Are we connected?
if(!$inbox) {
	$modx->log(MODX_LOG_LEVEL_INFO, 'Fehler beim Aufbauen der POP3-Connection!<br/>' . imap_last_error());
	$modx->log(MODX_LOG_LEVEL_INFO,'COMPLETED');
	return $modx->error->success('');
}
// Are there messages?
if(imap_num_msg($inbox) <= 0) {
	$modx->log(MODX_LOG_LEVEL_INFO, 'Keine Nachrichten vorhanden!');
	$modx->log(MODX_LOG_LEVEL_INFO,'COMPLETED');
	return $modx->error->success('');
}
// Are there unread messages?
if($start_index > imap_num_msg($inbox)) {
	$modx->log(MODX_LOG_LEVEL_INFO, 'Startindex hoeher als Anzahl der Nachrichten!');
	$modx->log(MODX_LOG_LEVEL_INFO,'COMPLETED');
	return $modx->error->success('');
}

// Looks like messages were found - do it!
for($i=$start_index; $i<=imap_num_msg($inbox); $i++) {		
	$header = imap_fetchheader($inbox,$i);
	$header_info = imap_headerinfo($inbox,$i);
	$body = imap_body($inbox,$i);

	// Check for bounce message
	$tmp_array = $bouncehandler->parse_email($header.$body);
	$tmp_array2 = $tmp_array[0];
	$tmp_array2['subject'] = $header_info->subject;
	$tmp_array2['sent_date'] = $header_info->udate;

	// Grab the queue key set as a X-HEADER variable
	$queue_key = getCampaignerID($body, $CUSTOM_HEADER);
	// If false => go to next message
	if(!$queue_key)
		continue;
	$tmp_array2['queue_key'] = $queue_key;

	//Wenn die BounceHandler Klasse beim RFC Code versagt:
	if(empty($tmp_array2['status'])) {
		preg_match('/Status: ([0-9]\.[0-9]\.[0-9])/', $header.$body, $code);
		$tmp_array2['status'] = $code[1];
	}

	//Wenn die BounceHandler Klasse bei der Action versagt:
	if(empty($tmp_array2['action'])) {
		preg_match('/Action: (failed|transient|autoreply|delayed)/', $header.$body, $code);
		$tmp_array2['action'] = $code[1];
	}

	$tmp_array2['message_nr'] = $i;
	$tmp_array[0] = $tmp_array2;
	array_push($bounce_messages, $tmp_array);
}

// var_dump($bounce_messages);
// return $modx->error->success('');
// $modx->log(MODX_LOG_LEVEL_INFO,'COMPLETED');
// return $modx->error->success('');

foreach($bounce_messages as $sub_array_over) {
	//Noetig, weil von der BounceHandler klasse ein Array im Array zurueck kommt
	$sub_array = $sub_array_over[0];
	$message[$sub_array['message_nr']]['message'] = $sub_array;

	// continue;					
	$was_bounce = false;
	
	// Fetch queue object
	// $queue_item = $modx->getObject('Queue', array(
	// 	'key' => trim('eccbc87e4b5ce2fe28308fd9f2a7baf3'))
	// );
	$queue_item = $modx->getObject('Queue', array(
		'key' => trim($sub_array['queue_key']))
	);

	if($queue_item) {
		// Get subscriber
		$subscriber = $modx->getObject(
			'Subscriber',
			array(
				'id' => trim($queue_item->get('subscriber'))
			)
		);

		//Schauen ob dieser Queue-Eintrag ein Resend war
		//Resend-Check Objekt holen
		$resend_check = $modx->getObject(
			'ResendCheck',
			array(
				'queue_id' => $queue_item->get('id'),
			)
		);
	} else {
		unset($subscriber);
		unset($resend_check);
		// echo "<li>Queue-Element nicht gefunden!</li>";
	}

	switch($sub_array['action']){
		case 'failed': // Hard-Bounce
			$sub_array['type'] = 'h';
			
			// No subscriber => do nothing
			if(!$subscriber) {
				$message[$sub_array['message_nr']]['success'] = false;
				continue;
			}
			// Disable subscriber
			if($subscriber->get('active') == 0) {
				$message[$sub_array['message_nr']]['success'] = false;
				continue;
			}
			$subscriber->set('active', 0);
			$subscriber->save();
			
			$nb = $modx->newObject('Bounces');
		    $nb->fromArray(
		    	array(
			        'newsletter' => $queue_item->get('newsletter'),
			        'subscriber' => $queue_item->get('subscriber'),
			        'reason' => $sub_array['error_msg'],
			        'type' => $sub_array['type'],
			        'code' => $sub_array['status'],
					'recieved' => $sub_array['sent_date']
		    	)
		    );
		    if($nb->save())
		    	$message[$sub_array['message_nr']]['success'] = true;
			
			//Wenn das ein Resend war
			if($resend_check) {
				$resend_check->set('state', 3);
				$resend_check->save();
			}
			
			$was_bounce = true;
			break;
		case 'transient': // Softbounce
			$sub_array['type'] = 's';
			
			// Subscriber exists => create a bounce
			if($subscriber) {
				$nb = $modx->newObject('Bounces');
		    	$nb->fromArray(
		    		array(
				        'newsletter' => $queue_item->get('newsletter'),
				        'subscriber' => $queue_item->get('subscriber'),
				        'reason' => $sub_array['error_msg'],
				        'type' => $sub_array['type'],
				        'code' => $sub_array['status'],
						'recieved' => $sub_array['sent_date']
		    		)
		    	);
		    	if($nb->save())
		    		$message[$sub_array['message_nr']]['success'] = true;
		    }
			
			// Was a resend => save as resend
			if($resend_check) {
				$resend_check->set('state', 2);
				// $resend_check->save();
			}
			
			$was_bounce = true;
			break;
		case 'autoreply': // OutOfOffice Reply => do nothing
			break;
		case 'delayed': // OutOfOffice Reply => nicht behandeln
			break;
		default:
			break;
	}

	// Delete message when bounce was created
	// if($was_bounce)
	//     imap_delete($inbox, $sub_array['message_nr']);
}

$modx->log(MODX_LOG_LEVEL_INFO, '<strong>There are  messages '. imap_num_msg($inbox) . ' in this inbox</strong>');
$modx->log(MODX_LOG_LEVEL_INFO, '<strong>' . count($bounce_messages) . ' messages are bounce messages</strong>');

$smarty = $modx->getService('smarty','smarty.modSmarty');

$o = array();
foreach($message[$sub_array['message_nr']] as $msg) {
	// var_dump($msg);
	$smarty->assign('message', $msg);
	$smarty->assign('path', $modx->getOption('site_url'));
	$o[] = $smarty->fetch($modx->getOption('core_path') . 'components/campaigner/elements/smarty/message.tpl');
}
// var_dump($o);
$modx->log(MODX_LOG_LEVEL_INFO, '<table>' . implode('', $o) . '</table>');
$modx->log(MODX_LOG_LEVEL_INFO,'COMPLETED');

return $modx->error->success('');

// imap_expunge($inbox);
imap_close($inbox);

function getCampaignerID($body, $custom_header) {
	if(strpos($body, $custom_header) === FALSE)
		return false;
    //Zuerst finden, wo der Header im Body steht
    $custom_header_start = strpos($body,$custom_header);
    //Dann herausfinden wo der Key selbst steht. Normalerweise hat das ganze die Form >Header-Name: Key<
    //Der key beginnt also beim Start vom Header-Namen + der Laenge des Header-Namen und dem Doppelpunkt und Leerzeichen nach dem Header-Namen
    $key_start = $custom_header_start+strlen($custom_header)+2;
    //Nun den Key herausschneiden
    //da ein md5 verschluesselter String immer 32 Zeichen lang ist, kann man das leicht machen
    $key = rtrim(substr($body, $key_start, 32));
    return $key;
}

// function insertBounce($sub_array, $queue_item) {
//     global $modx;    
    
//     // echo "Subscriber ID: ".$queue_item->get('subscriber')."<br/>";
//     // echo "Bounce wird in DB eingetragen [status=".$sub_array['status']."]!<br/>";
    
//     $nb = $modx->newObject('Bounces');
//     $nb->fromArray(
//     	array(
// 	        'newsletter' => $queue_item->get('newsletter'),
// 	        'subscriber' => $queue_item->get('subscriber'),
// 	        'reason' => $sub_array['error_msg'],
// 	        'type' => $sub_array['type'],
// 	        'code' => $sub_array['status'],
// 			'recieved' => $sub_array['sent_date']
//     	)
//     );
//     $nb->save();
    
//     //echo "Bounce in DB eingetragen!<br/>";
// }
