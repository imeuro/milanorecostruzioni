document.addEventListener('DOMContentLoaded', function() {
    
    // Header scroll effect
    const header = document.querySelector('.site-header');
    let lastScrollTop = 0;
    
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // Aggiungi classe scrolled quando si scrolla
        if (scrollTop > 150) {
            header.classList.add('scrolled');
            // Cambio logo desktop e mobile
            const logoDesktop = document.getElementById('site-logo-desktop');
            const logoMobile = document.getElementById('site-logo-mobile');
            if (logoDesktop) {
                logoDesktop.src = window.themeAssetsUrl + '/mrc-logo.png';
            }
            if (logoMobile) {
                logoMobile.src = window.themeAssetsUrl + '/mrc-logo.png';
            }
        } else {
            header.classList.remove('scrolled');
            // Ripristina logo desktop e mobile
            const logoDesktop = document.getElementById('site-logo-desktop');
            const logoMobile = document.getElementById('site-logo-mobile');
            if (logoDesktop) {
                logoDesktop.src = window.themeAssetsUrl + '/mrc-ico.svg';
            }
            if (logoMobile) {
                logoMobile.src = window.themeAssetsUrl + '/mrc-ico.svg';
            }
        }
        
        lastScrollTop = scrollTop;
    });
    
    // Intersection Observer per animazioni delle sezioni
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
            }
        });
    }, observerOptions);
    
    // Osserva le sezioni per le animazioni
    const animatedElements = document.querySelectorAll('.section-header, .activity-card, .portfolio-item');
    animatedElements.forEach(el => {
        el.style.animationPlayState = 'paused';
        observer.observe(el);
    });
    
    // Smooth scroll per i link interni
    const internalLinks = document.querySelectorAll('a[href^="#"]');
    internalLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                const headerHeight = header.offsetHeight;
                const targetPosition = targetElement.offsetTop - headerHeight - 20;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Effetto parallax per il hero carousel
    const heroCarousel = document.querySelector('.mrc-hero-carousel');
    if (heroCarousel) {
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            heroCarousel.style.transform = `translateY(${rate}px)`;
        });
    }
    
    // Hover effects per le card
    const cards = document.querySelectorAll('.activity-card, .portfolio-item');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Lazy loading per le immagini
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
    
    // Effetto typing per i titoli delle sezioni
    function typeWriter(element, text, speed = 100) {
        let i = 0;
        element.innerHTML = '';
        
        function type() {
            if (i < text.length) {
                element.innerHTML += text.charAt(i);
                i++;
                setTimeout(type, speed);
            }
        }
        type();
    }
    
    // Applica l'effetto typing ai titoli delle sezioni quando diventano visibili
    const sectionTitles = document.querySelectorAll('.section-title');
    const titleObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const title = entry.target;
                const originalText = title.textContent;
                typeWriter(title, originalText, 50);
                titleObserver.unobserve(title);
            }
        });
    }, { threshold: 0.5 });
    
    sectionTitles.forEach(title => titleObserver.observe(title));
}); 