<?php
/**
 * Default English Lexicon Entries for campaigner
 *
 * @package campaigner
 * @subpackage lexicon
 */

//general
$_lang['campaigner'] = 'Newsletter';
$_lang['campaigner.fulltitle'] = 'Newsletter Management';
$_lang['campaigner.desc'] = 'Verwaltung ihrer Newsletter';

$_lang['campaigner.all']   = 'Alle';
$_lang['campaigner.or']    = 'Oder';
$_lang['campaigner.days']  = 'Tage';
$_lang['campaigner.weeks'] = 'Wochen';
$_lang['campaigner.usedefault'] = 'Standard verwenden';

$_lang['campaigner.error.save'] = 'Fehler beim speichern.';

//newsletter
$_lang['campaigner.newsletters'] = 'Newsletter';
$_lang['campaigner.newsletter'] = 'Newsletter';
$_lang['campaigner.newsletter.info'] = 'Eine komplette Liste der Newsletter die bereits gesendet wurden oder zum senden angesetzt sind.';
$_lang['campaigner.newsletter.subject'] = 'Betreff';
$_lang['campaigner.newsletter.date'] = 'Datum';
$_lang['campaigner.newsletter.sentdate'] = 'Gesendet am';
$_lang['campaigner.newsletter.document'] = 'Dokument';
$_lang['campaigner.newsletter.total'] = 'Total';
$_lang['campaigner.newsletter.sent'] = 'Gesendet';
$_lang['campaigner.newsletter.scheduled'] = 'Angesetzt';
$_lang['campaigner.newsletter.add'] = 'Neuer Newswletter';
$_lang['campaigner.newsletter.groups'] = 'Gruppen';
$_lang['campaigner.newsletter.remove'] = 'Newsletter l&ouml;schen';
$_lang['campaigner.newsletter.saved'] = 'Newsletter gespeichert (angesetzt)';
$_lang['campaigner.newsletter.state'] = 'Status';
$_lang['campaigner.newsletter.preview'] = 'Vorschau';
$_lang['campaigner.newsletter.bounced'] = 'Bounced';
$_lang['campaigner.newsletter.priority'] = 'Priorit&auml;t';
$_lang['campaigner.newsletter.approved'] = 'freigegeben';
$_lang['campaigner.newsletter.unapproved'] = 'zur&uuml;ckgezogen';
$_lang['campaigner.newsletter.approve'] = 'freigeben';
$_lang['campaigner.newsletter.unapprove'] = 'zur&uuml;ckziehen';
$_lang['campaigner.newsletter.sendagain'] = 'nochmals senden';
$_lang['campaigner.newsletter.edit'] = 'Newsletter bearbeiten';
$_lang['campaigner.newsletter.sender'] = 'Sender';
$_lang['campaigner.newsletter.senderemail'] = 'Sender Email';
$_lang['campaigner.newsletter.properties'] = 'Newsletter Eigenschaften';

$_lang['campaigner.newsletter.editproperties'] = 'Eigenschaften bearbeiten';
$_lang['campaigner.newsletter.assigngroups'] = 'Gruppen zuweisen';
$_lang['campaigner.newsletter.sendtest'] = 'Test senden';

$_lang['campaigner.newsletter.sendtest.email'] = 'Test-Email';
$_lang['campaigner.newsletter.sendtest.selectgroup'] = 'W&auml;hlen sie eine Gruppe um den Test an diese zu senden. Nur private Gruppen mit weniger als 10 Mitgliedern werden angezeigt.';
$_lang['campaigner.newsletter.sendtest.personalize'] = 'Newsletter personalisieren';

$_lang['campaigner.newsletter.preview.persona'] = 'personalisieren?';
$_lang['campaigner.newsletter.preview.nopersona'] = 'Nein';
$_lang['campaigner.newsletter.preview.personalize'] = 'Ja, f�r...';
$_lang['campaigner.newsletter.preview.showtext'] = 'Text anzeigen';
$_lang['campaigner.newsletter.preview.showhtml'] = 'HTML anzeigen';

$_lang['campaigner.newsletter.create.info'] = 'Please note: The normal way to make a Campaigner newsletter is to create a document within your newsletters folders as specified in the config.
But you can also create a newsletter from another location. By checking the "copy" option a copy will be moved to the newsletter location.';

$_lang['campaigner.newsletter.remove.title'] = 'Newsletter entfernen?';
$_lang['campaigner.newsletter.remove.confirm'] = 'Are you sure you want to remove this newsletter and all it\'s data? It is also removed from your modx documents.';

$_lang['campaigner.newsletter.error.notfound'] = 'Newsletter wurde nicht gefunden.';
$_lang['campaigner.newsletter.error.noreceiver'] = 'Kein Empf&auml;nger f&uuml;r den Testnewsletter angegeben.';

// autonewsletter
$_lang['campaigner.autonewsletter.frequency'] = 'Frequenz';
$_lang['campaigner.autonewsletter.time'] = 'Zeit';
$_lang['campaigner.autonewsletter.last'] = 'Zuletzt';
$_lang['campaigner.autonewsletter.start'] = 'Start';
$_lang['campaigner.newsletter.kicknow'] = 'jetzt ausl&ouml;sen';

$_lang['campaigner.autonewsletter.remove.title'] = 'Auto-Newsletter entfernen?';
$_lang['campaigner.autonewsletter.remove.confirm'] = 'Are you sure you want to remove this newsletter and all it\'s data? It is also removed from your modx documents.';
$_lang['campaigner.autonewsletter.description'] = 'Beschreibung';
$_lang['campaigner.subscribe.confirm.subject'] = 'Newsletter best&auml;tigen';

//groups
$_lang['campaigner.groups'] = 'Gruppen';
$_lang['campaigner.group'] = 'Gruppe';
$_lang['campaigner.groups.info'] = 'Gruppen f&uuml;r den Newsletter verwalten';
$_lang['campaigner.group.name'] = 'Name';
$_lang['campaigner.group.public'] = '&Ouml;ffentlich';
$_lang['campaigner.group.private'] = 'privat';
$_lang['campaigner.group.color'] = 'Farbe';
$_lang['campaigner.group.priority'] = 'Priorit&auml;t';
$_lang['campaigner.group.public.desc'] = '&Ouml;ffentlich (anmelden zu dieser Gruppe erlauben)';
$_lang['campaigner.group.subscribers'] = 'Mitglieder';
$_lang['campaigner.group.active'] = 'aktive Mitglieder';
$_lang['campaigner.group.add'] = 'neue Gruppe';
$_lang['campaigner.group.edit'] = 'Gruppe bearbeiten';
$_lang['campaigner.group.remove'] = 'Gruppe entfernen';
$_lang['campaigner.group.remove.title'] = 'Gruppe entfernen?';
$_lang['campaigner.group.remove.confirm'] = 'Diese Gruppe wirklich entfernen?';
$_lang['campaigner.group.removed'] = 'Gruppe wurde entfernt.';
$_lang['campaigner.group.update'] = 'Gruppe bearbeiten';
$_lang['campaigner.group.saved'] = 'Gruppe wurde gespeichert';
$_lang['campaigner.group.notfound'] = 'Gruppe wurde nicht gefunden.';
$_lang['campaigner.group.filter.public'] = '�ffentliche filtern...';


$_lang['campaigner.group.error.noname'] = 'Gruppenname muss angegeben werden.';
$_lang['campaigner.group.error.nocolor'] = 'Die Gruppe muss eine Farbe haben.';
$_lang['campaigner.group.error.invalidcolor'] = 'Bitte eine g&uuml;ltige Farbe angeben.';

//subscribers
$_lang['campaigner.subscribers'] = 'Abonnenten';
$_lang['campaigner.subscriber'] = 'Abonnent';
$_lang['campaigner.subscribers.info'] = 'Verwalten sie Abonnenten und deren Gruppen.';

$_lang['campaigner.subscriber.firstname'] = 'Vorname';
$_lang['campaigner.subscriber.lastname'] = 'Nachname';
$_lang['campaigner.subscriber.active'] = 'aktiv';
$_lang['campaigner.subscriber.inactive'] = 'inaktiv';
$_lang['campaigner.subscriber.activate'] = 'aktivieren';
$_lang['campaigner.subscriber.deactivate'] = 'deaktivieren';
$_lang['campaigner.subscriber.email'] = 'Email';
$_lang['campaigner.subscriber.type'] = 'Email-Art';
$_lang['campaigner.subscriber.groups'] = 'Gruppen';
$_lang['campaigner.subscriber.add'] = 'Neuer Abonnent';
$_lang['campaigner.subscriber.remove'] = 'Abonnent entfernen';
$_lang['campaigner.subscriber.remove.title'] = 'Abonnent entfernen?';
$_lang['campaigner.subscriber.remove.confirm'] = 'Diesen Abonnent wirklich f&uuml;r immer entfernen?';
$_lang['campaigner.subscriber.astext'] = 'Newsletter als Text empfangen';
$_lang['campaigner.subscriber.text'] = 'Text';
$_lang['campaigner.subscriber.html'] = 'Html';
$_lang['campaigner.subscriber.edit'] = 'Abonnent bearbeiten';
$_lang['campainger.subscriber.since'] = 'Registriert seit';

$_lang['campaigner.subscriber.saved'] = 'Abonnent gespeichert';
$_lang['campaigner.subscriber.removed'] = 'Abonnent entfernt';

$_lang['campaigner.subscriber.filter.type'] = 'Art filtern...';
$_lang['campaigner.subscriber.filter.group'] = 'Gruppen filtern...';
$_lang['campaigner.subscriber.filter.active'] = 'Aktive filtern...';

$_lang['campaigner.subscriber.error.noemail'] = 'Es muss eine g&uuml;ltige Email-Adresse angegeben werden';
$_lang['campaigner.subscriber.error.notfound'] = 'Abonnent wurde nicht gefunden';

$_lang['campaigner.subscribe.error.emailtaken'] = 'Diese Email-Adresse wird bereits verwendet.';
$_lang['campaigner.subscribe.error.noemail'] = 'Es muss eine g&uuml;ltige Email-Adresse angegeben werden.';
$_lang['campaigner.subscribe.error.nogroup'] = 'Bitte w&auml;hlen sie eine Gruppe.';
$_lang['campaigner.subscribe.success'] = 'Newsletter erfolgreich abonniert.';

$_lang['campaigner.unsubscribe.error.nosubscriber'] = 'Abonnent wurde nicht gefunden.';
$_lang['campaigner.unsubscribe.error.invalidkey'] = 'Sicherheitschl&uuml;ssel war ung&uuml;ltig.';
$_lang['campaigner.unsubscribe.success'] = 'Erfolgreich vom Newsletter abgemeldet.';

$_lang['campaigner.confirm.error.nosubscriber'] = 'Abonnent wurde nicht gefunden.';
$_lang['campaigner.confirm.error.active'] = 'Abonnent ist schon best&auml;tigt.';
$_lang['campaigner.confirm.error.invalidkey'] = 'Ativierungsschl�ssel war ung�ltig.';
$_lang['campaigner.confirm.success'] = 'Best&auml;tigung erfolgreich! Ab sofort erhalten sie unseren Newsletter.';

$_lang['campaigner.subscribers.exportcsv'] = 'CSV exportieren';
$_lang['campaigner.subscribers.exportxml'] = 'XML exportieren';

$_lang['campaigner.queue'] = 'Warteschlange';
$_lang['campaigner.queue.info'] = 'Jede Nachricht die �ber Campaigner verschickt wird, oder wurde, kommt in die Warteschlange';
$_lang['campaigner.queue.receiver'] = 'Empf&auml;nger';
$_lang['campaigner.queue.newsletter'] = 'Newsletter';
$_lang['campaigner.queue.state'] = 'Status';
$_lang['campaigner.queue.priority'] = 'Priorit&auml;t';
$_lang['campaigner.queue.sent'] = 'gesendet';
$_lang['campaigner.queue.waiting'] = 'wartend';
$_lang['campaigner.queue.show_processed'] = 'Verarbeitete anzeigen';
$_lang['campaigner.queue.hide_processed'] = 'Verarbeitete verstecken';
$_lang['campaigner.queue.remove'] = 'Element entfernen';
$_lang['campaigner.queue.removed'] = 'Element wurde entfernt';

$_lang['campaigner.queue.error.notfound'] = 'Queue item was not found';

$_lang['campaigner.queue.remove.title'] = 'Element wirklich entfernen?';
$_lang['campaigner.queue.remove.confirm'] = 'Wollen sie dieses Element wirklich entferenen?';
$_lang['campaigner.queue.remove.unsend'] = 'Wollen sie dieses Element wirklich entfernen? Der Newsletter wurde noch nicht an den Empf&auml;nger versandt. Wenn sie dieses Element jetzt l�schen wird das auch nicht geschehen.';

$_lang['campaigner.autonewsletter'] = 'Auto-Newsletter';
$_lang['campaigner.autonewsletter.info'] = 'Auto-Newsletter werden automatisch an vorbestimmten Zeiten versendet';
$_lang['campaigner.autonewsletter.subject'] = 'Betreff';
$_lang['campaigner.autonewsletter.groups'] = 'Gruppen';
$_lang['campaigner.autonewsletter.deactivate'] = 'deaktivieren';
$_lang['campaigner.autonewsletter.activate'] = 'aktivieren';


$_lang['campaigner.autonewsletter.error.notfound'] = 'Newsletter wurde nicht.';

//settings
$_lang['campaigner.settings.use_modxmailer'] = 'Use the Modx Mailer';
$_lang['campaigner.settings.use_modxmailer.desc'] = 'If this option is set to yes the integrated Modx mailer and the core mailer settings will be used for sending. Otherwise you can specify your own SMTP settings.';
