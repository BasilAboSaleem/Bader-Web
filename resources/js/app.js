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

const initializeCountUp = () => {
    const counterNodes = document.querySelectorAll('[data-countup]');
    if (!counterNodes.length) return;

    if (prefersReducedMotion() || !('IntersectionObserver' in window)) {
        counterNodes.forEach((node) => {
            node.textContent = node.getAttribute('data-countup') || node.textContent;
        });
        return;
    }

    const parseValue = (text) => {
        const match = text.trim().match(/^([^\d]*)([\d,]+(?:\.\d+)?)(.*)$/);
        if (!match) return null;
        const prefix = match[1] || '';
        const rawNum = match[2].replace(/,/g, '');
        const suffix = match[3] || '';
        const num = parseFloat(rawNum);
        const hasCommas = match[2].includes(',');
        const decimals = rawNum.includes('.') ? rawNum.split('.')[1].length : 0;
        return { prefix, num, suffix, hasCommas, decimals };
    };

    const animateCount = (el, info) => {
        const duration = 1800;
        const start = performance.now();
        const startVal = 0;
        const targetVal = info.num;

        const easeOutExpo = (t) => (t === 1 ? 1 : 1 - Math.pow(2, -10 * t));

        const frame = (now) => {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            const current = startVal + (targetVal - startVal) * easeOutExpo(progress);

            let formattedNumber = current.toFixed(info.decimals);
            if (info.hasCommas) {
                const parts = formattedNumber.split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                formattedNumber = parts.join('.');
            }

            el.textContent = `${info.prefix}${formattedNumber}${info.suffix}`;

            if (progress < 1) {
                requestAnimationFrame(frame);
            } else {
                let finalFormatted = targetVal.toFixed(info.decimals);
                if (info.hasCommas) {
                    const parts = finalFormatted.split('.');
                    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    finalFormatted = parts.join('.');
                }
                el.textContent = `${info.prefix}${finalFormatted}${info.suffix}`;
            }
        };

        requestAnimationFrame(frame);
    };

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            const el = entry.target;
            const rawVal = el.getAttribute('data-countup') || el.textContent;
            const parsed = parseValue(rawVal);
            if (parsed && !isNaN(parsed.num)) {
                animateCount(el, parsed);
            }
            obs.unobserve(el);
        });
    }, { threshold: 0.2 });

    counterNodes.forEach((node) => observer.observe(node));
};

const initializeScrollEffects = () => {
    const progressBar = document.getElementById('scroll-progress-bar');
    const backToTopBtn = document.getElementById('back-to-top-btn');
    const header = document.querySelector('[data-site-header]');

    const onScroll = () => {
        const scrollTop = window.scrollY || document.documentElement.scrollTop;
        const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;

        if (progressBar && docHeight > 0) {
            const progress = Math.min((scrollTop / docHeight) * 100, 100);
            progressBar.style.width = `${progress}%`;
        }

        if (backToTopBtn) {
            if (scrollTop > 350) {
                backToTopBtn.classList.remove('opacity-0', 'invisible', 'translate-y-4', 'pointer-events-none');
                backToTopBtn.classList.add('opacity-100', 'visible', 'translate-y-0', 'pointer-events-auto');
            } else {
                backToTopBtn.classList.add('opacity-0', 'invisible', 'translate-y-4', 'pointer-events-none');
                backToTopBtn.classList.remove('opacity-100', 'visible', 'translate-y-0', 'pointer-events-auto');
            }
        }

        if (header) {
            if (scrollTop > 40) {
                header.classList.add('is-scrolled');
            } else {
                header.classList.remove('is-scrolled');
            }
        }
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    if (backToTopBtn) {
        backToTopBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
};

const initializePublicEnhancements = () => {
    reveal();
    initializeCountUp();
    initializeScrollEffects();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePublicEnhancements, { once: true });
} else {
    initializePublicEnhancements();
}
