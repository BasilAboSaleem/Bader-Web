import { isRtl, replayAnimations } from './utils';

const loadPanelImages = (panel) => {
    panel?.querySelectorAll('img[loading="lazy"]').forEach((image) => {
        image.loading = 'eager';
    });
};

/**
 * Hidden panels keep their lazy images unloaded, so fetch them once the tabs come near the viewport
 * (and as soon as a tab is hovered or focused) to have them ready by the time a tab is opened.
 */
const preloadPanels = (root, tabs) => {
    const panelOf = (tab) => document.getElementById(tab.getAttribute('aria-controls'));

    tabs.forEach((tab) => {
        ['pointerenter', 'focus', 'touchstart'].forEach((type) => tab.addEventListener(type, () => loadPanelImages(panelOf(tab)), { once: true, passive: true }));
    });

    if (!('IntersectionObserver' in window)) return;

    const observer = new IntersectionObserver((entries) => {
        if (!entries.some((entry) => entry.isIntersecting)) return;
        observer.disconnect();
        tabs.forEach((tab) => loadPanelImages(panelOf(tab)));
    }, { rootMargin: '600px 0px' });

    observer.observe(root);
};

/**
 * Accessible tabs: [data-tabs] > [role=tab][aria-controls] + [role=tabpanel][id].
 */
const initTabs = (root) => {
    const tabs = [...root.querySelectorAll('[role="tab"]')];
    if (!tabs.length) return;

    preloadPanels(root, tabs);

    const activate = (tab, focus = false) => {
        tabs.forEach((item) => {
            const isActive = item === tab;
            item.setAttribute('aria-selected', isActive ? 'true' : 'false');
            item.tabIndex = isActive ? 0 : -1;
            item.classList.toggle('is-active', isActive);

            const panel = document.getElementById(item.getAttribute('aria-controls'));
            if (panel) {
                panel.hidden = !isActive;
                if (isActive) replayAnimations(panel);
            }
        });

        if (focus) tab.focus();
    };

    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => activate(tab));
        tab.addEventListener('keydown', (event) => {
            const keys = isRtl() ? { next: 'ArrowLeft', prev: 'ArrowRight' } : { next: 'ArrowRight', prev: 'ArrowLeft' };
            let target = null;
            if (event.key === keys.next) target = tabs[(index + 1) % tabs.length];
            if (event.key === keys.prev) target = tabs[(index - 1 + tabs.length) % tabs.length];
            if (event.key === 'Home') target = tabs[0];
            if (event.key === 'End') target = tabs[tabs.length - 1];
            if (target) {
                event.preventDefault();
                activate(target, true);
            }
        });
    });

    activate(tabs.find((tab) => tab.getAttribute('aria-selected') === 'true') || tabs[0]);
};

export const initAllTabs = () => {
    document.querySelectorAll('[data-tabs]').forEach(initTabs);
};
