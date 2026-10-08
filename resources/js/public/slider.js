import { isRtl, prefersReducedMotion, replayAnimations } from './utils';

/**
 * Full-width slider: [data-slider] > [data-slider-track] > [data-slider-slide].
 * Optional controls: [data-slider-prev], [data-slider-next], [data-slider-dot][data-index].
 * Autoplay interval (ms) via data-autoplay on the root.
 */
const initSlider = (root) => {
    const track = root.querySelector('[data-slider-track]');
    const slides = [...root.querySelectorAll('[data-slider-slide]')];
    const dots = [...root.querySelectorAll('[data-slider-dot]')];

    if (!track || slides.length === 0) return;

    const interval = Number(root.dataset.autoplay || 0);
    let activeIndex = 0;
    let timer = null;
    let isPaused = false;

    const render = (index) => {
        activeIndex = (index + slides.length) % slides.length;
        const direction = isRtl() ? 1 : -1;
        track.style.transform = `translateX(${direction * activeIndex * 100}%)`;

        slides.forEach((slide, slideIndex) => {
            const isActive = slideIndex === activeIndex;
            slide.classList.toggle('is-active', isActive);
            slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
            slide.toggleAttribute('inert', !isActive);
            if (isActive) replayAnimations(slide);
        });

        dots.forEach((dot, dotIndex) => {
            const isActive = dotIndex === activeIndex;
            dot.setAttribute('aria-current', isActive ? 'true' : 'false');
            dot.classList.toggle('is-active', isActive);
        });
    };

    const restart = () => {
        window.clearInterval(timer);
        if (!interval || slides.length < 2 || prefersReducedMotion()) return;
        timer = window.setInterval(() => {
            if (!isPaused && !document.hidden) render(activeIndex + 1);
        }, interval);
    };

    const go = (index) => {
        render(index);
        restart();
    };

    root.querySelector('[data-slider-prev]')?.addEventListener('click', () => go(activeIndex - 1));
    root.querySelector('[data-slider-next]')?.addEventListener('click', () => go(activeIndex + 1));
    dots.forEach((dot, dotIndex) => dot.addEventListener('click', () => go(dotIndex)));

    // Pause only while a keyboard user is inside; hovering or clicking the arrows must not stop the slides.
    root.addEventListener('focusin', (event) => { isPaused = event.target.matches(':focus-visible'); });
    root.addEventListener('focusout', () => { isPaused = false; });

    root.addEventListener('keydown', (event) => {
        if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;
        const forward = (event.key === 'ArrowLeft') === isRtl();
        go(activeIndex + (forward ? 1 : -1));
    });

    let pointerStartX = null;
    root.addEventListener('pointerdown', (event) => {
        if (event.pointerType === 'mouse') return;
        pointerStartX = event.clientX;
    });
    root.addEventListener('pointerup', (event) => {
        if (pointerStartX === null) return;
        const deltaX = event.clientX - pointerStartX;
        pointerStartX = null;
        if (Math.abs(deltaX) < 40) return;
        const forward = isRtl() ? deltaX > 0 : deltaX < 0;
        go(activeIndex + (forward ? 1 : -1));
    });

    render(0);
    restart();
};

/**
 * Horizontal snap scroller: [data-scroller] > [data-scroller-track], with prev/next buttons.
 */
const initScroller = (root) => {
    const track = root.querySelector('[data-scroller-track]');
    if (!track) return;

    const step = () => {
        const firstItem = track.firstElementChild;
        return firstItem ? firstItem.getBoundingClientRect().width + 20 : track.clientWidth * 0.8;
    };

    const scrollByStep = (forward) => {
        const sign = isRtl() ? -1 : 1;
        track.scrollBy({
            left: (forward ? 1 : -1) * sign * step(),
            behavior: prefersReducedMotion() ? 'auto' : 'smooth',
        });
    };

    root.querySelector('[data-scroller-prev]')?.addEventListener('click', () => scrollByStep(false));
    root.querySelector('[data-scroller-next]')?.addEventListener('click', () => scrollByStep(true));
};

export const initSliders = () => {
    document.querySelectorAll('[data-slider]').forEach(initSlider);
    document.querySelectorAll('[data-scroller]').forEach(initScroller);
};
