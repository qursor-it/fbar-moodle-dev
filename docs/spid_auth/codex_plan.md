# Piano Operativo Codex - Integrazione SPID in Moodle

Obiettivo generale: integrare SPID come nuovo sistema di autenticazione in Moodle, mantenendo allineamento con le linee guida AgID e con l'ecosistema dei plugin core.

## Artefatti di supporto
- `docs/spid_auth/step_memory.md`: diario da creare nel **Passo 0** e aggiornare a fine di ogni passo con tre sezioni obbligatorie (`Sintesi`, `Problemi aperti`, `Prossimo passo`). Ogni aggiornamento va aggiunto in cima usando data ISO e nome del passo completato.
- `docs/spid_auth/checklist.md`: checklist opzionale per tracciare micro-attivita; tienila allineata con la memoria.
- `docs/spid_auth/log_operativo.md` (opzionale): log puntuale delle attivita svolte per facilitare la review finale da parte dell'utente.

Prima di ogni passo operativo:
1. Leggi l'ultima voce in `step_memory.md` per riallinearti.
2. Annota nella memoria una sezione "Obiettivo del passo" con cio che vuoi ottenere.

## Passo 0 - Allestimento note e contesto
- Crea `docs/spid_auth/step_memory.md` con intestazione e tre sottosezioni fisse.
- Aggiungi la prima voce (`Sintesi` = "Bootstrap note fatto"; `Problemi aperti` = elenco aspetti da indagare; `Prossimo passo` = riferimento al Passo 1).
- (Facoltativo) Crea `docs/spid_auth/checklist.md` con elenco ancora vuoto o macro-voci da riempire piu avanti.
- **Memoria**: registra creazione file, dubbi iniziali e collegamento al Passo 1.

## Passo 1 - Analisi plugin esistente e dipendenze
- Esamina `auth/saml2` (struttura file, `auth.php`, `settings.php`, cartelle `classes/` e `db/`).
- Identifica punti di estensione necessari (`auth_plugin_saml2`, hook cron, script CLI).
- Mappa configurazioni rilevanti (metadata URL, lista IdP, gestione certificati) e annota quali erediterai o sostituirai per SPID.
- Rivedi il pacchetto [`italia/spid-cie-php`](https://github.com/italia/spid-cie-php): uso come Service Provider, gestione certificati, generazione metadata, endpoint richiesti.
- Valuta impatto su Composer (versioni PHP/Moodle) e su pipeline di deploy.
- **Memoria**: sintetizza punti chiave, link ai file da rivedere, TODO per Passo 2.

## Passo 2 - Definizione architettura plugin `auth/spid`
- Disegna struttura cartelle (esempio: `auth/spid/auth.php`, `classes/metadata/generator.php`, `settings.php`, `lang/it/auth_spid.php`, script di callback).
- Decidi come estendere `auth_saml2\auth` o riutilizzarne i servizi.
- Definisci configurazioni nuove (certificati SPID, entityID, mapping attributi SPID -> Moodle) e come esporle in admin UI.
- Pianifica un endpoint pubblico per metadata SPID (`metadata.php`) e servizi di aggiornamento (cron o script manuali).
- Redigi checklist file-by-file e riportala in `checklist.md`.
- **Memoria**: riassumi decisioni architetturali, elenco file da creare, dipendenze per Passo 3.

## Passo 3 - Preparazione Composer e librerie
- Scegli se usare Composer locale al plugin (consigliato) o il composer root; documenta pro e contro.
- Definisci autoload PSR-4 per il namespace del plugin (`auth_spid\` o simile) e includi il vendor autoload nel bootstrap.
- Installa `italia/spid-cie-php` con versione minima compatibile e verifica il lockfile.
- Documenta requisiti per il deploy (comandi `composer install`, gestione della directory `vendor`).
- **Memoria**: registra comandi eseguiti, versioni librerie, problemi di ambiente; imposta `Prossimo passo` al Passo 4.

## Passo 4 - Implementazione autenticazione principale
- Implementa la classe primaria (`auth.php` o `classes/auth.php`) estendendo `auth_saml2\auth`.
- Configura il provider SPID con la libreria: endpoints, ACS, SLO, attribute mapping.
- Gestisci il flusso di login (inizializzazione, validazione assertion, creazione/aggiornamento utente Moodle, fallback).
- Aggiungi gestione di errori e logging secondo gli standard Moodle (`\core\notification`, `mtrace`, `debugging`).
- Integra il salvataggio delle impostazioni (`get_config`, `set_config`).
- **Memoria**: indica file modificati, stato test parziali, edge case da coprire nel Passo 5.

## Passo 5 - Metadata, certificati e configurazione admin
- Crea endpoint `metadata.php` che esponga metadata SPID firmati usando gli helper della libreria.
- Implementa gestione certificati (upload, rotazione) nell'interfaccia admin (`settings.php` o form dedicato).
- Aggiorna stringhe lingua (`lang/en` e `lang/it`) con istruzioni per gli amministratori.
- Aggiungi script CLI o task programmato per rigenerare metadata/certificati se necessario.
- Valuta la creazione di file di configurazione demo (es. metadata di esempio, certificato placeholder) per supportare test e review.
- **Memoria**: annota processo di generazione metadata, file sensibili e TODO per i test.

## Passo 6 - Test funzionali e conformita
- Configura ambiente di test SPID (validator AgID) e descrivi procedura per caricare i metadata.
- Esegui test end-to-end di login (utente nuovo, utente esistente, errori IdP).
- Verifica regressioni sugli altri metodi di login (ordine plugin, fallback manuale).
- Registra in memoria risultati test, bug trovati, piani di correzione (Passo 7 se necessario).

## Passo 7 - Documentazione e consegna
- Scrivi README del plugin (`auth/spid/README.md`) con setup, configurazioni, troubleshooting.
- Aggiorna documentazione interna (`docs/spid_auth/`) con dettagli finali.
- Prepara checklist di verifica per il deploy (certificati, cron, backup).
- Valuta script di uninstall o sanity check.
- Includi, se possibile, file demo e scenari di test automatizzati/manuali documentati per consentire feedback rapidi in fase di review.
- **Memoria**: chiudi con sintesi finale, stato generale, attivita future.

---

### Note operative generali
- Prima di modificare codice: consulta `step_memory.md` per stato attuale e obiettivi.
- Dopo ogni passo: aggiorna `step_memory.md` e, quando utile, `checklist.md`; non iniziare il passo successivo senza aver validato i punti del passo corrente.
- Mantieni i commit Git correlati ai passi (uno o piu commit per passo con messaggi chiari).
- Non cancellare informazioni nella memoria: aggiungi nuove sezioni in cima per preservare lo storico.
- In caso di deviazioni importanti dal piano, documentale nella memoria e aggiorna questa guida aggiungendo un'appendice con le variazioni approvate.
- Documenta ogni attivita completata (codice, configurazioni, test) per agevolare la review dell'utente, referenziando file e commit rilevanti.
