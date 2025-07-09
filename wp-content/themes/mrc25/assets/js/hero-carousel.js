document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.querySelector('.mrc-hero-carousel');
    if (!carousel) return;

    const slides = carousel.querySelectorAll('.mrc-hero-slide');
    const navContainer = document.createElement('div');
    navContainer.className = 'mrc-hero-nav';
    
    let currentSlide = 0;
    let interval;

    // Crea il contenuto per ogni slide
    slides.forEach((slide, index) => {
        const content = document.createElement('div');
        content.className = 'mrc-hero-content';
        
        const title = document.createElement('h1');
        title.className = 'mrc-hero-title';
        title.innerHTML = `<img src="wp-content/themes/mrc25/assets/img/mrc-logo-white.svg" alt="Milano RE Costruzioni">`;
        
        const subtitle = document.createElement('p');
        subtitle.className = 'mrc-hero-subtitle';
        // Usa il titolo del post hero-slide come subtitle
        const slideTitle = slide.getAttribute('data-title') || 'Milano RE Costruzioni';
        subtitle.textContent = slideTitle;
        
        content.appendChild(title);
        content.appendChild(subtitle);
        slide.appendChild(content);
    });

    // Crea i punti di navigazione
    slides.forEach((_, index) => {
        const dot = document.createElement('div');
        dot.className = 'mrc-hero-dot';
        dot.addEventListener('click', () => goToSlide(index));
        navContainer.appendChild(dot);
    });
    
    carousel.appendChild(navContainer);
    const dots = navContainer.querySelectorAll('.mrc-hero-dot');

    // Gestione video
    const videos = carousel.querySelectorAll('.mrc-hero-video');
    videos.forEach(video => {
        // Aggiungi classe loading durante il caricamento
        const slide = video.closest('.mrc-hero-slide');
        if (slide) {
            slide.classList.add('loading');
        }

        // Gestisci eventi video
        video.addEventListener('loadeddata', function() {
            if (slide) {
                slide.classList.remove('loading');
            }
        });

        video.addEventListener('error', function() {
            if (slide) {
                slide.classList.remove('loading');
                console.error('Errore caricamento video:', video.src);
            }
        });

        // Gestisci la fine del video
        video.addEventListener('ended', function() {
            // Quando il video finisce, passa alla slide successiva
            const currentIndex = Array.from(slides).indexOf(slide);
            const nextIndex = (currentIndex + 1) % slides.length;
            // Passa true per indicare che è una transizione automatica
            showSlide(nextIndex, true);
            resetInterval();
        });

        // Pausa video quando non è attivo
        video.addEventListener('pause', function() {
            // Se il video è pausato e la slide è attiva, riavvia
            if (slide && slide.classList.contains('active')) {
                video.play().catch(e => console.log('Errore autoplay video:', e));
            }
        });
    });

    // Gestione animazioni per le slide con immagini
    slides.forEach(slide => {
        if (!slide.querySelector('.mrc-hero-video')) {
            // Rimuovi eventuale listener precedente
            slide.removeEventListener('animationend', slide._zoomEndListener);
            // Definisci e salva il listener
            slide._zoomEndListener = function(e) {
                if (e.animationName === 'zoomOut') {
                    this.classList.add('zoomed-final');
                }
            };
            slide.addEventListener('animationend', slide._zoomEndListener);
        }
    });

    function showSlide(index, isAutoTransition = false) {
        slides.forEach((slide, i) => {
            slide.classList.remove('active');
            if (dots[i]) dots[i].classList.remove('active');
            
            // Pausa tutti i video
            const video = slide.querySelector('.mrc-hero-video');
            if (video) {
                video.pause();
                // Resetta il tempo solo se NON è una transizione automatica
                if (!isAutoTransition) {
                    video.currentTime = 0;
                }
            }
            
            // Per le slide con immagini, rimuovi la classe zoomed-final solo se la slide sta per essere riattivata
            if (!slide.querySelector('.mrc-hero-video')) {
                if (i === index) {
                    slide.classList.remove('zoomed-final');
                }
            }
        });
        
        slides[index].classList.add('active');
        if (dots[index]) dots[index].classList.add('active');
        currentSlide = index;

        // Avvia il video della slide attiva solo se non è già alla fine
        const activeVideo = slides[index].querySelector('.mrc-hero-video');
        if (activeVideo) {
            // Se il video è alla fine, non riprodurlo
            if (activeVideo.ended || activeVideo.currentTime >= activeVideo.duration - 0.1) {
                // Il video è già finito, non fare nulla
            } else {
                activeVideo.play().catch(e => console.log('Errore autoplay video:', e));
            }
        }
        
        // Avvia l'animazione zoom per le slide con immagini
        const activeSlide = slides[index];
        if (!activeSlide.querySelector('.mrc-hero-video')) {
            // Piccolo delay per assicurarsi che la transizione opacity sia completata
            setTimeout(() => {
                const slideDuration = getComputedStyle(carousel).getPropertyValue('--slide-duration');
                activeSlide.style.setProperty('--slide-animation', `zoomOut ${slideDuration} ease-out forwards`);
            }, 100);
        }
    }

    function nextSlide() {
        const next = (currentSlide + 1) % slides.length;
        showSlide(next, false); // Non è una transizione automatica
    }

    function goToSlide(index) {
        // Per navigazione manuale, resetta sempre l'animazione
        slides.forEach(slide => {
            if (!slide.querySelector('.mrc-hero-video')) {
                slide.classList.remove('animation-complete');
                slide.style.setProperty('--slide-animation', 'none');
                slide.style.setProperty('--slide-transform', 'scale(1.2)');
            }
        });
        
        showSlide(index, false); // Navigazione manuale
        resetInterval();
        
        // Se l'utente naviga manualmente e il video è finito, riavvialo
        const activeVideo = slides[index].querySelector('.mrc-hero-video');
        if (activeVideo && (activeVideo.ended || activeVideo.currentTime >= activeVideo.duration - 0.1)) {
            activeVideo.currentTime = 0;
            activeVideo.play().catch(e => console.log('Errore autoplay video:', e));
        }
    }

    function resetInterval() {
        clearInterval(interval);
        
        // Se la slide attiva ha un video, non impostare l'intervallo automatico
        // perché il video gestirà la transizione alla fine
        const activeSlide = slides[currentSlide];
        const hasVideo = activeSlide.querySelector('.mrc-hero-video');
        
        if (!hasVideo) {
            interval = setInterval(nextSlide, 8000);
        }
    }

    // Inizializza
    showSlide(0);
    resetInterval();

    // Pausa al hover
    carousel.addEventListener('mouseenter', () => {
        clearInterval(interval);
        // Pausa il video attivo durante l'hover
        const activeVideo = slides[currentSlide].querySelector('.mrc-hero-video');
        if (activeVideo) {
            activeVideo.pause();
        }
    });
    
    carousel.addEventListener('mouseleave', () => {
        // Riprendi il video attivo quando esci dall'hover
        const activeVideo = slides[currentSlide].querySelector('.mrc-hero-video');
        if (activeVideo && !activeVideo.ended && activeVideo.currentTime < activeVideo.duration - 0.1) {
            activeVideo.play().catch(e => console.log('Errore autoplay video:', e));
        }
        // Reset dell'intervallo solo se non c'è un video
        if (!activeVideo) {
            resetInterval();
        }
    });

    // Navigazione con tastiera
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            const prev = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(prev, false); // Navigazione manuale
            resetInterval();
        } else if (e.key === 'ArrowRight') {
            nextSlide();
            resetInterval();
        }
    });

    // Gestione visibilità pagina per ottimizzare la riproduzione video
    document.addEventListener('visibilitychange', () => {
        const activeVideo = slides[currentSlide].querySelector('.mrc-hero-video');
        if (document.hidden) {
            // Pausa video quando la pagina non è visibile
            if (activeVideo) {
                activeVideo.pause();
            }
            clearInterval(interval);
        } else {
            // Riprendi video quando la pagina torna visibile solo se non è finito
            if (activeVideo && !activeVideo.ended && activeVideo.currentTime < activeVideo.duration - 0.1) {
                activeVideo.play().catch(e => console.log('Errore autoplay video:', e));
            }
            // Reset dell'intervallo solo se non c'è un video
            if (!activeVideo) {
                resetInterval();
            }
        }
    });
}); 