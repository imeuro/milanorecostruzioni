# MRC - Plugin per Milano RE Costruzioni

Plugin personalizzato per il sito web di Milano RE Costruzioni. Gestisce Custom Post Types, funzionalità specifiche e moduli aggiuntivi.

## Struttura del Plugin

```
mrc/
├── mrc.php                    # File principale del plugin
├── README.md                  # Documentazione
├── modules/                   # Moduli funzionali
│   ├── cpt/                   # Custom Post Types
│   │   └── class-cpt-manager.php
│   └── pages/                 # Gestione pagine statiche
│       └── class-pages-manager.php
└── languages/                 # File di traduzione (futuro)
```

## Moduli Disponibili

### CPT (Custom Post Types)
Gestisce tutti i Custom Post Types del sito:

- **Lavori**: Post type per il portfolio dei lavori svolti
  - Slug: `lavori`
  - Supporta: titolo, contenuto, immagine in evidenza, estratto
  - Non visibile in REST API
  - Icona: portfolio

### Pages (Pagine Statiche)
Crea automaticamente le pagine principali del sito:

- **Home**: Pagina principale con presentazione aziendale
- **Attività**: Presentazione dei servizi offerti
- **Portfolio Lavori**: Pagina che mostra l'archivio dei lavori
- **Contatti**: Informazioni di contatto

## Installazione

1. Carica la cartella `mrc` nella directory `/wp-content/plugins/`
2. Attiva il plugin dal pannello di amministrazione WordPress
3. Le pagine statiche verranno create automaticamente al primo accesso all'admin
4. I Custom Post Types saranno automaticamente disponibili

## Utilizzo

### Pagine Statiche

Dopo l'attivazione del plugin, le seguenti pagine verranno create automaticamente:

- **Home** (`/home/`): Pagina principale del sito
- **Attività** (`/attivita/`): Presentazione dei servizi
- **Portfolio Lavori** (`/portfolio-lavori/`): Archivio lavori
- **Contatti** (`/contatti/`): Informazioni di contatto

La pagina Home verrà automaticamente impostata come pagina principale del sito.

### Custom Post Type "Lavori"

Dopo l'attivazione del plugin, troverai nel menu di amministrazione la voce "Lavori" dove potrai:

- Creare nuovi lavori
- Aggiungere immagini in evidenza
- Scrivere descrizioni dettagliate
- Gestire l'archivio dei lavori

### Menu di Navigazione

Per creare il menu di navigazione:

1. Vai su **Aspetto > Menu**
2. Crea un nuovo menu chiamato "Menu Principale"
3. Aggiungi le pagine:
   - Home
   - Attività
   - Portfolio lavori (link all'archivio `/lavori/`)
   - Contatti
4. Assegna il menu alla posizione "Menu Principale"

## Sviluppo

### Aggiungere Nuovi Moduli

Per aggiungere un nuovo modulo:

1. Crea una nuova cartella in `modules/`
2. Crea la classe manager seguendo la convenzione `class-{modulo}-manager.php`
3. Aggiungi il caricamento del modulo in `mrc.php`

### Estendere i Custom Post Types

Per aggiungere nuovi CPT, modifica `modules/cpt/class-cpt-manager.php` e aggiungi nuovi metodi di registrazione.

### Modificare le Pagine Statiche

Le pagine vengono create automaticamente al primo avvio. Per ricrearle:

1. Elimina l'opzione `mrc_pages_created` dal database
2. Ricarica la pagina di amministrazione

## Compatibilità

- WordPress 5.0+
- PHP 7.4+
- Tema MRC25

## Licenza

GPL v2 o successiva

## Changelog

### Versione 1.0.0
- Rilascio iniziale
- Custom Post Type "Lavori"
- Pagine statiche automatiche (Home, Attività, Portfolio, Contatti)
- Struttura modulare per future estensioni 