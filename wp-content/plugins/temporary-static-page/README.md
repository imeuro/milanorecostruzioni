# Plugin Pagina Temporanea Statica

Plugin WordPress per mostrare una pagina statica temporanea personalizzabile al posto della homepage.

## Caratteristiche

✅ **Completamente personalizzabile** - Modifica direttamente il file HTML/CSS/JS  
✅ **Attivazione rapida** - Un click per attivare/disattivare  
✅ **Bypass amministratori** - Gli admin vedono sempre il sito normale  
✅ **Solo homepage** - Non interferisce con le altre pagine  
✅ **Design responsive** - Ottimizzato per tutti i dispositivi  
✅ **Accessibilità** - Conforme alle best practices  

## Installazione

1. Copia la cartella `temporary-static-page` in `/wp-content/plugins/`
2. Attiva il plugin dal pannello WordPress (Plugin → Plugin installati)
3. Vai in Impostazioni → Pagina Temporanea per gestirlo

## Utilizzo

### Attivazione
- Vai in **Impostazioni → Pagina Temporanea**
- Clicca "Attiva Pagina Temporanea"
- I visitatori vedranno la pagina temporanea, tu (admin) vedrai il sito normale

### Personalizzazione
Il file da modificare è: `temporary-page.html`

Puoi modificarlo come vuoi:
- **HTML**: Cambia struttura, contenuti, testi
- **CSS**: Personalizza completamente il design 
- **JavaScript**: Aggiungi funzionalità dinamiche

### Test
Per vedere la pagina come visitatore:
- Apri il sito in **navigazione privata/incognito**
- Oppure esci dal pannello admin di WordPress

## Esempi di personalizzazione

### Cambiare i testi
Modifica direttamente nel file `temporary-page.html`:
```html
<h1 class="maintenance-title">Il tuo titolo qui</h1>
<p>Il tuo messaggio personalizzato...</p>
```

### Cambiare i colori (NUOVO!)
Ora puoi facilmente personalizzare tutti i colori modificando le variabili CSS nella sezione `:root`:

**Tema scuro:**
```css
:root {
    --bg-gradient-start: #2c3e50;
    --bg-gradient-end: #34495e;
    --container-bg: rgba(52, 73, 94, 0.95);
    --text-primary: #ecf0f1;
    --text-heading: #ffffff;
}
```

**Tema verde:**
```css
:root {
    --primary-color: #27ae60;
    --primary-dark: #2ecc71;
    --bg-gradient-start: #27ae60;
    --bg-gradient-end: #2ecc71;
}
```

**Personalizzazione completa:**
```css
:root {
    --primary-color: #tuocolore;          /* Colore principale */
    --bg-gradient-start: #tuocolore1;     /* Inizio sfondo */
    --bg-gradient-end: #tuocolore2;       /* Fine sfondo */
    --text-heading: #tuocolore3;          /* Colore titoli */
    --accent-color: #tuocolore4;          /* Colore accenti */
}
```

### Cambiare countdown
Modifica nel JavaScript:
```javascript
// 4 ore da adesso
const endTime = new Date().getTime() + (4 * 60 * 60 * 1000);
```

### Aggiungere il tuo logo
Sostituisci l'emoji con un'immagine:
```html
<img src="path/al/tuo/logo.png" alt="Logo" class="maintenance-icon">
```

## Sicurezza

- ✅ Nonce protection per le azioni admin
- ✅ Capability checks (solo amministratori)
- ✅ Escape di tutti gli output
- ✅ Prevenzione accesso diretto ai file

## FAQ

**Q: Gli amministratori vedono la pagina temporanea?**  
A: No, gli admin vedono sempre il sito normale.

**Q: La pagina temporanea viene indicizzata?**  
A: No, include `noindex, nofollow` nei meta tag.

**Q: Posso usare PHP nel file temporaneo?**  
A: Sì, puoi rinominare il file in `temporary-page.php` e usare codice PHP.

**Q: Quando disattivo il plugin cosa succede?**  
A: Il sito torna immediatamente normale e le impostazioni vengono rimosse.

## Struttura file

```
temporary-static-page/
├── temporary-static-page.php    (Plugin principale)
├── temporary-page.html          (Pagina personalizzabile)
└── README.md                    (Questo file)
```

## Supporto

Per assistenza o personalizzazioni contatta: info@milanorecostruzioni.it

---

**Versione:** 1.0.0  
**Compatibilità:** WordPress 5.0+  
**Licenza:** GPL v2 or later 