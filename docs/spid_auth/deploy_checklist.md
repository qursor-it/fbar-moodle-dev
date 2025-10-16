# Checklist deploy SPID

## Prima del deploy
- [ ] Installare plugin `auth_saml2` nella piattaforma Moodle.
- [ ] Copiare `auth/spid` e eseguire `composer install --no-dev` nella directory del plugin.
- [ ] Configurare EntityID, ACS, SLO e mapping attributi in `Amministrazione del sito > Plugin > Autenticazione > SPID`.
- [ ] Caricare certificato/chiave SPID o generarli con `php auth/spid/cli/regenerate_metadata.php`.
- [ ] Validare metadata con tool AgID (upload `auth/spid/metadata.php`).
- [ ] Aggiornare ordine dei plugin di autenticazione assicurando SPID attivo.

## Durante il deploy
- [ ] Eseguire `php admin/cli/upgrade.php` per completare installazione plugin.
- [ ] Eseguire `php admin/cli/scheduled_task.php --execute=\auth_spid\task\metadata_refresh_task` per generare metadata iniziali.
- [ ] Scaricare e archiviare `moodledata/spidcerts` come backup.

## Post-deploy
- [ ] Effettuare login SPID con utente di test per verificare mappature.
- [ ] Documentare nel registro di sicurezza la durata dei certificati SPID.
- [ ] Programmare monitoraggio della scadenza certificati (es. calendario).
