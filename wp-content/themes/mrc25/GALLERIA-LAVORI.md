# Guida all'utilizzo delle funzioni galleria lavori

## Problema risolto

Le funzioni `mrc_display_gallery_grid` e `mrc_display_gallery_lightbox` non funzionavano perché non erano disponibili nel template del tema.

## Soluzione implementata

### 1. Inclusione delle funzioni
Ho aggiunto nel file `template-parts/content-lavori.php` alle **righe 9-10**:
```php
require_once WP_CONTENT_DIR . '/plugins/mrc/modules/lavori/mrc25-template.php';
require_once WP_CONTENT_DIR . '/plugins/mrc/modules/lavori/mrc25-carousel.php';
```

### 2. Stili CSS aggiunti
Ho aggiunto gli stili CSS nel file `style.css`:
- **Righe 1138-1343**: Griglia responsive e lightbox
- **Righe 1345-1526**: Carousel con animazioni
- Media queries per mobile-first design

### 3. JavaScript per lightbox e carousel
Ho creato i file JavaScript:
- **`assets/js/gallery-lightbox.js`**: Gestione lightbox con navigazione e chiusura
- **`assets/js/carousel.js`**: Gestione carousel con auto-play e controlli

### 4. Inclusione JavaScript
Ho aggiunto nel file `functions.php` alle **righe 96-97**:
```php
wp_enqueue_script('mrc25-gallery-lightbox', get_template_directory_uri() . '/assets/js/gallery-lightbox.js', array(), '1.0.0', true);
wp_enqueue_script('mrc25-carousel', get_template_directory_uri() . '/assets/js/carousel.js', array(), '1.0.0', true);
```

## Come utilizzare le funzioni

### Carousel
Per utilizzare il carousel, decommentare la **riga 33** in `content-lavori.php`:
```php
mrc_display_lavoro_with_carousel(get_the_ID());
```

### Griglia semplice
Per utilizzare la griglia, decommentare la **riga 36** in `content-lavori.php`:
```php
mrc_display_gallery_grid($images);
```

### Lightbox
Per utilizzare il lightbox, decommentare la **riga 39** in `content-lavori.php`:
```php
mrc_display_gallery_lightbox($images);
```

## Funzionalità disponibili

### Carousel
- **Auto-play**: Cambio automatico ogni 5 secondi
- **Controlli**: Pulsanti precedente/successiva e dots
- **Navigazione**: Frecce tastiera e click sui dots
- **Pausa**: Si ferma al hover o quando la finestra non è attiva
- **Click per lightbox**: Click sull'immagine apre il lightbox
- **Responsive**: Altezze diverse per mobile, tablet e desktop

### Lightbox
- **Click sui thumbnails**: Apre il lightbox
- **Navigazione**: Frecce sinistra/destra o tasti freccia
- **Chiusura**: Pulsante X, tasto ESC, o click fuori dall'immagine
- **Caption**: Mostra le didascalie delle immagini se disponibili
- **Responsive**: Funziona su tutti i dispositivi

## Funzionalità combinata Carousel + Lightbox

Quando entrambe le funzioni sono attive (come nel template attuale):
- Il **carousel** mostra le immagini in sequenza con auto-play
- I **thumbnails del lightbox** sono nascosti automaticamente
- **Click su qualsiasi immagine del carousel** apre il lightbox con quella specifica immagine
- Il lightbox mantiene tutte le sue funzionalità (navigazione, chiusura, caption)
- L'esperienza utente è fluida: carousel per la visualizzazione, lightbox per i dettagli

## Stili CSS inclusi

- **Mobile-first**: Design responsive che parte da mobile
- **Animazioni**: Hover effects e transizioni fluide
- **Accessibilità**: Controlli keyboard e aria-labels
- **Performance**: Lazy loading per le immagini

## Note tecniche

- Le funzioni sono ora disponibili in tutti i template del tema
- Il JavaScript si carica automaticamente su tutte le pagine
- Gli stili sono ottimizzati per le Core Web Vitals
- Compatibile con tutti i browser moderni
