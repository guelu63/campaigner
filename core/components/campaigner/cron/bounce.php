<?php

//TODO: Objektorientiert -> Klasse EmailBounce mit Eigenschaften wie in $sub_array

require_once("bounce_driver.class.php");
require_once dirname(__FILE__).'/../../../../config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
include_once (MODX_CORE_PATH . "model/modx/modx.class.php");

$CUSTOM_HEADER = 'X-Campaigner-Mail-ID';
$start_index = isset($_GET['start_index']) ? $_GET['start_index'] : 1;

echo '<h2>Bounce-Management | Version 0.2.0</h2>';

$modx= new modX();
$modx->initialize('web');
$modx->lexicon->load('campaigner:default');
$campaigner = $modx->getService('campaigner', 'Campaigner', $modx->getOption('core_path'). 'components/campaigner/model/campaigner/');
if (!($campaigner instanceof Campaigner)) return;
$bouncehandler = new Bouncehandler();

$debug_bounce = $_GET['debug_bounce'];
if(isset($debug_bounce)) {    
    error_reporting(E_ALL); 
    ini_set('display_errors', TRUE);
}

echo 'Aufbauen der POP3-Connection...<br/>';
$inbox = imap_open("{mail.bundesliga.at:993/imap/ssl/novalidate-cert}INBOX", "news", "19FbRGmluI26oH9nA3K8");
$bounce_messages = array();

if($inbox) {
    echo 'POP3-Connection aufgebaut!<br/>';    
    if(imap_num_msg($inbox) > 0) {
        echo 'In diesem Postfach befinden sich '.imap_num_msg($inbox).' Nachricht(en)!<br/><br/>';
	
	echo '<strong>['.date('d.m.Y H:i:s',time()).'] - Starte das Auslesen bei Nachricht '.$start_index.'!</strong><br/>';
	
	if($start_index <= imap_num_msg($inbox)) {
	    for($i=$start_index; $i<=imap_num_msg($inbox); $i++) {
			echo '<hr/><span style="background-color:rgb(157, 213, 234)">['.date('d.m.Y H:i:s',time()).'] - Nachricht '.$i.'!</span><br/>';
			echo '<hr/><ul>';
		
			$header = imap_fetchheader($inbox,$i);
			$header_info = imap_headerinfo($inbox,$i);
			$body = imap_body($inbox,$i);
		
			if(isset($debug_bounce)) {
			    echo '<strong>'.$header.'</strong><br/>';
			    echo '<em>'.strip_tags($body).'</em><br/>';		
			}
			$tmp_array = $bouncehandler->parse_email($header.$body);
			$tmp_array2 = $tmp_array[0];
			$tmp_array2['subject'] = $header_info->subject;
			$tmp_array2['sent_date'] = $header_info->udate;
			$tmp_array2['queue_key'] = getCampaignerID($body, $CUSTOM_HEADER);
		
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
			
			
			echo "<li>SUBJECT: ".$tmp_array2['subject']."</li>";
			echo "<li>RECIPIENT: ".$tmp_array2['recipient']."</li>";
			echo "<li>SENT_DATE: ".$tmp_array2['sent_date']."</li>";
			echo "<li>QUEUE_KEY: ".$tmp_array2['queue_key']."</li>";
			echo "<li>ACTION: ".$tmp_array2['action']."</li>";
			echo "<li>STATUS: ".$tmp_array2['status']."</li>";
			echo "</ul><hr/><br/>";
	    }
	    
	    echo '<strong>['.date('d.m.Y H:i:s',time()).'] - Auslesen beendet!</strong><br/><br/>';
	    echo '<strong>['.date('d.m.Y H:i:s',time()).'] - Trage Bounces ein!</strong><br/>';
	    
		foreach($bounce_messages as $sub_array_over) {
			//Noetig, weil von der BounceHandler klasse ein Array im Array zurueck kommt
			$sub_array = $sub_array_over[0];
		
			echo '<hr/><span style="background-color:rgb(249, 215, 134)">['.date('d.m.Y H:i:s',time()).'] - Nachricht '.$sub_array['message_nr'].'!</span><br/>';
			echo '<hr/><ul>';
			echo "<li>SUBJECT: ".$sub_array['subject']."</li>";
			echo "<li>RECIPIENT: ".$sub_array['recipient']."</li>";
			echo "<li>SENT_DATE: ".$sub_array['sent_date']."</li>";
			echo "<li>QUEUE_KEY: ".$sub_array['queue_key']."</li>";
			echo "<li>ACTION: ".$sub_array['action']."</li>";
			echo "<li>STATUS: ".$sub_array['status']."</li>";
								
			$was_bounce = false;
			
			//Queue Objekt holen
			$queue_item = $modx->getObject('Queue', array(
				'key' => trim($sub_array['queue_key']))
			);
		
			if($queue_item) {
				//Subscriber Objekt holen
				$subscriber = $modx->getObject('Subscriber', array(
					'id' => trim($queue_item->get('subscriber')))
				);
				//Schauen ob dieser Queue-Eintrag ein Resend war
				//Resend-Check Objekt holen
				$resend_check = $modx->getObject('ResendCheck', array(
				'queue_id' => $queue_item->get('id'),
				));
			}
			else {
				unset($subscriber);
				unset($resend_check);
				echo "<li>Queue-Element nicht gefunden!</li>";
			}
			
			switch($sub_array['action']){
				case 'failed':
				echo '<li>Hardbounce wird verarbeitet</li>';
				//Hardbounce
				$sub_array['type'] = 'h';
				
				//Wenn es diesen Subscriber im System nicht gibt, soll auch kein Bounce gespeichert werden
				if($subscriber) {
					//1. Subscriber deaktivieren
					if($subscriber->get('active') != 0) {                            
					$subscriber->set('active', 0);
					$subscriber->save();
					echo "<li>Subscriber ".$subscriber->email." auf INAKTIV gesetzt!</li>";
					//2. In DB aufnehmen
					insertBounce($sub_array, $queue_item);
					}
					else {                            
					echo "<li>Subscriber ".$subscriber->email." war schon INAKTIV!</li>";
					}
				}
				else {
					echo "<li>Subscriber ".$subscriber->id." nicht im System gefunden!</li>";
				}
				
				//Wenn das ein Resend war
				if($resend_check) {
					echo '<li><span style="color:rgb(100, 170, 13)">Hardbounce bei RESEND!</span></li>';
					$resend_check->set('state', 3);
					$resend_check->save();
				}
				
				$was_bounce = true;
				break;
				case 'transient':
				echo '<li>Softbounce wird verarbeitet</li>';
				//Softbounce
				$sub_array['type'] = 's';
				//1. In DB aufnehmen
				//Wenn es diesen Subscriber im System nicht gibt, soll auch kein Bounce gespeichert werden
				if($subscriber) {
					insertBounce($sub_array, $queue_item);
				}
				else {
					echo "<li>Subscriber ".$subscriber->id." nicht im System gefunden!</li>";
				}
				
				//Wenn das ein Resend war
				if($resend_check) {
					echo '<li><span style="color:rgb(100, 170, 13)">Softbounce bei RESEND!</span></li>';
					$resend_check->set('state', 2);
					$resend_check->save();
				}
				
				$was_bounce = true;
				break;
				case 'autoreply':
				echo '<li>Auto-Reply: Nachricht nicht verarbeiten!</li>';
				//Nur ein OutOfOffice Reply -> nicht behandeln
				break;
				case 'delayed':
				echo '<li>Delayed: Nachricht nicht verarbeiten!</li>';
				//Nur ein OutOfOffice Reply -> nicht behandeln
				break;
				default:
				echo '<li>Kein ACTION!</li>';
				break;
			}
		
		if($was_bounce) {
		    imap_delete($inbox, $sub_array['message_nr']);
		    echo '<li><span style="color:rgb(219, 61, 71)">Nachricht zum Löschen markiert!</span></li>';
		}
		
		
		echo "</ul><hr/><br/>";
	    }
		
	    imap_expunge($inbox);
	    echo '<li><span style="color:rgb(219, 61, 71)">Markierte Nachrichten gelöscht!</span></li><br/>';
	    echo '<br/>In diesem Postfach befinden sich jetzt '.imap_num_msg($inbox).' Nachricht(en)!<br/>';
	}
        else {
	    echo 'Der Start-Index ist höher als die Anzahl an Nachrichten!<br/>';
	}

    }
    else {
        echo 'Es befinden sich keine Nachrichten in diesem Postfach!<br/>';
    }
    imap_close($inbox);
}
else {
    echo 'Fehler beim Aufbauen der POP3-Connection!<br/>';
    echo imap_last_error();
}

function getCampaignerID($body, $custom_header) {
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

function insertBounce($sub_array, $queue_item) {
    global $modx;    
    
    echo "Subscriber ID: ".$queue_item->get('subscriber')."<br/>";
    echo "Bounce wird in DB eingetragen [status=".$sub_array['status']."]!<br/>";
    
    $nb = $modx->newObject('Bounces');
    $nb->fromArray(array(
        'newsletter' => $queue_item->get('newsletter'),
        'subscriber' => $queue_item->get('subscriber'),
        'reason' => $sub_array['error_msg'],
        'type' => $sub_array['type'],
        'code' => $sub_array['status'],
	'recieved' => $sub_array['sent_date']
    ));
    $nb->save();
    
    //echo "Bounce in DB eingetragen!<br/>";
}
