# Modulo Lavori - MRC Plugin

Questo modulo gestisce la personalizzazione del backend per i post di tipo "lavori" con descrizione breve e galleria immagini riordinabile.

## Funzionalità

### Backend (Admin)
- **Meta Box Descrizione**: Campo textarea per inserire una breve descrizione del lavoro (max 500 caratteri)
- **Meta Box Galleria**: Interfaccia drag & drop per upload e gestione immagini
- **Riordinamento**: Possibilità di riordinare le immagini tramite drag & drop
- **Upload multiplo**: Supporto per upload di più immagini contemporaneamente
- **Validazione**: Controllo automatico del numero massimo di immagini (20)
- **Interfaccia responsive**: Design mobile-first per l'admin

### Frontend (Helper Functions)
- Funzioni helper per recuperare descrizione e galleria
- Supporto per diverse dimensioni di immagini
- Generazione HTML per gallerie
- Query personalizzate per lavori con galleria/descrizione

## Utilizzo

### Nel Backend
1. Vai su **Lavori > Aggiungi Nuovo**
2. Compila il titolo del lavoro
3. Inserisci una breve descrizione nella meta box "Descrizione Lavoro"
4. Carica le immagini nella meta box "Galleria Immagini":
   - Clicca su "Seleziona Immagini" o trascina le immagini nell'area
   - Riordina le immagini trascinandole
   - Rimuovi immagini cliccando sull'icona cestino

### Nel Frontend

#### Funzioni Base
```php
// Ottieni la descrizione di un lavoro
$description = mrc_get_lavoro_description($post_id);

// Ottieni la galleria (array di ID)
$gallery_ids = mrc_get_lavoro_gallery($post_id);

// Ottieni le immagini con dati completi
$images = mrc_get_lavoro_gallery_images($post_id, 'full');
```

#### Verifiche
```php
// Verifica se ha galleria
if (mrc_has_lavoro_gallery($post_id)) {
    // Mostra galleria
}

// Verifica se ha descrizione
if (mrc_has_lavoro_description($post_id)) {
    // Mostra descrizione
}
```

#### Rendering HTML
```php
// Genera HTML per una singola immagine
echo mrc_render_gallery_image($image, 'my-class');

// Genera HTML per tutta la galleria
echo mrc_render_lavoro_gallery($post_id, 'medium', 'my-gallery');

// Ottieni dati JSON per JavaScript
$gallery_json = mrc_get_lavoro_gallery_json($post_id, 'full');
```

#### Query Personalizzate
```php
// Lavori con galleria
$query = mrc_get_lavori_with_gallery(array(
    'posts_per_page' => 12
));

// Lavori con descrizione
$query = mrc_get_lavori_with_description(array(
    'posts_per_page' => 8
));
```

## Struttura Dati

### Meta Fields
- `_mrc_lavori_description`: Stringa con la descrizione breve
- `_mrc_lavori_gallery`: Array serializzato con gli ID delle immagini in ordine

### Struttura Immagine
```php
array(
    'id' => 123,
    'url' => 'https://example.com/image.jpg',
    'width' => 1920,
    'height' => 1080,
    'alt' => 'Testo alternativo',
    'caption' => 'Didascalia immagine'
)
```

## Personalizzazione

### CSS
Gli stili sono in `css/admin.css` e seguono l'approccio mobile-first. Puoi sovrascrivere gli stili nel tuo tema.

### JavaScript
Lo script principale è in `js/admin.js`. Include:
- Gestione upload drag & drop
- Riordinamento tramite HTML5 Drag & Drop API nativa
- Validazione e feedback utente
- **Vanilla JavaScript** - Nessuna dipendenza da jQuery

### Hook e Filtri
Il modulo utilizza gli hook standard di WordPress:
- `add_meta_boxes`: Per aggiungere le meta box
- `save_post`: Per salvare i dati
- `admin_enqueue_scripts`: Per caricare script e stili

## Requisiti
- WordPress 5.0+
- PHP 7.4+
- Browser moderno con supporto HTML5 Drag & Drop API
- Supporto per media uploader di WordPress

## Compatibilità
- Compatibile con Gutenberg
- Funziona con editor classico
- Supporta multisite
- Compatibile con cache plugins

## Sicurezza
- Verifica nonce per tutte le operazioni
- Sanitizzazione dei dati in input
- Validazione dei permessi utente
- Escape dei dati in output
