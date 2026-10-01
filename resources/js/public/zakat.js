import { buildUrl, formatMoney } from './utils';

const ZAKAT_RATE = 0.025;
const NISAB_GOLD_GRAMS = 85;

/**
 * Zakat calculator: [data-zakat][data-gold-price][data-donate-base].
 * Modes via [data-zakat-mode][data-value="gold|money|both"]; sections via [data-zakat-section="gold|money"].
 * Inputs: [data-zakat-input="gold_grams|gold_karat|cash|savings|trade|debts"].
 * Outputs: [data-zakat-output="nisab|wealth|due"], [data-zakat-status="below|above"], [data-zakat-pay] link.
 */
const initCalculator = (root) => {
    const goldPrice = parseFloat(root.dataset.goldPrice || '0');
    const nisab = goldPrice * NISAB_GOLD_GRAMS;
    let mode = root.dataset.mode || 'money';

    const read = (name) => {
        const input = root.querySelector(`[data-zakat-input="${name}"]`);
        const value = parseFloat(input?.value || '0');
        return Number.isFinite(value) && value > 0 ? value : 0;
    };

    const calculate = () => {
        const includesGold = mode === 'gold' || mode === 'both';
        const includesMoney = mode === 'money' || mode === 'both';

        const karat = read('gold_karat') || 24;
        const goldValue = includesGold ? read('gold_grams') * (karat / 24) * goldPrice : 0;
        const moneyValue = includesMoney ? Math.max(read('cash') + read('savings') + read('trade') - read('debts'), 0) : 0;
        const wealth = goldValue + moneyValue;
        const isAboveNisab = nisab > 0 && wealth >= nisab;
        const due = isAboveNisab ? Math.round(wealth * ZAKAT_RATE * 100) / 100 : 0;

        root.querySelectorAll('[data-zakat-section]').forEach((section) => {
            const key = section.dataset.zakatSection;
            section.hidden = !(key === 'gold' ? includesGold : includesMoney);
        });

        const outputs = { nisab, wealth, due };
        root.querySelectorAll('[data-zakat-output]').forEach((node) => {
            node.textContent = formatMoney(outputs[node.dataset.zakatOutput] || 0);
        });

        root.querySelectorAll('[data-zakat-status]').forEach((node) => {
            const showWhenAbove = node.dataset.zakatStatus === 'above';
            node.hidden = wealth === 0 || showWhenAbove !== isAboveNisab;
        });

        root.querySelectorAll('[data-zakat-pay]').forEach((link) => {
            link.href = buildUrl(root.dataset.donateBase, { amount: due || null, category: 'zakat' });
            link.classList.toggle('pointer-events-none', due === 0);
            link.classList.toggle('opacity-50', due === 0);
            link.setAttribute('aria-disabled', due === 0 ? 'true' : 'false');
        });
    };

    root.querySelectorAll('[data-zakat-mode]').forEach((button) => {
        button.addEventListener('click', () => {
            mode = button.dataset.value;
            root.querySelectorAll('[data-zakat-mode]').forEach((item) => {
                item.setAttribute('aria-pressed', item === button ? 'true' : 'false');
            });
            calculate();
        });
    });

    root.querySelectorAll('[data-zakat-input]').forEach((input) => input.addEventListener('input', calculate));

    calculate();
};

export const initZakatCalculators = () => {
    document.querySelectorAll('[data-zakat]').forEach(initCalculator);
};
