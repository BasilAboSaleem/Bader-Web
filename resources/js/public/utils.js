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

export const formatMoney = (value, locale = document.documentElement.lang || 'en') => {
    const numberLocale = locale.startsWith('ar') ? 'ar-EG' : 'en-US';

    return new Intl.NumberFormat(numberLocale, { maximumFractionDigits: 2 }).format(value);
};

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
