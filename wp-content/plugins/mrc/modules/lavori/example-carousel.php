<?php
/**
 * Esempio di implementazione carousel per lavori
 * 
 * Questo file mostra come utilizzare il modulo lavori per creare
 * un carousel di immagini nel frontend
 * 
 * @package MRC
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Esempio di template per visualizzare un lavoro con carousel
 */
function mrc_display_lavoro_with_carousel($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    // Verifica che sia un post di tipo lavori
    if (get_post_type($post_id) !== 'lavori') {
        return;
    }
    
    $title = get_the_title($post_id);
    $description = mrc_get_lavoro_description($post_id);
    $gallery_images = mrc_get_lavoro_gallery_images($post_id, 'large');
    
    if (empty($gallery_images)) {
        return;
    }
    
    ?>
    <div class="mrc-lavoro-carousel" data-post-id="<?php echo esc_attr($post_id); ?>">
        <div class="mrc-lavoro-header">
            <?php if ($description): ?>
                <div class="mrc-lavoro-description">
                    <?php echo wp_kses_post(wpautop($description)); ?>
                </div>
            <?php endif; ?>
            <?php if (mrc_has_lavoro_gallery()): ?>
                <span class="gallery-count">
                    <?php echo mrc_get_lavoro_gallery_count(); ?> immagini
                </span>
            <?php endif; ?>
        </div>
        
        <div class="mrc-carousel-container">
            <div class="mrc-carousel-wrapper">
                <div class="mrc-carousel-track" id="carousel-track-<?php echo esc_attr($post_id); ?>">
                    <?php foreach ($gallery_images as $index => $image): ?>
                        <div class="mrc-carousel-slide <?php echo $index === 0 ? 'active' : ''; ?>" 
                             data-index="<?php echo esc_attr($index); ?>">
                            <img src="<?php echo esc_url($image['url']); ?>" 
                                 alt="<?php echo esc_attr($image['alt'] ?: $title); ?>"
                                 loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>">
                            
                            <?php if ($image['caption']): ?>
                                <div class="mrc-carousel-caption">
                                    <?php echo esc_html($image['caption']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (count($gallery_images) > 1): ?>
                    <button class="mrc-carousel-prev" aria-label="Immagine precedente">
                        <span class="dashicons dashicons-arrow-left-alt2"></span>
                    </button>
                    <button class="mrc-carousel-next" aria-label="Immagine successiva">
                        <span class="dashicons dashicons-arrow-right-alt2"></span>
                    </button>
                <?php endif; ?>
            </div>
            
            <?php if (count($gallery_images) > 1): ?>
                <div class="mrc-carousel-dots">
                    <?php foreach ($gallery_images as $index => $image): ?>
                        <button class="mrc-carousel-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                                data-index="<?php echo esc_attr($index); ?>"
                                aria-label="Vai all'immagine <?php echo esc_attr($index + 1); ?>">
                        </button>
                    <?php endforeach; ?>
                </div>
                
                <div class="mrc-carousel-counter">
                    <span class="current">1</span> / <span class="total"><?php echo count($gallery_images); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/**
 * CSS per il carousel (da includere nel tema)
 */
function mrc_carousel_styles() {
    ?>
    <style>
    /* Mobile First - Carousel Styles */
    .mrc-lavoro-carousel {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
    }
    
    .mrc-lavoro-header {
        margin-bottom: 1rem;
        text-align: center;
    }
    
    .mrc-lavoro-title {
        font-size: 1.5rem;
        margin: 0 0 0.5rem 0;
        color: #333;
    }
    
    .mrc-lavoro-description {
        font-size: 0.9rem;
        line-height: 1.6;
        color: #666;
        max-width: 600px;
        margin: 0 auto;
    }
    
    .mrc-carousel-container {
        position: relative;
        background: #f9f9f9;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .mrc-carousel-wrapper {
        position: relative;
        width: 100%;
        height: 250px;
        overflow: hidden;
    }
    
    .mrc-carousel-track {
        display: flex;
        width: 100%;
        height: 100%;
        transition: transform 0.3s ease;
    }
    
    .mrc-carousel-slide {
        min-width: 100%;
        height: 100%;
        position: relative;
        display: none;
    }
    
    .mrc-carousel-slide.active {
        display: block;
    }
    
    .mrc-carousel-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    
    .mrc-carousel-caption {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 0.5rem;
        font-size: 0.8rem;
        text-align: center;
    }
    
    .mrc-carousel-prev,
    .mrc-carousel-next {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s ease;
        z-index: 10;
    }
    
    .mrc-carousel-prev:hover,
    .mrc-carousel-next:hover {
        background: rgba(0, 0, 0, 0.8);
    }
    
    .mrc-carousel-prev {
        left: 10px;
    }
    
    .mrc-carousel-next {
        right: 10px;
    }
    
    .mrc-carousel-dots {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        padding: 1rem;
    }
    
    .mrc-carousel-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: none;
        background: #ccc;
        cursor: pointer;
        transition: background 0.3s ease;
    }
    
    .mrc-carousel-dot.active {
        background: #333;
    }
    
    .mrc-carousel-counter {
        text-align: center;
        padding: 0.5rem;
        font-size: 0.8rem;
        color: #666;
    }
    
    /* Tablet */
    @media (min-width: 768px) {
        .mrc-carousel-wrapper {
            height: 400px;
        }
        
        .mrc-lavoro-title {
            font-size: 2rem;
        }
        
        .mrc-lavoro-description {
            font-size: 1rem;
        }
        
        .mrc-carousel-prev,
        .mrc-carousel-next {
            width: 50px;
            height: 50px;
        }
        
        .mrc-carousel-prev {
            left: 20px;
        }
        
        .mrc-carousel-next {
            right: 20px;
        }
    }
    
    /* Desktop */
    @media (min-width: 1024px) {
        .mrc-carousel-wrapper {
            height: 500px;
        }
        
        .mrc-lavoro-title {
            font-size: 2.5rem;
        }
        
        .mrc-lavoro-description {
            font-size: 1.1rem;
        }
    }
    </style>
    <?php
}

/**
 * JavaScript per il carousel (da includere nel tema)
 * Vanilla JavaScript - No jQuery dependency
 */
function mrc_carousel_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const carousels = document.querySelectorAll('.mrc-lavoro-carousel');
        
        carousels.forEach(function(carousel) {
            const track = carousel.querySelector('.mrc-carousel-track');
            const slides = carousel.querySelectorAll('.mrc-carousel-slide');
            const prevBtn = carousel.querySelector('.mrc-carousel-prev');
            const nextBtn = carousel.querySelector('.mrc-carousel-next');
            const dots = carousel.querySelectorAll('.mrc-carousel-dot');
            const counter = carousel.querySelector('.mrc-carousel-counter');
            
            if (!track || slides.length <= 1) return;
            
            let currentIndex = 0;
            const totalSlides = slides.length;
            
            function updateCarousel() {
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
            }
            
            function nextSlide() {
                currentIndex = (currentIndex + 1) % totalSlides;
                updateCarousel();
            }
            
            function prevSlide() {
                currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
                updateCarousel();
            }
            
            function goToSlide(index) {
                currentIndex = index;
                updateCarousel();
            }
            
            // Event listeners
            if (nextBtn) {
                nextBtn.addEventListener('click', nextSlide);
            }
            
            if (prevBtn) {
                prevBtn.addEventListener('click', prevSlide);
            }
            
            dots.forEach(function(dot, index) {
                dot.addEventListener('click', function() {
                    goToSlide(index);
                });
            });
            
            // Auto-play (opzionale)
            let autoPlayInterval;
            
            function startAutoPlay() {
                autoPlayInterval = setInterval(nextSlide, 5000);
            }
            
            function stopAutoPlay() {
                clearInterval(autoPlayInterval);
            }
            
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
        });
    });
    </script>
    <?php
}

/**
 * Hook per includere stili e script nel frontend
 */
add_action('wp_head', 'mrc_carousel_styles');
add_action('wp_footer', 'mrc_carousel_script');
