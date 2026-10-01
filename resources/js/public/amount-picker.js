import { buildUrl } from './utils';

/**
 * Donation amount pickers used by project cards, the quick-give bar and the donate form.
 *
 * Root: [data-amount-picker] (optional data-donate-base for link mode).
 * - [data-amount-option][data-amount] (optional data-category) preset chips
 * - [data-amount-input] custom amount field
 * - [data-frequency-option][data-value] once/monthly toggles
 * - [data-picker-field="amount|frequency|donation_category"] hidden inputs (form mode)
 * - [data-donate-link] anchors whose href is rebuilt from data-donate-base (link mode)
 * - [data-amount-display] text nodes mirroring the current amount
 */
const initPicker = (root) => {
    const options = [...root.querySelectorAll('[data-amount-option]')];
    const customInput = root.querySelector('[data-amount-input]');
    const frequencyOptions = [...root.querySelectorAll('[data-frequency-option]')];

    const state = {
        amount: root.dataset.amount || options.find((option) => option.getAttribute('aria-pressed') === 'true')?.dataset.amount || '',
        frequency: root.dataset.frequency || 'once',
        category: root.dataset.category || '',
    };

    const sync = () => {
        options.forEach((option) => {
            const isActive = option.dataset.amount === String(state.amount)
                && (!option.dataset.category || option.dataset.category === state.category);
            option.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });

        frequencyOptions.forEach((option) => {
            option.setAttribute('aria-pressed', option.dataset.value === state.frequency ? 'true' : 'false');
        });

        root.querySelectorAll('[data-picker-field]').forEach((field) => {
            const key = field.dataset.pickerField;
            if (key === 'amount') field.value = state.amount;
            if (key === 'frequency') field.value = state.frequency;
            if (key === 'donation_category' && state.category) field.value = state.category;
        });

        root.querySelectorAll('[data-amount-display]').forEach((node) => {
            node.textContent = state.amount || '—';
        });

        root.querySelectorAll('[data-frequency-display]').forEach((node) => {
            node.hidden = node.dataset.frequencyDisplay !== state.frequency;
        });

        if (root.dataset.donateBase) {
            const href = buildUrl(root.dataset.donateBase, {
                amount: state.amount,
                frequency: state.frequency,
                category: state.category,
            });
            root.querySelectorAll('[data-donate-link]').forEach((link) => { link.href = href; });
        }
    };

    options.forEach((option) => {
        option.addEventListener('click', (event) => {
            event.preventDefault();
            state.amount = option.dataset.amount;
            if (option.dataset.category) state.category = option.dataset.category;
            if (customInput) customInput.value = '';
            sync();
        });
    });

    customInput?.addEventListener('input', () => {
        const value = parseFloat(customInput.value);
        state.amount = Number.isFinite(value) && value > 0 ? String(value) : '';
        sync();
    });

    frequencyOptions.forEach((option) => {
        option.addEventListener('click', (event) => {
            event.preventDefault();
            state.frequency = option.dataset.value;
            sync();
        });
    });

    sync();
};

export const initAmountPickers = () => {
    document.querySelectorAll('[data-amount-picker]').forEach(initPicker);
};
