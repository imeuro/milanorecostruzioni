# MRC25 - Tema WordPress per Milano RE Costruzioni

Un tema WordPress personalizzato basato su Underscores, progettato specificamente per Milano RE Costruzioni.

## Caratteristiche

- **Design Responsive**: Layout mobile-first che si adatta a tutti i dispositivi
- **Accessibilità**: Conforme agli standard WCAG per l'accessibilità web
- **Performance**: Ottimizzato per Core Web Vitals (CLS, LCP, INP)
- **Personalizzabile**: Supporto per Customizer con opzioni di personalizzazione
- **SEO Friendly**: Struttura HTML semantica e meta tag ottimizzati
- **Multilingua**: Pronto per la traduzione con text domain 'mrc25'

## Struttura del Tema

```
mrc25/
├── style.css                 # Stili principali del tema
├── functions.php             # Funzionalità e setup del tema
├── index.php                 # Template principale
├── header.php                # Header del sito
├── footer.php                # Footer del sito
├── sidebar.php               # Sidebar
├── single.php                # Template per singoli post
├── page.php                  # Template per pagine
├── archive.php               # Template per archivi
├── search.php                # Template per ricerche
├── 404.php                   # Pagina di errore 404
├── inc/                      # File di supporto
│   ├── template-tags.php     # Funzioni helper per template
│   ├── template-functions.php # Funzioni di supporto
│   ├── customizer.php        # Opzioni Customizer
│   └── jetpack.php           # Compatibilità Jetpack
├── template-parts/           # Parti di template riutilizzabili
│   ├── content.php           # Template per post
│   ├── content-page.php      # Template per pagine
│   ├── content-single.php    # Template per singoli post
│   ├── content-search.php    # Template per risultati ricerca
│   └── content-none.php      # Template per nessun contenuto
└── js/                       # File JavaScript
    ├── navigation.js         # Gestione menu mobile
    └── customizer.js         # Funzionalità Customizer
```

## Funzionalità

### Menu di Navigazione
- Menu principale responsive con toggle mobile
- Menu footer
- Supporto per menu personalizzati

### Widget Areas
- Sidebar principale
- Area widget footer

### Personalizzazione
- Colore primario personalizzabile
- Testo footer personalizzabile
- Logo personalizzabile
- Sfondo personalizzabile

### Immagini
- Supporto per featured images
- Dimensioni immagine personalizzate:
  - `mrc25-featured`: 800x400px
  - `mrc25-thumbnail`: 300x200px

### Compatibilità
- WordPress 5.0+
- PHP 7.4+
- Browser moderni (IE11+)

## Installazione

1. Carica la cartella `mrc25` nella directory `/wp-content/themes/`
2. Attiva il tema dal pannello di amministrazione WordPress
3. Personalizza il tema tramite Aspetto > Personalizza

## Personalizzazione

### Colori
Il tema utilizza CSS Custom Properties per i colori principali:
- `--primary-color`: #0073aa
- `--secondary-color`: #005a87
- `--text-color`: #333
- `--light-gray`: #f9f9f9
- `--border-color`: #e1e1e1

### Breakpoint Responsive
- Mobile: < 768px
- Tablet: 768px - 1023px
- Desktop: 1024px - 1199px
- Large Desktop: ≥ 1200px

## Sviluppo

### Aggiungere Stili Personalizzati
Modifica il file `style.css` o crea un child theme per personalizzazioni avanzate.

### Aggiungere Funzionalità
Utilizza il file `functions.php` per aggiungere nuove funzionalità o hook personalizzati.

### Template Personalizzati
Crea nuovi template nella cartella `template-parts/` per layout specifici.

## Supporto

Per supporto tecnico o domande, contatta il team di sviluppo.

## Licenza

GPL v2 o successiva - vedi [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html)

## Changelog

### Versione 1.0.0
- Rilascio iniziale del tema
- Layout responsive mobile-first
- Supporto per Customizer
- Compatibilità con Jetpack
- Struttura HTML semantica
- Ottimizzazioni per Core Web Vitals 