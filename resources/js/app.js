const prefersReducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const reveal = () => {
    const nodes = document.querySelectorAll('[data-reveal]');

    if (! nodes.length) {
        return;
    }

    if (prefersReducedMotion() || ! ('IntersectionObserver' in window)) {
        nodes.forEach((node) => node.classList.add('is-visible'));

        return;
    }

    const observer = new IntersectionObserver((entries, current) => {
        entries.forEach((entry) => {
            if (! entry.isIntersecting) {
                return;
            }

            entry.target.classList.add('is-visible');
            current.unobserve(entry.target);
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -8% 0px',
    });

    nodes.forEach((node) => observer.observe(node));
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', reveal, { once: true });
} else {
    reveal();
}
