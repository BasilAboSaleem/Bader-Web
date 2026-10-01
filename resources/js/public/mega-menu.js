/**
 * Desktop mega menus: [data-mega] > [data-mega-trigger] + [data-mega-panel].
 * Mobile drawer: [data-drawer-open] opens [data-drawer]; [data-drawer-close] closes it.
 */
const initMegaMenus = () => {
    const menus = [...document.querySelectorAll('[data-mega]')];
    if (!menus.length) return;

    const hoverQuery = window.matchMedia('(hover: hover) and (min-width: 1024px)');

    const setOpen = (menu, isOpen) => {
        const trigger = menu.querySelector('[data-mega-trigger]');
        const panel = menu.querySelector('[data-mega-panel]');
        if (!trigger || !panel) return;
        trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        panel.hidden = !isOpen;
        menu.classList.toggle('is-open', isOpen);
    };

    const closeAll = (except = null) => menus.forEach((menu) => menu !== except && setOpen(menu, false));

    menus.forEach((menu) => {
        const trigger = menu.querySelector('[data-mega-trigger]');
        let closeTimer = null;

        trigger?.addEventListener('click', () => {
            const willOpen = trigger.getAttribute('aria-expanded') !== 'true';
            closeAll(menu);
            setOpen(menu, willOpen);
        });

        menu.addEventListener('pointerenter', () => {
            if (!hoverQuery.matches) return;
            window.clearTimeout(closeTimer);
            closeAll(menu);
            setOpen(menu, true);
        });

        menu.addEventListener('pointerleave', () => {
            if (!hoverQuery.matches) return;
            closeTimer = window.setTimeout(() => setOpen(menu, false), 160);
        });

        menu.addEventListener('focusout', (event) => {
            if (!menu.contains(event.relatedTarget)) setOpen(menu, false);
        });
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('[data-mega]')) closeAll();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        const openMenu = menus.find((menu) => menu.classList.contains('is-open'));
        if (openMenu) {
            setOpen(openMenu, false);
            openMenu.querySelector('[data-mega-trigger]')?.focus();
        }
    });
};

const initDrawer = () => {
    const drawer = document.querySelector('[data-drawer]');
    if (!drawer) return;

    const openButtons = document.querySelectorAll('[data-drawer-open]');
    let lastFocused = null;

    const setOpen = (isOpen) => {
        drawer.hidden = !isOpen;
        document.documentElement.classList.toggle('overflow-hidden', isOpen);
        openButtons.forEach((button) => button.setAttribute('aria-expanded', isOpen ? 'true' : 'false'));

        if (isOpen) {
            lastFocused = document.activeElement;
            requestAnimationFrame(() => {
                drawer.classList.add('is-open');
                drawer.querySelector('[data-drawer-close]')?.focus();
            });
        } else {
            drawer.classList.remove('is-open');
            lastFocused?.focus();
        }
    };

    openButtons.forEach((button) => button.addEventListener('click', () => setOpen(true)));
    drawer.querySelectorAll('[data-drawer-close]').forEach((button) => button.addEventListener('click', () => setOpen(false)));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !drawer.hidden) setOpen(false);
    });
};

export const initNavigation = () => {
    initMegaMenus();
    initDrawer();
};
