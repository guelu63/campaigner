<?php
/**
 * Default English Lexicon Entries for campaigner
 *
 * @package campaigner
 * @subpackage lexicon
 */

//general
$_lang['campaigner'] = 'Campaigner';
$_lang['campaigner.fulltitle'] = 'Campaigner - Newsletter System';
$_lang['campaigner.desc'] = 'Manage your newsletter here.';

$_lang['campaigner.all']   = 'All';
$_lang['campaigner.or']    = 'Or';
$_lang['campaigner.days']  = 'days';
$_lang['campaigner.weeks'] = 'weeks';
$_lang['campaigner.usedefault'] = 'Use default';

$_lang['campaigner.error.save'] = 'Fehler beim speichern.';

//newsletter
$_lang['campaigner.newsletters'] = 'Newsletter';
$_lang['campaigner.newsletter'] = 'Newsletter';
$_lang['campaigner.newsletter.info'] = 'Here is a complete list of newsletters sended or sheduled for sending the next weeks.';
$_lang['campaigner.newsletter.subject'] = 'Subject';
$_lang['campaigner.newsletter.date'] = 'Date';
$_lang['campaigner.newsletter.sentdate'] = 'Sent Date';
$_lang['campaigner.newsletter.document'] = 'Document';
$_lang['campaigner.newsletter.total'] = 'Total';
$_lang['campaigner.newsletter.sent'] = 'Sent';
$_lang['campaigner.newsletter.sheduled'] = 'Sheduled';
$_lang['campaigner.newsletter.add'] = 'New newsletter';
$_lang['campaigner.newsletter.groups'] = 'Groups';
$_lang['campaigner.newsletter.remove'] = 'Remove newsletter';
$_lang['campaigner.newsletter.saved'] = 'Newsletter saved (scheduled)';
$_lang['campaigner.newsletter.state'] = 'Status';
$_lang['campaigner.newsletter.priority'] = 'Priority';
$_lang['campaigner.newsletter.preview'] = 'Preview';
$_lang['campaigner.newsletter.bounced'] = 'Bounced';
$_lang['campaigner.newsletter.approved'] = 'approved';
$_lang['campaigner.newsletter.unapproved'] = 'unapproved';
$_lang['campaigner.newsletter.approve'] = 'Approve';
$_lang['campaigner.newsletter.unapprove'] = 'Unapprovee';
$_lang['campaigner.newsletter.sendagain'] = 'Send again';
$_lang['campaigner.newsletter.edit'] = 'Edit newsletter';
$_lang['campaigner.newsletter.assigngroups'] = 'Gruppen zuweisen';
$_lang['campaigner.newsletter.sender'] = 'Sender';
$_lang['campaigner.newsletter.senderemail'] = 'Sender Email';
$_lang['campaigner.newsletter.properties'] = 'Newsletter properties';

$_lang['campaigner.newsletter.editproperties'] = 'Edit properties';
$_lang['campaigner.newsletter.assigngroups'] = 'Groups assignment';
$_lang['campaigner.newsletter.sendtest'] = 'Send test';

$_lang['campaigner.newsletter.sendtest.email'] = 'Test-Email';
$_lang['campaigner.newsletter.sendtest.selectgroup'] = 'Select group a test is sended to. Only groups with less than 10 members appear in the list.';
$_lang['campaigner.newsletter.sendtest.personalize'] = 'Personalize newsletter';

$_lang['campaigner.newsletter.preview.persona'] = 'Personalize?';
$_lang['campaigner.newsletter.preview.nopersona'] = 'No';
$_lang['campaigner.newsletter.preview.personalize'] = 'Yes, for...';
$_lang['campaigner.newsletter.preview.showtext'] = 'Show text';
$_lang['campaigner.newsletter.preview.showhtml'] = 'Show HTML';

$_lang['campaigner.newsletter.create.info'] = 'Please note: The normal way to make a Campaigner newsletter is to create a document within your newsletters folders as specified in the config.
But you can also create a newsletter from another location. By checking the "copy" option a copy will be moved to the newsletter location.';

$_lang['campaigner.newsletter.remove.title'] = 'Remove newsletter?';
$_lang['campaigner.newsletter.remove.confirm'] = 'Are you sure you want to remove this newsletter and all it\'s data? It is also removed from your modx documents.';

$_lang['campaigner.newsletter.error.notfound'] = 'Newsletter wurde nicht gefunden.';
$_lang['campaigner.newsletter.error.noreceiver'] = 'Kein Empfänger für den Testuser angegeben.';

// autonewsletter
$_lang['campaigner.autonewsletter.frequency'] = 'Frequency';
$_lang['campaigner.autonewsletter.time'] = 'Time';
$_lang['campaigner.autonewsletter.last'] = 'Last';
$_lang['campaigner.autonewsletter.start'] = 'Start';

$_lang['campaigner.autonewsletter.remove.title'] = 'Remove Auto-Newsletter?';
$_lang['campaigner.autonewsletter.remove.confirm'] = 'Are you sure you want to remove this newsletter and all it\'s data? It is also removed from your modx documents.';

$_lang['campaigner.subscribe.confirm.subject'] = 'Confirm newsletter registration';

//groups
$_lang['campaigner.groups'] = 'Groups';
$_lang['campaigner.group'] = 'Gruppe';
$_lang['campaigner.groups.info'] = 'Manager newsletter groups';
$_lang['campaigner.group.name'] = 'Name';
$_lang['campaigner.group.public'] = 'Public';
$_lang['campaigner.group.private'] = 'Private';
$_lang['campaigner.group.color'] = 'Color';
$_lang['campaigner.group.priority'] = 'Priority';
$_lang['campaigner.group.public.desc'] = 'Public (allow subscription through form)';
$_lang['campaigner.group.subscribers'] = 'Members';
$_lang['campaigner.group.active'] = 'Active members';
$_lang['campaigner.group.add'] = 'New group';
$_lang['campaigner.group.edit'] = 'Edit group';
$_lang['campaigner.group.remove'] = 'Remove group';
$_lang['campaigner.group.remove.title'] = 'Remove group?';
$_lang['campaigner.group.remove.confirm'] = 'Are you sure you want to remove this group?';
$_lang['campaigner.group.removed'] = 'The group was removed.';
$_lang['campaigner.group.update'] = 'Update group';
$_lang['campaigner.group.saved'] = 'Group saved';
$_lang['campaigner.group.notfound'] = 'Group was not found.';
$_lang['campaigner.group.filter.public'] = 'Filter public...';


$_lang['campaigner.group.error.noname'] = 'Group name must not be emtpy.';
$_lang['campaigner.group.error.nocolor'] = 'Group color mus not be empty.';
$_lang['campaigner.group.error.invalidcolor'] = 'Please provide a valid group color.';

//subscribers
$_lang['campaigner.subscribers'] = 'Subscribers';
$_lang['campaigner.subscriber'] = 'Subscriber';
$_lang['campaigner.subscribers.info'] = 'Manage all subscribers with their groups';

$_lang['campaigner.subscriber.firstname'] = 'First name';
$_lang['campaigner.subscriber.lastname'] = 'Last name';
$_lang['campaigner.subscriber.active'] = 'Active';
$_lang['campaigner.subscriber.inactive'] = 'Inactive';
$_lang['campaigner.subscriber.activate'] = 'Activate';
$_lang['campaigner.subscriber.deactivate'] = 'Deactivate';
$_lang['campaigner.subscriber.email'] = 'Email';
$_lang['campaigner.subscriber.type'] = 'Email-Type';
$_lang['campaigner.subscriber.groups'] = 'Abos';
$_lang['campaigner.subscriber.add'] = 'New subscriber';
$_lang['campaigner.subscriber.remove'] = 'Remove subscriber';
$_lang['campaigner.subscriber.remove.title'] = 'Remove subscriber?';
$_lang['campaigner.subscriber.remove.confirm'] = 'Are you sure you want to remove this subscriber permanently?';
$_lang['campaigner.subscriber.astext'] = 'Receive newsletter as text';
$_lang['campaigner.subscriber.text'] = 'Text';
$_lang['campaigner.subscriber.html'] = 'Html';
$_lang['campaigner.subscriber.edit'] = 'Update subscriber';
$_lang['campaigner.subscriber.since'] = 'Registered since';

$_lang['campaigner.subscriber.saved'] = 'Subscriber saved';
$_lang['campaigner.subscriber.removed'] = 'Subscriber removed';

$_lang['campaigner.subscriber.filter.type'] = 'Filter type...';
$_lang['campaigner.subscriber.filter.group'] = 'Filter group...';
$_lang['campaigner.subscriber.filter.active'] = 'Filter active...';

$_lang['campaigner.subscriber.error.noemail'] = 'You must provide a valid email address';
$_lang['campaigner.subscriber.error.notfound'] = 'Subscriber not found';

$_lang['campaigner.subscribe.error.emailtaken'] = 'The email you provided is allrady in use.';
$_lang['campaigner.subscribe.error.noemail'] = 'Please provide a valid email address.';
$_lang['campaigner.subscribe.error.nogroup'] = 'Please select a group to subscribe to.';
$_lang['campaigner.subscribe.success'] = 'Subscribed to newsletter.';

$_lang['campaigner.unsubscribe.error.nosubscriber'] = 'Subscriber not found.';
$_lang['campaigner.unsubscribe.error.invalidkey'] = 'Security key was invalid.';
$_lang['campaigner.unsubscribe.success'] = 'Unsubscribed from newsletter.';

$_lang['campaigner.confirm.error.nosubscriber'] = 'Subscriber not found.';
$_lang['campaigner.confirm.error.active'] = 'Subscriber allready active.';
$_lang['campaigner.confirm.error.invalidkey'] = 'Activation key was invalid.';
$_lang['campaigner.confirm.success'] = 'Confirmation successful. You will receive the newsletter from now on.';

$_lang['campaigner.subscribers.exportcsv'] = 'Export CSV';
$_lang['campaigner.subscribers.exportxml'] = 'Export XML';

$_lang['campaigner.subscribers.importcsv'] = 'Import CSV';
$_lang['campaigner.subscribers.importcsv.start'] = 'Start import';
$_lang['campaigner.subscribers.importcsv.file'] = 'File';
$_lang['campaigner.subscribers.importcsv.results'] = 'Results';
$_lang['campaigner.subscribers.importcsv.err.uploadfile'] = 'Please, upload a file';
$_lang['campaigner.subscribers.importcsv.err.cantopenfile'] = 'Can\'t open file';
$_lang['campaigner.subscribers.importcsv.err.firstrow'] = 'First row must contain column names (first column must be email)';
$_lang['campaigner.subscribers.importcsv.err.cantsaverow'] = 'Can\'t save row [[+rownum]]';
$_lang['campaigner.subscribers.importcsv.err.skippedrow'] = 'Skipped row [[+rownum]]';
$_lang['campaigner.subscribers.importcsv.msg.complete'] = 'Import complete. Imported [[+importCount]] records ([[+newCount]] new)';
$_lang['campaigner.subscribers.confirm.subject'] = 'Confirm your newsletter subscription';
$_lang['campaigner.subscribers.confirm.success'] = 'You are now subscribed to our newsletter.';
$_lang['campaigner.subscribers.confirm.err'] = 'Subscriber / code combination incorrect.';
$_lang['campaigner.subscribers.signup.err.emailunique'] = 'Email address already in use';
$_lang['campaigner.subscribers.unsubscribe.success'] = 'You have been removed from our mailing list.';
$_lang['campaigner.subscribers.unsubscribe.err'] = 'Subscriber not found.';
$_lang['campaigner.subscribers.saved'] = 'Subscriber saved';
$_lang['campaigner.subscribers.error'] = 'Error while saving subscriber';

$_lang['campaigner.queue'] = 'Queue';
$_lang['campaigner.queue.info'] = 'Every email sended via Campaigner or is sheduled to be sended gets into the queue.';
$_lang['campaigner.queue.receiver'] = 'Receiver';
$_lang['campaigner.queue.newsletter'] = 'Newsletter';
$_lang['campaigner.queue.state'] = 'State';
$_lang['campaigner.queue.priority'] = 'Priority';
$_lang['campaigner.queue.sent'] = 'Sent';
$_lang['campaigner.queue.waiting'] = 'Waiting';
$_lang['campaigner.queue.show_processed'] = 'show processed';
$_lang['campaigner.queue.hide_processed'] = 'hide processed';
$_lang['campaigner.queue.remove'] = 'Remove queue item';
$_lang['campaigner.queue.removed'] = 'Queue item was removed';

$_lang['campaigner.queue.error.notfound'] = 'Queue item was not found';

$_lang['campaigner.queue.remove.title'] = 'Remove queue item?';
$_lang['campaigner.queue.remove.confirm'] = 'Are you sure you want to remove the queue item?';
$_lang['campaigner.queue.remove.unsend'] = 'Are you sure you want to remove the queue item? The newsletter wasn\'t sent to the subscriber yet. If you delete the queue item it will not be send to this subscriber.';

$_lang['campaigner.autonewsletter'] = 'Auto-Newsletter';
$_lang['campaigner.autonewsletter.info'] = 'Auto-Newsletters are automated Newsletters sent periodically on sheduled times.';
$_lang['campaigner.autonewsletter.subject'] = 'Subject';
$_lang['campaigner.autonewsletter.groups'] = 'Groups';
$_lang['campaigner.autonewsletter.deactivate'] = 'Deactivate newsletter';
$_lang['campaigner.autonewsletter.activate'] = 'Activate newsletter';


$_lang['campaigner.autonewsletter.error.notfound'] = 'Newsletter could not be found.';

//settings
$_lang['campaigner.settings.use_modxmailer'] = 'Use the Modx Mailer';
$_lang['campaigner.settings.use_modxmailer.desc'] = 'If this option is set to yes the integrated Modx mailer and the core mailer settings will be used for sending. Otherwise you can specify your own SMTP settings.';

$_lang['campaigner.settings'] = 'Settings';
$_lang['campaigner.settings.name'] = 'Name';
$_lang['campaigner.settings.email'] = 'Email';
$_lang['campaigner.settings.bounceemail'] = 'Bounce email address';
$_lang['campaigner.settings.confirmpage'] = 'Confirmation page';
$_lang['campaigner.settings.unsubscribepage'] = 'Unsubscribe page';
$_lang['campaigner.settings.template'] = 'Template';
$_lang['campaigner.settings.saved'] = 'Settings saved';
$_lang['campaigner.settings.error'] = 'Error while saving settings';