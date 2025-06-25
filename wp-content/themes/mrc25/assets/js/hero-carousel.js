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

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.remove('active');
            if (dots[i]) dots[i].classList.remove('active');
        });
        
        slides[index].classList.add('active');
        if (dots[index]) dots[index].classList.add('active');
        currentSlide = index;
    }

    function nextSlide() {
        const next = (currentSlide + 1) % slides.length;
        showSlide(next);
    }

    function goToSlide(index) {
        showSlide(index);
        resetInterval();
    }

    function resetInterval() {
        clearInterval(interval);
        interval = setInterval(nextSlide, 8000);
    }

    // Inizializza
    showSlide(0);
    resetInterval();

    // Pausa al hover
    carousel.addEventListener('mouseenter', () => clearInterval(interval));
    carousel.addEventListener('mouseleave', resetInterval);

    // Navigazione con tastiera
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            const prev = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(prev);
            resetInterval();
        } else if (e.key === 'ArrowRight') {
            nextSlide();
            resetInterval();
        }
    });
}); 