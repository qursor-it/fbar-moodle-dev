# Configurazione Composer plugin `auth/spid`

- Composer locale al plugin con namespace `auth_spid\\` -> `classes/`.
- Dipendenza principale `italia/spid-cie-php` (versione >= 2.1) dichiarata ma non installata per limiti di rete nel repository.
- Passi installazione:
  1. `cd auth/spid`
  2. `composer install --no-dev`
- Il file `vendor/autoload.php` viene caricato condizionalmente da `auth/spid/lib/autoload.php` (da creare nel Passo 4).
- Il file `composer.lock` viene ignorato nel repository perche l'installazione verra eseguita in ambienti deploy con accesso a rete.
- In caso di ambienti offline, predisporre un mirror locale della libreria o includere manualmente il pacchetto nel vendor.
