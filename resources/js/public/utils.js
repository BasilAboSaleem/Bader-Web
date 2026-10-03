export const prefersReducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export const isRtl = () => document.documentElement.getAttribute('dir') === 'rtl';

/**
 * Restart CSS animations on elements marked with [data-replay] inside a container.
 */
export const replayAnimations = (container) => {
    container.querySelectorAll('[data-replay]').forEach((element) => {
        element.style.animation = 'none';
        void element.offsetHeight;
        element.style.animation = '';
    });
};

/**
 * Numbers always use Western (English) digits, in both the Arabic and English interfaces.
 */
export const formatMoney = (value) => new Intl.NumberFormat('en-US', { maximumFractionDigits: 2 }).format(value);

export const buildUrl = (base, params) => {
    const url = new URL(base, window.location.origin);

    Object.entries(params).forEach(([key, value]) => {
        if (value === null || value === undefined || value === '') {
            url.searchParams.delete(key);
        } else {
            url.searchParams.set(key, value);
        }
    });

    return url.toString();
};
