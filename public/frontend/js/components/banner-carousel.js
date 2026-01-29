/**
 * ============================================================
 * DALEL - Banner Carousel Component
 * Handles carousel sliding, lightbox, and touch gestures
 * ============================================================
 */

(function() {
    'use strict';

    // State
    let currentSlide = 0;
    let autoSlideInterval;
    let touchStartX = 0;
    let touchEndX = 0;

    // Elements
    const track = document.getElementById('bannerTrack');
    const dots = document.querySelectorAll('.p-banner-carousel__dot');
    const carousel = document.getElementById('bannerCarousel');
    const lightbox = document.getElementById('bannerLightbox');
    const totalSlides = dots.length;

    /**
     * Update carousel position and active dot
     */
    function updateCarousel() {
        if (!track) return;

        const isRTL = document.documentElement.dir === 'rtl' || document.documentElement.lang === 'ar';
        const offset = isRTL ? currentSlide * 100 : -currentSlide * 100;
        track.style.transform = `translateX(${offset}%)`;

        dots.forEach((dot, index) => {
            dot.classList.toggle('p-banner-carousel__dot--active', index === currentSlide);
        });
    }

    /**
     * Slide carousel in direction
     * @param {number} direction - 1 for next, -1 for previous
     */
    window.slideCarousel = function(direction) {
        currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
        updateCarousel();
        resetAutoSlide();
    };

    /**
     * Go to specific slide
     * @param {number} index - Slide index
     */
    window.goToSlide = function(index) {
        currentSlide = index;
        updateCarousel();
        resetAutoSlide();
    };

    /**
     * Reset auto-slide timer
     */
    function resetAutoSlide() {
        clearInterval(autoSlideInterval);
        startAutoSlide();
    }

    /**
     * Start auto-sliding
     */
    function startAutoSlide() {
        if (totalSlides > 1) {
            autoSlideInterval = setInterval(() => {
                window.slideCarousel(1);
            }, 5000);
        }
    }

    /**
     * Open lightbox with image
     * @param {string} imageSrc - Image URL
     * @param {string} title - Banner title
     * @param {string} description - Banner description
     */
    window.openLightbox = function(imageSrc, title, description) {
        if (!lightbox) return;

        const image = document.getElementById('lightboxImage');
        const titleEl = document.getElementById('lightboxTitle');
        const descEl = document.getElementById('lightboxDesc');

        if (image) image.src = imageSrc;
        if (titleEl) {
            titleEl.textContent = title || '';
            titleEl.style.display = title ? 'block' : 'none';
        }
        if (descEl) {
            descEl.textContent = description || '';
            descEl.style.display = description ? 'block' : 'none';
        }

        lightbox.classList.add('p-lightbox--active');
        document.body.style.overflow = 'hidden';
    };

    /**
     * Close lightbox
     */
    window.closeLightbox = function() {
        if (!lightbox) return;
        lightbox.classList.remove('p-lightbox--active');
        document.body.style.overflow = '';
    };

    /**
     * Handle touch swipe
     */
    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;

        if (Math.abs(diff) > swipeThreshold) {
            const isRTL = document.documentElement.dir === 'rtl';
            if (diff > 0) {
                window.slideCarousel(isRTL ? -1 : 1);
            } else {
                window.slideCarousel(isRTL ? 1 : -1);
            }
        }
    }

    /**
     * Initialize carousel
     */
    function init() {
        // Close lightbox on backdrop click
        if (lightbox) {
            lightbox.addEventListener('click', function(e) {
                if (e.target === this) {
                    window.closeLightbox();
                }
            });
        }

        // Close lightbox on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.closeLightbox();
            }
        });

        // Touch events for carousel
        if (carousel) {
            carousel.addEventListener('touchstart', function(e) {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });

            carousel.addEventListener('touchend', function(e) {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();
            }, { passive: true });
        }

        // Start auto-slide
        if (totalSlides > 1) {
            startAutoSlide();
        }

        // Pause auto-slide on hover
        if (carousel) {
            carousel.addEventListener('mouseenter', function() {
                clearInterval(autoSlideInterval);
            });

            carousel.addEventListener('mouseleave', function() {
                startAutoSlide();
            });
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
