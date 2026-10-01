import { prefersReducedMotion } from './utils';

export const initReveal = () => {
    const nodes = document.querySelectorAll('[data-reveal]');

    if (!nodes.length) return;

    if (prefersReducedMotion() || !('IntersectionObserver' in window)) {
        nodes.forEach((node) => node.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver((entries, current) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            current.unobserve(entry.target);
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -8% 0px',
    });

    nodes.forEach((node) => observer.observe(node));
};

const parseCountValue = (text) => {
    const match = text.trim().match(/^([^\d]*)([\d,]+(?:\.\d+)?)(.*)$/);
    if (!match) return null;

    const rawNumber = match[2].replace(/,/g, '');

    return {
        prefix: match[1] || '',
        value: parseFloat(rawNumber),
        suffix: match[3] || '',
        hasCommas: match[2].includes(','),
        decimals: rawNumber.includes('.') ? rawNumber.split('.')[1].length : 0,
    };
};

const formatCount = (value, info) => {
    let formatted = value.toFixed(info.decimals);

    if (info.hasCommas) {
        const parts = formatted.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        formatted = parts.join('.');
    }

    return `${info.prefix}${formatted}${info.suffix}`;
};

export const initCountUp = () => {
    const counterNodes = document.querySelectorAll('[data-countup]');
    if (!counterNodes.length) return;

    if (prefersReducedMotion() || !('IntersectionObserver' in window)) {
        counterNodes.forEach((node) => {
            node.textContent = node.getAttribute('data-countup') || node.textContent;
        });
        return;
    }

    const animateCount = (element, info) => {
        const duration = 1800;
        const start = performance.now();
        const easeOutExpo = (t) => (t === 1 ? 1 : 1 - Math.pow(2, -10 * t));

        const frame = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            element.textContent = formatCount(info.value * easeOutExpo(progress), info);

            if (progress < 1) {
                requestAnimationFrame(frame);
            } else {
                element.textContent = formatCount(info.value, info);
            }
        };

        requestAnimationFrame(frame);
    };

    const observer = new IntersectionObserver((entries, current) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            const info = parseCountValue(entry.target.getAttribute('data-countup') || entry.target.textContent);
            if (info && !Number.isNaN(info.value)) {
                animateCount(entry.target, info);
            }
            current.unobserve(entry.target);
        });
    }, { threshold: 0.2 });

    counterNodes.forEach((node) => observer.observe(node));
};

export const initScrollEffects = () => {
    const progressBar = document.getElementById('scroll-progress-bar');
    const backToTopButton = document.getElementById('back-to-top-btn');
    const header = document.querySelector('[data-site-header]');

    const onScroll = () => {
        const scrollTop = window.scrollY || document.documentElement.scrollTop;
        const documentHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;

        if (progressBar && documentHeight > 0) {
            progressBar.style.width = `${Math.min((scrollTop / documentHeight) * 100, 100)}%`;
        }

        if (backToTopButton) {
            const isShown = scrollTop > 350;
            backToTopButton.classList.toggle('opacity-0', !isShown);
            backToTopButton.classList.toggle('invisible', !isShown);
            backToTopButton.classList.toggle('translate-y-4', !isShown);
            backToTopButton.classList.toggle('pointer-events-none', !isShown);
        }

        if (header) {
            header.classList.toggle('is-scrolled', scrollTop > 8);
        }
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    backToTopButton?.addEventListener('click', (event) => {
        event.preventDefault();
        window.scrollTo({ top: 0, behavior: prefersReducedMotion() ? 'auto' : 'smooth' });
    });
};

/** Bring a form's flash message (success or validation errors) into view after the redirect back. */
export const initFlashFocus = () => {
    const flash = document.querySelector('[data-flash]');
    if (!flash) return;

    flash.setAttribute('tabindex', '-1');
    flash.scrollIntoView({ block: 'center', behavior: prefersReducedMotion() ? 'auto' : 'smooth' });
    flash.focus({ preventScroll: true });
};

export const initShareButtons = () => {
    document.querySelectorAll('[data-share]').forEach((button) => {
        button.addEventListener('click', async () => {
            const url = button.dataset.shareUrl || window.location.href;
            const title = button.dataset.shareTitle || document.title;

            if (navigator.share) {
                try {
                    await navigator.share({ title, url });
                } catch {
                    // The donor dismissed the native share sheet.
                }
                return;
            }

            await navigator.clipboard?.writeText(url);
            const label = button.querySelector('[data-share-label]');
            if (label && button.dataset.copiedText) {
                const original = label.textContent;
                label.textContent = button.dataset.copiedText;
                window.setTimeout(() => { label.textContent = original; }, 2000);
            }
        });
    });

    document.querySelectorAll('[data-print]').forEach((button) => {
        button.addEventListener('click', () => window.print());
    });
};
