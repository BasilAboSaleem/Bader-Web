// ─── TailAdmin Theme Bootstrap (runs before body renders) ─────────────────
// The <script> inline in each page head does this synchronously,
// but we also guard here in case of JS-only navigation.
(function () {
    const saved       = localStorage.getItem('tailadmin-theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (saved === 'dark' || (!saved && prefersDark)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }

    const savedDir = localStorage.getItem('tailadmin-dir');
    if (savedDir) {
        document.documentElement.setAttribute('dir', savedDir);
    }
})();

// ─── Public site: reveal animation ────────────────────────────────────────
const prefersReducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

document.documentElement.classList.add('js');

const reveal = () => {
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

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', reveal, { once: true });
} else {
    reveal();
}
