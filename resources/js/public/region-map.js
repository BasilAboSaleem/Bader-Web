/**
 * Region map: [data-region-map] with [data-region-pin][data-region] pins and
 * [data-region-panel][data-region] detail panels. "all" shows the overview panel.
 */
const initRegionMap = (root) => {
    const pins = [...root.querySelectorAll('[data-region-pin]')];
    const panels = [...root.querySelectorAll('[data-region-panel]')];

    const select = (region) => {
        pins.forEach((pin) => {
            const isActive = pin.dataset.region === region;
            pin.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            pin.classList.toggle('is-active', isActive);
        });

        const hasPanel = panels.some((panel) => panel.dataset.region === region);
        panels.forEach((panel) => {
            panel.hidden = panel.dataset.region !== (hasPanel ? region : 'all');
        });
    };

    pins.forEach((pin) => {
        pin.addEventListener('click', () => select(pin.dataset.region));
        pin.addEventListener('pointerenter', () => pin.classList.add('is-hovered'));
        pin.addEventListener('pointerleave', () => pin.classList.remove('is-hovered'));
    });

    select('all');
};

export const initRegionMaps = () => {
    document.querySelectorAll('[data-region-map]').forEach(initRegionMap);
};
