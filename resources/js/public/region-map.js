/**
 * Impact map: [data-region-map] holds the governorates ([data-region-area]), region markers
 * ([data-region-marker]), facility markers ([data-region-facility]) and detail panels
 * ([data-region-panel]). Any [data-region-pin] selects its region; "all" returns to the overview.
 * data-initial-region opens on a region; data-sync-url mirrors the selection in "?region=".
 */
const ZOOM = 1.7;

const initRegionMap = (root) => {
    const canvas = root.querySelector('[data-region-map-canvas]');
    const stage = root.querySelector('[data-region-map-stage]');
    const tooltip = root.querySelector('[data-region-tooltip]');
    const resetButton = root.querySelector('[data-region-reset]');
    const selectors = [...root.querySelectorAll('[data-region-pin]')];
    const markers = [...root.querySelectorAll('[data-region-marker]')];
    const areas = [...root.querySelectorAll('[data-region-area]')];
    const facilities = [...root.querySelectorAll('[data-region-facility]')];
    const panels = [...root.querySelectorAll('[data-region-panel]')];
    const canHover = window.matchMedia('(hover: hover)').matches;
    const markerFor = (region) => markers.find((marker) => marker.dataset.region === region);
    let activeRegion = 'all';

    const highlight = (region) => {
        [...areas, ...markers].forEach((node) => node.classList.toggle('is-hovered', node.dataset.region === region));
    };

    const hideTooltip = () => {
        if (tooltip) tooltip.hidden = true;
    };

    const showTooltip = (region) => {
        const marker = markerFor(region);
        if (!tooltip || !canHover || !marker || region === activeRegion) {
            hideTooltip();
            return;
        }

        tooltip.querySelector('[data-region-tooltip-title]').textContent = marker.dataset.name;
        tooltip.querySelector('[data-region-tooltip-stats]').replaceChildren(
            ...marker.dataset.stats.split('|').map((stat) => Object.assign(document.createElement('li'), { textContent: stat })),
        );

        const canvasBox = canvas.getBoundingClientRect();
        const markerBox = marker.querySelector('.region-pin-dot').getBoundingClientRect();
        tooltip.hidden = false;
        const left = markerBox.left + markerBox.width / 2 - canvasBox.left;
        const top = markerBox.top - canvasBox.top;
        tooltip.style.left = `${Math.min(Math.max(left, tooltip.offsetWidth / 2 + 8), canvasBox.width - tooltip.offsetWidth / 2 - 8)}px`;
        tooltip.style.top = `${Math.max(top, tooltip.offsetHeight + 12)}px`;
    };

    const zoomTo = (marker) => {
        if (!stage) return;

        if (!marker) {
            stage.style.transform = '';
            stage.style.setProperty('--map-zoom', '1');
            return;
        }

        const x = Number(marker.dataset.x);
        const y = Number(marker.dataset.y);
        stage.style.transform = `translate(${ZOOM * (50 - x)}%, ${ZOOM * (50 - y)}%) scale(${ZOOM})`;
        stage.style.setProperty('--map-zoom', String(ZOOM));
    };

    const syncUrl = () => {
        if (!root.hasAttribute('data-sync-url')) return;

        const url = new URL(window.location.href);
        if (activeRegion === 'all') {
            url.searchParams.delete('region');
        } else {
            url.searchParams.set('region', activeRegion);
        }
        window.history.replaceState(window.history.state, '', url);
    };

    const select = (region, { updateUrl = true } = {}) => {
        const hasPanel = panels.some((panel) => panel.dataset.region === region);
        activeRegion = hasPanel ? region : 'all';

        selectors.forEach((selector) => {
            const isActive = selector.dataset.region === activeRegion;
            selector.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            selector.classList.toggle('is-active', isActive);
        });
        areas.forEach((area) => area.classList.toggle('is-active', area.dataset.region === activeRegion));
        facilities.forEach((facility) => {
            const isVisible = facility.dataset.region === activeRegion;
            facility.classList.toggle('is-visible', isVisible);
            facility.tabIndex = isVisible ? 0 : -1;
            facility.setAttribute('aria-hidden', isVisible ? 'false' : 'true');
        });
        panels.forEach((panel) => {
            panel.hidden = panel.dataset.region !== activeRegion;
        });

        canvas?.classList.toggle('is-zoomed', activeRegion !== 'all');
        if (resetButton) resetButton.hidden = activeRegion === 'all';
        zoomTo(activeRegion === 'all' ? null : markerFor(activeRegion));
        hideTooltip();
        if (updateUrl) syncUrl();
    };

    selectors.forEach((selector) => {
        selector.addEventListener('click', () => select(selector.dataset.region));
    });

    [...areas, ...markers, ...selectors].forEach((node) => {
        node.addEventListener('pointerenter', () => {
            highlight(node.dataset.region);
            if (node.matches('[data-region-area], [data-region-marker]')) showTooltip(node.dataset.region);
        });
        node.addEventListener('pointerleave', () => {
            highlight(null);
            hideTooltip();
        });
    });

    areas.forEach((area) => area.addEventListener('click', () => select(area.dataset.region)));
    resetButton?.addEventListener('click', () => select('all'));
    root.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && activeRegion !== 'all') select('all');
    });

    select(root.dataset.initialRegion || 'all', { updateUrl: false });
};

export const initRegionMaps = () => {
    document.querySelectorAll('[data-region-map]').forEach(initRegionMap);
};
