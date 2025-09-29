/**
 * Gallery Lightbox JavaScript
 * Gestisce la funzionalità del lightbox per le gallerie dei lavori
 */

(function() {
    'use strict';

    // Variabili globali
    let currentImageIndex = 0;
    let images = [];
    let lightboxModal = null;
    let lightboxImage = null;
    let lightboxCaption = null;

    /**
     * Inizializza il lightbox
     */
    const initLightbox = () => {
        // Trova tutti i thumbnails della galleria
        const thumbnails = document.querySelectorAll('.mrc-gallery-thumb');
        
        if (thumbnails.length === 0) {
            return;
        }

        // Raccoglie i dati delle immagini
        images = Array.from(thumbnails).map((thumb, index) => {
            const img = thumb.querySelector('img');
            return {
                src: img.src,
                alt: img.alt,
                caption: img.getAttribute('data-caption') || '',
                index: index
            };
        });

        // Aggiunge event listeners ai thumbnails
        thumbnails.forEach((thumb, index) => {
            thumb.addEventListener('click', () => openLightbox(index));
        });

        // Inizializza il modal del lightbox
        initLightboxModal();
    };

    /**
     * Inizializza il modal del lightbox
     */
    const initLightboxModal = () => {
        lightboxModal = document.getElementById('mrc-lightbox');
        if (!lightboxModal) {
            return;
        }

        lightboxImage = lightboxModal.querySelector('.mrc-lightbox-image');
        lightboxCaption = lightboxModal.querySelector('.mrc-lightbox-caption');

        // Event listeners per i controlli
        const closeBtn = lightboxModal.querySelector('.mrc-lightbox-close');
        const prevBtn = lightboxModal.querySelector('.mrc-lightbox-prev');
        const nextBtn = lightboxModal.querySelector('.mrc-lightbox-next');

        if (closeBtn) {
            closeBtn.addEventListener('click', closeLightbox);
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => navigateImage(-1));
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => navigateImage(1));
        }

        // Chiudi con ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && lightboxModal.classList.contains('active')) {
                closeLightbox();
            }
        });

        // Navigazione con frecce
        document.addEventListener('keydown', (e) => {
            if (!lightboxModal.classList.contains('active')) {
                return;
            }

            if (e.key === 'ArrowLeft') {
                navigateImage(-1);
            } else if (e.key === 'ArrowRight') {
                navigateImage(1);
            }
        });

        // Chiudi cliccando fuori dall'immagine
        lightboxModal.addEventListener('click', (e) => {
            if (e.target === lightboxModal) {
                closeLightbox();
            }
        });
    };

    /**
     * Apre il lightbox
     * @param {number} index - Indice dell'immagine da mostrare
     */
    const openLightbox = (index) => {
        if (!lightboxModal || images.length === 0) {
            return;
        }

        currentImageIndex = index;
        updateLightboxImage();
        lightboxModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    /**
     * Chiude il lightbox
     */
    const closeLightbox = () => {
        if (!lightboxModal) {
            return;
        }

        lightboxModal.classList.remove('active');
        document.body.style.overflow = '';
    };

    /**
     * Naviga tra le immagini
     * @param {number} direction - Direzione (-1 per precedente, 1 per successiva)
     */
    const navigateImage = (direction) => {
        if (images.length === 0) {
            return;
        }

        currentImageIndex += direction;

        // Gestisce il loop delle immagini
        if (currentImageIndex < 0) {
            currentImageIndex = images.length - 1;
        } else if (currentImageIndex >= images.length) {
            currentImageIndex = 0;
        }

        updateLightboxImage();
    };

    /**
     * Aggiorna l'immagine nel lightbox
     */
    const updateLightboxImage = () => {
        if (!lightboxImage || !lightboxCaption || images.length === 0) {
            return;
        }

        const currentImage = images[currentImageIndex];
        
        lightboxImage.src = currentImage.src;
        lightboxImage.alt = currentImage.alt;
        
        if (currentImage.caption) {
            lightboxCaption.textContent = currentImage.caption;
            lightboxCaption.style.display = 'block';
        } else {
            lightboxCaption.style.display = 'none';
        }
    };

    /**
     * Inizializza quando il DOM è pronto
     */
    const init = () => {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initLightbox);
        } else {
            initLightbox();
        }
    };

    // Avvia l'inizializzazione
    init();

})();
