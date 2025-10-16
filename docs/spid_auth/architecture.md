# Architettura proposta plugin `auth/spid`

## Obiettivi
- Fornire un layer di configurazione e servizi specifici SPID sopra il plugin `auth_saml2`.
- Isolare tutte le personalizzazioni SPID in un nuovo plugin mantenendo Moodle core intatto.
- Permettere la generazione dei metadata SPID e la gestione dei certificati senza modificare `auth_saml2`.

## Struttura directory
```
auth/spid/
├── auth.php
├── classes/
│   ├── api/
│   │   └── spid_service.php
│   ├── certificates/
│   │   ├── generator.php
│   │   └── repository.php
│   ├── metadata/
│   │   ├── builder.php
│   │   └── signer.php
│   ├── task/
│   │   └── metadata_refresh_task.php
│   └── util/
│       └── config.php
├── cli/
│   └── regenerate_metadata.php
├── db/
│   └── tasks.php
├── lang/
│   ├── en/auth_spid.php
│   └── it/auth_spid.php
├── metadata.php
├── settings.php
├── version.php
├── composer.json
├── README.md
└── vendor/ (generato da composer, ignorato in git)
```

## Dipendenze
- `auth_saml2` installato a livello di Moodle (manuale o tramite tool esterno) per fornire le funzionalita SAML.
- Libreria `italia/spid-cie-php` gestita tramite `composer` locale al plugin. Il caricamento avverra tramite `vendor/autoload.php` condizionale.

## Flusso di autenticazione
1. Gli utenti scelgono SPID nella pagina di login Moodle.
2. `auth/spid/auth.php` estende `\auth_saml2\auth` configurando provider SPID al bootstrap.
3. Durante `user_login`, se le dipendenze sono soddisfatte, viene inizializzato il client SPID usando `spid_service`.
4. Gli attributi restituiti vengono mappati verso i campi Moodle tramite configurazione (`settings.php`).
5. La creazione/aggiornamento utente viene delegata al metodo base di `auth_saml2` quando possibile; in alternativa, viene gestita in locale.

## Metadata e certificati
- `metadata.php` esporta metadata SPID firmati.
- `metadata_refresh_task` rigenera i metadata periodicamente usando `metadata\builder` e `certificates\generator`.
- I certificati sono conservati in `moodledata/spidcerts` (configurabile) tramite `certificates\repository`.

## Configurazioni amministrative
- Pagina `settings.php` espone: EntityID, Endpoint ACS/SLO, durata certificati, attributi SPID -> Moodle, gestione utenti.
- Prevede azione manuale per rigenerare metadata e per caricare certificati esistenti.

## Strategie di fallback
- Se `auth_saml2` o la libreria SPID non sono disponibili, il plugin mostra notifiche e disabilita il login, mantenendo accessibile il login manuale.
- I comandi CLI verificano la presenza delle dipendenze prima di operare.

## Test e conformita
- Scenario manuale per validare metadata via tool AgID.
- Test CLI per rigenerazione metadata.
- Verifica di coesistenza con altri plugin di autenticazione.
