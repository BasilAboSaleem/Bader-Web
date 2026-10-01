import { initCountUp, initFlashFocus, initReveal, initScrollEffects, initShareButtons } from './public/effects';
import { initSliders } from './public/slider';
import { initAllTabs } from './public/tabs';
import { initNavigation } from './public/mega-menu';
import { initAmountPickers } from './public/amount-picker';
import { initZakatCalculators } from './public/zakat';
import { initGiftDesigners } from './public/gift';
import { initRegionMaps } from './public/region-map';
import { initLightboxes } from './public/lightbox';

const isPublicSite = () => document.body?.classList.contains('bader-public');

// ─── TailAdmin Theme Bootstrap (dashboard only) ───────────────────────────
// The public site always renders in its own light theme and locale direction.
(function () {
    if (isPublicSite()) return;

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

document.documentElement.classList.add('js');

const initializePublicEnhancements = () => {
    initReveal();
    initCountUp();
    initScrollEffects();
    initShareButtons();
    initNavigation();
    initSliders();
    initAllTabs();
    initAmountPickers();
    initZakatCalculators();
    initGiftDesigners();
    initRegionMaps();
    initLightboxes();
    initFlashFocus();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePublicEnhancements, { once: true });
} else {
    initializePublicEnhancements();
}
