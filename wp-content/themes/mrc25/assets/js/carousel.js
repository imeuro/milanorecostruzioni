/**
 * Carousel JavaScript
 * Gestisce la funzionalità del carousel per le gallerie dei lavori
 * Vanilla JavaScript - No jQuery dependency
 */

(function() {
    'use strict';

    /**
     * Inizializza tutti i carousel presenti nella pagina
     */
    const initCarousels = () => {
        const carousels = document.querySelectorAll('.mrc-lavoro-carousel');
        
        carousels.forEach(function(carousel) {
            initSingleCarousel(carousel);
        });
    };

    /**
     * Inizializza un singolo carousel
     * @param {HTMLElement} carousel - Elemento carousel
     */
    const initSingleCarousel = (carousel) => {
        const track = carousel.querySelector('.mrc-carousel-track');
        const slides = carousel.querySelectorAll('.mrc-carousel-slide');
        const prevBtn = carousel.querySelector('.mrc-carousel-prev');
        const nextBtn = carousel.querySelector('.mrc-carousel-next');
        const dots = carousel.querySelectorAll('.mrc-carousel-dot');
        const counter = carousel.querySelector('.mrc-carousel-counter');
        
        if (!track || slides.length <= 1) return;
        
        let currentIndex = 0;
        const totalSlides = slides.length;
        
        /**
         * Aggiorna il carousel
         */
        const updateCarousel = () => {
            // Nascondi tutte le slide
            slides.forEach(function(slide) {
                slide.classList.remove('active');
            });
            
            // Mostra la slide corrente
            slides[currentIndex].classList.add('active');
            
            // Aggiorna i dots
            dots.forEach(function(dot) {
                dot.classList.remove('active');
            });
            if (dots[currentIndex]) {
                dots[currentIndex].classList.add('active');
            }
            
            // Aggiorna il counter
            if (counter) {
                const currentSpan = counter.querySelector('.current');
                if (currentSpan) {
                    currentSpan.textContent = currentIndex + 1;
                }
            }
        };
        
        /**
         * Vai alla slide successiva
         */
        const nextSlide = () => {
            currentIndex = (currentIndex + 1) % totalSlides;
            updateCarousel();
        };
        
        /**
         * Vai alla slide precedente
         */
        const prevSlide = () => {
            currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
            updateCarousel();
        };
        
        /**
         * Vai a una slide specifica
         * @param {number} index - Indice della slide
         */
        const goToSlide = (index) => {
            currentIndex = index;
            updateCarousel();
        };
        
        // Event listeners per i pulsanti
        if (nextBtn) {
            nextBtn.addEventListener('click', nextSlide);
        }
        
        if (prevBtn) {
            prevBtn.addEventListener('click', prevSlide);
        }
        
        // Event listeners per i dots
        dots.forEach(function(dot, index) {
            dot.addEventListener('click', function() {
                goToSlide(index);
            });
        });
        
        // Auto-play
        let autoPlayInterval;
        
        /**
         * Avvia l'auto-play
         */
        const startAutoPlay = () => {
            autoPlayInterval = setInterval(nextSlide, 5000);
        };
        
        /**
         * Ferma l'auto-play
         */
        const stopAutoPlay = () => {
            clearInterval(autoPlayInterval);
        };
        
        // Avvia auto-play
        startAutoPlay();
        
        // Pausa auto-play al hover
        carousel.addEventListener('mouseenter', stopAutoPlay);
        carousel.addEventListener('mouseleave', startAutoPlay);
        
        // Pausa auto-play quando la finestra non è attiva
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopAutoPlay();
            } else {
                startAutoPlay();
            }
        });
        
        // Navigazione con tastiera
        carousel.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                prevSlide();
            } else if (e.key === 'ArrowRight') {
                nextSlide();
            }
        });
        
        // Rendi il carousel focusabile per la navigazione con tastiera
        carousel.setAttribute('tabindex', '0');
        
        // Aggiungi click handler per aprire lightbox
        slides.forEach(function(slide, index) {
            slide.addEventListener('click', function() {
                openLightboxFromCarousel(index);
            });
            
            // Aggiungi cursore pointer per indicare che è cliccabile
            slide.style.cursor = 'pointer';
        });
        
        // Inizializza il carousel
        updateCarousel();
    };

    /**
     * Apre il lightbox dal carousel
     * @param {number} slideIndex - Indice della slide cliccata
     */
    const openLightboxFromCarousel = (slideIndex) => {
        // Trova il lightbox modal
        const lightboxModal = document.getElementById('mrc-lightbox');
        if (!lightboxModal) {
            console.warn('Lightbox modal non trovato');
            return;
        }

        // Trova l'immagine nella slide del carousel
        const carousel = document.querySelector('.mrc-lavoro-carousel');
        if (!carousel) return;

        const slides = carousel.querySelectorAll('.mrc-carousel-slide');
        const clickedSlide = slides[slideIndex];
        if (!clickedSlide) return;

        const slideImg = clickedSlide.querySelector('img');
        if (!slideImg) return;

        // Trova tutti i thumbnails del lightbox
        const thumbnails = document.querySelectorAll('.mrc-gallery-thumb');
        if (thumbnails.length === 0) {
            console.warn('Thumbnails del lightbox non trovati');
            return;
        }

        // Trova l'indice corrispondente nel lightbox
        let lightboxIndex = 0;
        thumbnails.forEach(function(thumb, index) {
            const thumbImg = thumb.querySelector('img');
            if (thumbImg && thumbImg.src === slideImg.src) {
                lightboxIndex = index;
            }
        });

        // Simula il click sul thumbnail corrispondente
        if (thumbnails[lightboxIndex]) {
            thumbnails[lightboxIndex].click();
        }
    };

    /**
     * Inizializza quando il DOM è pronto
     */
    const init = () => {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCarousels);
        } else {
            initCarousels();
        }
    };

    // Avvia l'inizializzazione
    init();

})();
