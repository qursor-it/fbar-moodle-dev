# Moodle SPID Authentication plugin

Questo plugin fornisce un'integrazione SPID per Moodle basata su `auth_saml2`. Gestisce configurazione, metadata e certificati specifici SPID, lasciando al plugin `auth_saml2` la gestione del flusso SAML.

## Dipendenze
- Plugin [auth_saml2](https://moodle.org/plugins/auth_saml2) installato nella stessa piattaforma.
- Libreria PHP [`italia/spid-cie-php`](https://github.com/italia/spid-cie-php) installata tramite Composer locale al plugin (`composer install` da `auth/spid`).

## Installazione
1. Copia la cartella `auth/spid` nella directory `auth/` del tuo Moodle.
2. Installa il plugin da interfaccia web di Moodle o eseguendo l'upgrade CLI.
3. Da shell, posizionati in `auth/spid` ed esegui `composer install` per scaricare le dipendenze.
4. Configura il plugin da `Amministrazione del sito > Plugin > Autenticazione > SPID`.

## Configurazione SPID
- Definisci EntityID, ACS e Single Logout URL.
- Carica o genera i certificati tramite l'interfaccia (puoi incollare PEM o usare la CLI per generarli).
- Esegui `php auth/spid/cli/regenerate_metadata.php` per rigenerare i metadata da fornire all'IdP (salvati anche in `moodledata/spidcerts/metadata.xml`).
- Verifica il task pianificato `auth_spid\task\metadata_refresh_task` per la rigenerazione periodica.

## Supporto
Consulta la documentazione in `docs/spid_auth/` per note operative e flussi di test.
