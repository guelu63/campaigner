# Campaigner

![tabs]

## [Newsletter](id:newsletter)
*Newsletter, die in dafür vorgesehenen MODx-Containern [^1], wie in den [System-Einstellungen](#settings) eingestellt, erstellt werden, werden automatisch in die Newsletter-Komponente übernommen. Zudem werden bereits versendete Auto-Newsletter Instanzen hier abgelegt um ein Archiv zur Verfügung stellen zu können*

[^1]: MODx - Modernes CMS/CMF [www.modx.com](http://www.modx.com)

### Handhabung
* Erstellen der Ressource als normale Ressource
	* Einstellen der geplanten Versandzeit durch das Veröffentlichungsdatum
	* Anhängen von Attachments
* Automatische Übernahme in die Komponente
* Definieren der Gruppe(n) an welche der Newsletter versendet werden soll
* Voransicht anzeigen lassen
* Versand an Test-Abonnenten zur Kontrolle

### Funktionen

* Vorschau
	* Voransicht des Newsletters ohne prozessierte Tags (z.B.: `[[+campaigner.firstname]]`)
	* Voransicht des Newsletters für einen Abonnenten. Dadurch werden oben genannte Tags prozessiert (z.B.: aus `[[+campaigner.firstname]]` wird 'Andreas')
	* Text-Mail anzeigen
* Test-Versand
	* Versand an einen einzelnen Abonnenten oder externe E-Mail Adresse
		* Optionale Personalisierung
	* Versand an eine Gruppe von Abonnenten
		* Optionale Personalisierung
	* Hinzufügen einer Nachricht (z.B.: 'Mit der Bitte um Kontrolldurchsicht')
* Auslösung
	* Ein geplanter Newsletter kann auf Wunsch sofort ausgelöst werden
* Eigenschaften
	* Sender - Der Name des Versenders
	* Sender-Email - Die Absender-Email
	* Priorität - Wie wichtig ist dieser Newsletter?
	* Status - Welchen Status hat dieser Newsletter?

###Verfügbare Tags
*Es stehen sogenannte Tags zur Verfügung die es ermöglichen Informationen über den Newsletter (z.B. Gesamtanzahl) im Newsletter zu hinterlegen*

**Derzeit verfügbare Tags**

	Total [[+campaigner.total]]
	Sender [[+campaigner.sender]]
	Sender-Email [[+campaigner.sender_email]]
	Date [[+campaigner.date]]
	Send date [[+campaigner.send_date]]

## [Auto-Newsletter](id:auto-newsletter)
*Auto-Newsletter sind Newsletter die in periodischen Abständen automatisch generiert und, wenn erlaubt, versendet werden. Die Erlaubnis des automatischen Versands wird über eine [Systemeinstellung](#settings) vorgenommen.*

### Handhabung
Die Handhabung der Auto-Newsletter unterscheidet sich geringfügig von der "normalen" Newslettern. Zusätzlich gibt es die Möglichkeit die Frequenz der Generierung zu bestimmen.

### Funktionen
* Vorschau
	* Voransicht des Newsletters
* Eigenschaften
	* zusätzlich zu den Eigenschaften des "normalen" Newsletters
	* Start - Datum und Zeit
	* Zuletzt - Zuletzt stattgefundener Versand des Newsletters
	* Zyklus - Wert der Wiederholung
	* Faktor - Faktor der Wiederholung
	* Beschreibung - Beschreibung eines Newsletters
* Plan anstossen - Den Autonewsletter anstossen
* Plan einsehen - Die geplanten Versendungen dieses Auto-Newsletters ()

## Technik Newsletter & Autonewsletter

### Autonewsletter-Scheduling
Autonewsletter werden wenn nötig automatisch geplant und abgewickelt (siehe `campaigner.newsletter.autosend` [System-Einstellungen](#settings)). Dies dient der Chronologie und ermöglicht ein einfaches Versenden der Newsletter. Ein geplanter Autonewsletter erstellt bei Bedarf eine neue Newsletter-Instanz. Der Bedarfsfall ist in der unten angeführten SQL-Query aufgezeigt.

	$c = $this->modx->newQuery('Autonewsletter');
    $c->where('`Autonewsletter`.`state` = 1 AND UNIX_TIMESTAMP() > (GREATEST((`Autonewsletter`.`start`), COALESCE(`Autonewsletter`.`last`, 0)) + `Autonewsletter`.`frequency` + TIME_TO_SEC(`Autonewsletter`.`time`))');

### Warteschlange-Erstellung
Wenn ein Newsletter zum Versand freigegeben wird, erstellt Campaigner automatisch die Liste der Abonnenten die diesen Newsletter erhalten sollen. Unten angeführt die SQL-Query sowie die Tabellen-Struktur zwecks Verständnis.

	$c->where('`Newsletter`.`state` = 1');
    $c->where('((sent_date IS NULL AND `modDocument`.`publishedon` < UNIX_TIMESTAMP() AND `modDocument`.`publishedon` > 0) OR (`modDocument`.`pub_date` < UNIX_TIMESTAMP() AND `modDocument`.`publishedon` < `modDocument`.`pub_date` AND `modDocument`.`pub_date` > 0))');

![sql_queue]

### Caching
Um die Performance des Versandes zu erhöhen, wird ein Newsletter vorab prozessiert, lediglich die Abonnenten-Tags werden dabei nicht durch den Parser da diese pro Abonnent angepasst werden müssen.

	$cacheOptions = array(
        xPDO::OPT_CACHE_KEY => '',
        xPDO::OPT_CACHE_HANDLER => 'xPDOFileCache',
        xPDO::OPT_CACHE_EXPIRES => 0,
    );
    $cacheElementKey = 'newsletter/' . $document->get('id');
    $this->modx->cacheManager->set($cacheElementKey, $composedNewsletter, 0, $cacheOptions);

### Prozessieren
Beim Prozessieren werden je nach Stapelgrösse (siehe *campaigner.batchsize* [System-Einstellugen](#settings)) unversendete Elemente durch eine Schleife abgearbeitet. Dabei wird auf das vorab erstellte Cache-File des Newsletters zugegriffen und die Abonnenten-Tags eingearbeitet.

	$c->where('`Queue`.`state`=0 OR `Queue`.`state`=8');
    $c->leftJoin('Subscriber');
    $c->select('`Queue`.*, `Queue`.`key` AS queue_key, `Subscriber`.`firstname`, `Subscriber`.`lastname`, `Subscriber`.`email`, `Subscriber`.`text`, `Subscriber`.`key`, `Subscriber`.`address`, `Subscriber`.`title`');
    $c->sortby('`Queue`.`priority`');


## [Gruppen](id:groups)

![groups]

*Gruppen dienen dazu Abonnenten einzuteilen und werden von Newsletter-Prozessen verwendet um die []Warteschlange](#queue) zu erstellen. Je nach [System-Einstellung](#settings) können sich Anwender für ein oder meherere Gruppen anmelden. Dabei kann grundsätzlich jeder Abonnent unendlich vielen Gruppen angehören.*

### Funktionen
* Gruppe erstellen
	* Erstellen von Gruppen und anschliessendem zuweisen von Abonnenten
	* Eine Gruppe hat 4 Parameter:
		* Name
		* Allgemein (Öffentlich)
		* Farbe
		* Priorität
* Gruppe bearbeiten - gleiche Funktionalität wie "Gruppe erstellen"
* Gruppe entfernen - Eine Gruppe unwiderrruflich löschen
* Abonnenten zuweisen
	* Funktion zum einfacheren Zuweisen mehrerer Abonnenten zu einer Gruppe

## Abonnenten

![subscriber]

*Abonnenten sind die Empfänger von Newslettern. Diese werden entweder durch die eigene Anmeldung an den Newsletter erstellt oder können durch Import in das System eingepflegt werden. Je mehr Informationen über einen Abonnenten vorhanden sind, umso spezifischer können Newsletter gestaltet werden*

### Handhabung
* Selbstanmeldung
	* Der User meldet sich über ein auf der Website positioniertes Formular für den Newsletter an
	* Durch das "Opt-In" System wird ihm zunächst ein Mail mit Aktivierungslink zugesendet, welcher geklickt werden muss um den Abonnenten zu aktivieren. (dies kann von Benutzern mit ausreichenden Rechten auch händisch durchgeführt werden)
* Selbstabmeldung
	* Laut Gesetz muss in jedem versendeten Newsletter ein Link zur Abmeldung zur Verfügung stehen. Durch Klick auf diesen Link wird der Abonnent auf eine Seite geleitet die ihm dies ermöglicht und auch ein Grund zur Abmeldung kann angegeben werden.
	* Benutzer mit ausreichenden Berechtigungen können Abonnenten auch händisch aus dem System entfernen oder deaktivieren.

### Funktionen
* Erstellen/Bearbeiten
	* Bei der Erstellung/Bearbeitung eines Abonnenten gibt es benötigte Felder und optionale Felder
	* Das einzig **benötigte** Feld ist die **E-Mail Adresse**
	* Das Erstellungs-/Bearbeitungsfenster eines Abonnenten teilt sich in 4 Tabs auf:
		* Generell - Grundlegende beschreibende Daten
		* Adresse - Adressdaten wie Strasse, PLZ, Ort, Land
		* Gruppen - angehörige Gruppen
		* Felder - benutzerdefinierte Felder (systemabhängig, siehe [Felder](#fields))
* Statistiken
	* Abonnenten-Statistiken zum Öffnungs-, Klickverhalten. Gesamthaft und pro Newsletter angewendet
* Deaktivieren - Abonnenten deaktivieren (erhält keine Newsletter mehr, bleibt aber im System erhalten)
* Entfernen - Abonnenten unwiderrruflich aus dem System entfernen

###Verfügbare Tags
*Es stehen sogenannte Tags zur Verfügung die es ermöglichen Informationen über den Abonnenten (z.B. Name, Adresse) im Newsletter zu hinterlegen und dadurch den Newsletter zu personalisieren*

**Auszug der verfügbaren Tags:**

	Email [[+campaigner.email]]
	Firstname [[+campaigner.firstname]]
	Lastname [[+campaigner.lastname]]
	Address [[+campaigner.address]]
	Salutation [[+campaigner.salutation]]
	Unsubscribe [[+campaigner.unsubscribe]]
	Is Text [[+campaigner.istext]]
	Key [[+campaigner.key]]
	Tracking image [[+campaigner.tracking_image]]

**zusätzlich können Abonnenten mit benutzerdefinierten Feldern versehen werden (siehe [Felder](#fields))**


## [Bouncing](id:bounces)

*Bounces sind vom Empfänger oder Empfänger-Server abgelehnte Mails. Hier können diese aufgelistet und nötfalls manuell bearbeitet werden.*

### Handhabung

### Funktionen

* Bounces abholen - Analysiert das Postfach der Reply-To (Antwortadresse) auf vorhandene Bounce-Emails und erstellt anhand der darin gefundenen Codes entweder Hardbounce- oder Softbounce-Elemente.
* Tabs:
	* Hardbounces sind Antworten von Mailservern wenn z.B. ein Mail-Account nicht gefunden wird.
	* Softbounces sind Antworten von Mailservern wenn z.B. ein Mail-Account voll ist.
	* Resends sind Elemente an sich ein Softbounce sind und deren Versand erneut versucht wird.
	* Weitere Informationen: [siehe Bounces@Wikipedia](http://de.wikipedia.org/wiki/Bounce_Message)

## [Warteschlange](id:queue)

![queue]

*In der Warteschlange sind alle jemals erfassten Mails erfasst. Je nach Filter können Elemente der unterschiedlichen Stati angezeigt als auch weiterverarbeitet werden*

### Handhabung


### Funtionen
* Aktionen
	* Warteschlange abarbeiten - Alle in der Warteschlange befindlichen **unversendeten** Elemente werden sofort versendet
	* Tests entfernen - Alle als Test markierte Elemente werden entfernt
	* Log-Window anzeigen - Eine MODx-Console gibt in Echtzeit den Mail-Versand wieder. (in Entwicklung, 2013-10-29)
* Stapel-Aktionen
	* Markierte entfernen - Alle derzeit angehakten Elemente werden unwiderruflich entfernt
	* Markierte senden - Alle derzeit angehakten Elemente werden sofort versendet
	* Status bearbeiten - Bei allen derzeit angehakten Elemente wird der Status auf die darauf folgende Auswahl verändert
* Filter
	* 0 - Unversendete Nachrichten
	* 1 - Erfolgreich versendete Nachrichten
	* 3 - Zum Versand markiert (diese werden gerade versendet)
	* 5 - Abgebrochene Elemente
	* 6 - Fehlerhafte Elemente (Spalte *Fehler* von Relevanz)
	* 8 - Zum Neuversand vorgemerkt

## [Statistiken](id:statistics)

![stats]

*Statistken bieten eine Darstellung von gesammelten Daten zum Userverhalten.*

** Gesammelt werden **

* Newsletter-Öffnungen (abhängig vom verwendeten Mail-Client [^2] bzw. Einstellungen)
* Klicks
	* normaler Link,
	* Facebook, Twitter oder andere soziale Netzwerke, die im [Social Sharing](#social-sharing) hinterlegt sind,
* Bounces
* Abmeldungen (abhängig davon ob der verwendete Client Bilder anzeigt)

[^2]: z.B. Outlook, Thunderbird, GMail, Mail (OS X), ...

### Handhabung

Um die Statistiken der Newsletter einzusehen navigiert man in der Komponente zu dem Tab *Statisitken*. Durch die rechte Aktionstaste der Maus/Trackpad können die Statistik-Details eingesehen werden.

### Funktionen

* Statistik-Details - Erweiterte Detailansicht zu den Statistiken eines Newsletters
* Statistiken exportieren - CSV-Export der Statistiken

#### Statistik-Details

Die Statistiken eines Newsletters sind in 4 Tabs aufgeteilt:

* Geöffnet - Wieviele Newsletter wurden geöffnet (Voraussetzung ist der Erhalt von HTML-Mails als auch die Darstellung von Bildern im Mail-Client. Durch ein 1x1 Pixel grosses Bild wird ein Öffnen der Mail eruiert)
* Klicks - Jeder Link (exkl. Abmeldung) wird für jeden Abonnnenten spezifisch erfasst
	* *Soziale Netzwerke werden extra erfasst*
* Bounces - Daten über die Bounce-Rate des versendeten Newsletters
* Abmeldungen - Anzahl und Auflistung der über diesen Newsletter generierten Abmeldungen

## [Social-Sharing](id:social-sharing)
*Social-Sharing bietet die Möglichkeit einerseits Share-Links für einzelne Artikel zu implementieren, andererseits z.B. auf die eigene Seite in einem "Sozialen Netzwerk" hinzuweisen. In der Dokumentation findet sich ein Beispiel beider Implementationen.*

### Handhabung & Implementierung
Es steht das Snippet *CampaignerSharing* zur Verfügung welches es ermöglicht alle aktiven Social-Sharing-Elemente einfach einzubinden.

**Einbindung des Snippets:**

	[[!CampaignerSharing? &url=`[[~[[+id]]? scheme=`full`]]`]]

### Funktionen
* Erstellen/Bearbeiten - siehe [Screen](#screen_socialsharing)
	* Name - Beschreibender Name des Elements
	* URL - Der URI wobei mit dem Platzhalter [[+url]], der übergebene Link mitgegeben wird (z.B.: http://www.facebook.com/share.php?u=[[+url]])
	* Icon - Das Icon des Dienstes
	* Linktext - Der anzuzeigende Linktext
	* Suchmuster - Dient zur Erfassung in den [Statistiken](#statistics) um Links auf soziale Netzwerke identifizieren zu können
	* Aktiv - Status des Elements (inaktive werden nicht implementiert bzw stehen nicht zur Verfügung)
* Entfernen - Löschen des Elements
* Aktivieren/Deaktivieren (nicht implementiert)

## [Felder](id:fields)

![fields]

*Felder dienen dazu Daten von Abonnenten noch spezifischer erfassen zu können und nach den projektbezogenen Bedürfnissen zu erweitern.*

### Handhabung

Um ein Feld hinzuzufügen oder die bestehenden zu bearbeiten navigiert man in der Komponente zu dem Tab **Felder**. Über die Schaltfläche *Neues Element* lässt sich ein neues Feld hinzufügen. Um bereits bestehende Felder zu bearbeiten wird die rechte Aktionstaste der Maus/Trackpad verwendet.

### Funktionen

* Erstellen/Bearbeiten - siehe [Screen](#screen_fieldswindow)
	* Name - Schlüssel des Feldes, wird zur Identifizierung des Feldes benötigt und muss eindeutig sein
	* Label - Der Titel des Feldes
	* Typ - Ein ExtJS XType; Folgende Typen werden derzeit (2013-10-29) unterstützt:
		* textfield
		* modx-combo
		* textarea
		* datefield
	* Werte - derzeit nur bei *modx-combo* möglich
	* Format - derzeit nur bei *datefield* möglich
	* Benötigt - Ist dieses Feld unbedingt auszufüllen?
	* Aktiv - Ist dieses Feld aktiv?
	* Tab - In welchem Tab des Abonnenten-Fenster soll dieses Feld angezeigt werden? Derzeit verfügbar (2013-10-29):
		* campaigner-subscriber-tab-main
		* campaigner-subscriber-tab-address
		* campaigner-subscriber-tab-groups
		* campaigner-subscriber-tab-fields
	* Positionierung - An welcher Position soll dieses Feld angezeigt werden? (Index-Werte beginnend mit 0, 1, 2, ...)
* Entfernen - Löschen des Elements
* Drag 'n' Drop - Felder können via Drag 'n' Drop in ihrere Positionierung verändert werden. Diese Positionierung betrifft aber nur Felder die im Feld-Tab (*campaigner-subscriber-tab-fields*) angezeigt werden!


## [Rechte-Management](id:permissions)

![perms]

## [Systemeinstellungen](id:settings)

![settings]

## [Weitere Screenshots](id:screens)
### [Social-Sharing](id:screen_socialsharing)
![sharing]

### [Abonnenten bearbeiten](id:screen_subscriber_edit)
![subscriber_edit]

### [Gruppe bearbeiten](id:screen_group_edit)
![group_edit]

### [Felder bearbeiten](id:screen_fields_edit)
![field_edit]

### [Test-Versand](id:screen_sendtest)
![send_test]

[tabs]: https://googledrive.com/host/0ByBGzCLq1WRAZThncExuWmR0WTA/campaigner_tabs.png
[subscriber]: https://googledrive.com/host/0ByBGzCLq1WRAZThncExuWmR0WTA/campaigner_subscriber.png
[queue]: https://googledrive.com/host/0ByBGzCLq1WRAZThncExuWmR0WTA/campaigner_queue.png
[groups]: https://googledrive.com/host/0ByBGzCLq1WRAZThncExuWmR0WTA/campaigner_groups.png
[stats]: https://googledrive.com/host/0ByBGzCLq1WRAZThncExuWmR0WTA/campaigner_stats.png
[stats_details]: https://googledrive.com/host/0ByBGzCLq1WRAZThncExuWmR0WTA/campaigner_stats_detail.png
[perms]: https://googledrive.com/host/0ByBGzCLq1WRAZThncExuWmR0WTA/campaigner_permissions_combined.png
[settings]: https://googledrive.com/host/0ByBGzCLq1WRAZThncExuWmR0WTA/campaigner_settings_combined.png
[sharing]: https://googledrive.com/host/0ByBGzCLq1WRAZThncExuWmR0WTA/campaigner_socialsharing_edit.png
[subscriber_edit]: https://googledrive.com/host/0ByBGzCLq1WRAZThncExuWmR0WTA/campaigner_subscriber_edit.png
[group_edit]: https://googledrive.com/host/0ByBGzCLq1WRAZThncExuWmR0WTA/campaigner_group_edit.png
[fields]: https://googledrive.com/host/0ByBGzCLq1WRAZThncExuWmR0WTA/campaigner_fields.png
[field_edit]: https://googledrive.com/host/0ByBGzCLq1WRAZThncExuWmR0WTA/campaigner_field_edit.png
[send_test]: https://googledrive.com/host/0ByBGzCLq1WRAZThncExuWmR0WTA/campaigner_sendtest.png
[sql_queue]: https://googledrive.com/host/0ByBGzCLq1WRAZThncExuWmR0WTA/campaigner_sql_queue.png