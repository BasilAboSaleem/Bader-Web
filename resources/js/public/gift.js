/**
 * Gift card designer: [data-gift].
 * - [data-gift-design][value] radio inputs select the card theme
 * - [data-gift-preview] receives data-design and the --gift-from/--gift-to/--gift-accent colors
 *   taken from the radio's data-from/data-to/data-accent
 * - [data-gift-bind="name"] inputs mirror their value into [data-gift-out="name"] (falls back to data-placeholder)
 */
const initGift = (root) => {
    const preview = root.querySelector('[data-gift-preview]');

    const syncDesign = () => {
        const selected = root.querySelector('[data-gift-design]:checked');
        if (!selected || !preview) return;
        preview.dataset.design = selected.value;
        ['from', 'to', 'accent'].forEach((tone) => {
            if (selected.dataset[tone]) preview.style.setProperty(`--gift-${tone}`, selected.dataset[tone]);
        });
        root.querySelectorAll('[data-gift-design-label]').forEach((node) => {
            node.textContent = selected.dataset.label || '';
        });
    };

    const syncText = (input) => {
        root.querySelectorAll(`[data-gift-out="${input.dataset.giftBind}"]`).forEach((node) => {
            node.textContent = input.value.trim() || node.dataset.placeholder || '';
        });
    };

    root.querySelectorAll('[data-gift-design]').forEach((input) => input.addEventListener('change', syncDesign));
    root.querySelectorAll('[data-gift-bind]').forEach((input) => {
        input.addEventListener('input', () => syncText(input));
        syncText(input);
    });

    syncDesign();
};

export const initGiftDesigners = () => {
    document.querySelectorAll('[data-gift]').forEach(initGift);
};
