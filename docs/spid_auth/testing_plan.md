# Piano di test SPID

## Prerequisiti
- Plugin `auth_saml2` installato e configurato.
- Libreria `italia/spid-cie-php` installata in `auth/spid/vendor`.
- Certificato SPID valido o generato tramite CLI del plugin.

## Scenari manuali
1. **Validazione metadata**
   - Comando: `php auth/spid/cli/regenerate_metadata.php`
   - Caricare `auth/spid/metadata.php` nel validatore AgID e verificare firma e attributi.
2. **Login utente nuovo**
   - Accedere alla pagina login Moodle e usare pulsante "Entra con SPID".
   - Completare autenticazione su IdP demo; verificare creazione utente con mapping attributi (email, nome, cognome, idnumber).
3. **Login utente esistente**
   - Collegare un account Moodle a SPID (stesso codice fiscale) e ripetere il login per verificare aggiornamento campi.
4. **Gestione errori IdP**
   - Simulare IdP non raggiungibile verificando che Moodle mostri notifica e permetta altri metodi di login.
5. **Rigenerazione programmata metadata**
   - Eseguire `php admin/cli/scheduled_task.php --execute=\auth_spid\task\metadata_refresh_task` e controllare log.

## Compatibilita
- Verificare che plugin manuale (`auth_manual`) resti attivo.
- Controllare ordine plugin: SPID deve essere abilitato ma non bloccare altri metodi.

## Note
- In ambienti offline utilizzare certificati di test generati dalla CLI.
- Annotare eventuali errori in `docs/spid_auth/step_memory.md`.
