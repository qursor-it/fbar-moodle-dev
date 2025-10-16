<?php
// Questo file fa parte di Moodle - http://moodle.org/
//
// Moodle e software libero: puoi redistribuirlo e/o modificarlo
// secondo i termini della GNU General Public License come pubblicata dalla
// Free Software Foundation, versione 3 o successive.
//
// Moodle e distribuito nella speranza che sia utile,
// ma SENZA ALCUNA GARANZIA; senza neppure la garanzia implicita di
// COMMERCIABILITA o IDONEITA A UNO SCOPO PARTICOLARE.
// Vedi la GNU General Public License per maggiori dettagli.
//
// Dovresti aver ricevuto una copia della GNU General Public License
// insieme a Moodle. In caso contrario, vedi <http://www.gnu.org/licenses/>.

/**
 * Stringhe in italiano.
 *
 * @package     auth_spid
 * @copyright   2025 Finzioni
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 o successive
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Autenticazione SPID';
$string['auth_spiddescription'] = 'Fornisce autenticazione SPID sfruttando il plugin auth_saml2 e configurazioni dedicate.';
$string['settings:generalheading'] = 'Configurazione SPID';
$string['settings:entityid'] = 'Entity ID';
$string['settings:entityid_help'] = 'Identificativo del Service Provider registrato in SPID.';
$string['settings:acs'] = 'URL Assertion Consumer Service';
$string['settings:slo'] = 'URL Single Logout';
$string['settings:metadata'] = 'Endpoint metadata';
$string['settings:certduration'] = 'Validita certificato (giorni)';
$string['settings:certpath'] = 'Percorso archivio certificati';
$string['settings:attrmap'] = 'Mapping attributi (JSON)';
$string['settings:attrmap_help'] = 'Oggetto JSON che associa gli attributi SPID (es. "spidCode") ai campi utente Moodle (es. "idnumber").';
$string['settings:autoupdate'] = 'Rigenera automaticamente i metadata';
$string['settings:autoupdate_desc'] = 'Se abilitato, un task pianificato rigenera periodicamente i metadata.';
$string['settings:buttonlabel'] = 'Etichetta pulsante login';
$string['settings:debug'] = 'Abilita log diagnostici SPID';
$string['settings:certificate'] = 'Certificato SPID (PEM)';
$string['settings:certificate_help'] = 'Incolla il certificato X.509 fornito per il Service Provider SPID in formato PEM.';
$string['settings:privatekey'] = 'Chiave privata SPID (PEM)';
$string['settings:privatekey_help'] = 'Incolla la chiave privata associata al certificato SPID in formato PEM. Conserva con cura.';
$string['settings:metadatadownload'] = 'Scarica metadata';
$string['settings:metadatadownload_desc'] = 'Usa questo link per scaricare i metadata SPID esposti dal plugin.';
$string['event:loginstarted'] = 'Avvio login SPID';
$string['event:loginfailed'] = 'Login SPID fallito';
$string['event:loginsuccess'] = 'Login SPID completato';
$string['privacy:metadata'] = 'Il plugin di autenticazione SPID conserva configurazioni e metadata necessari per dialogare con la federazione SPID.';
$string['error:missingdependency'] = 'Il plugin auth_saml2 non risulta installato o attivo. Installalo e abilitalo prima di usare SPID.';
$string['error:missingvendor'] = 'La libreria PHP SPID non e disponibile. Esegui composer install nella cartella auth/spid.';
$string['cli:regenerated'] = 'Metadata SPID rigenerati in {$a}.';
$string['cli:regenerateerror'] = 'Impossibile rigenerare i metadata SPID: {$a}';
$string['task:metadatarefresh'] = 'Rigenera metadata SPID';
$string['button:login'] = 'Entra con SPID';
$string['metadata:signaturemissing'] = 'Generazione metadata fallita: certificato o chiave privata assenti.';
$string['metadata:generated'] = 'Metadata SPID generati correttamente.';
$string['metadata:regenerated'] = 'Metadata rigenerati e memorizzati.';
