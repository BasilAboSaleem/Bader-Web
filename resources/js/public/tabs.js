import { isRtl, replayAnimations } from './utils';

/**
 * Accessible tabs: [data-tabs] > [role=tab][aria-controls] + [role=tabpanel][id].
 */
const initTabs = (root) => {
    const tabs = [...root.querySelectorAll('[role="tab"]')];
    if (!tabs.length) return;

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
