/**
 * Sponsor details: inside each [data-sponsor-scope], the [data-sponsor-fields] fieldset opens only while the
 * [data-sponsor-status] select is on "sponsored"; while closed it is disabled, so its required inputs do not block saving.
 * Picking a sponsor switches the currency to the one they usually pay in, until the currency is chosen by hand.
 */
export const initSponsorFields = () => {
    document.querySelectorAll('[data-sponsor-scope]').forEach((scope) => {
        const status = scope.querySelector('[data-sponsor-status]');
        const fields = scope.querySelector('[data-sponsor-fields]');
        if (!status || !fields) return;

        const sync = () => {
            const isSponsored = status.value === 'sponsored';
            fields.hidden = !isSponsored;
            fields.disabled = !isSponsored;
        };

        status.addEventListener('change', sync);
        sync();

        const sponsor = scope.querySelector('[data-sponsor-select]');
        const currency = scope.querySelector('[data-currency-select]');
        if (!sponsor || !currency) return;

        let currencyChosen = currency.dataset.currencyChosen === 'true';
        currency.addEventListener('change', () => { currencyChosen = true; });
        sponsor.addEventListener('change', () => {
            const preferred = sponsor.selectedOptions[0]?.dataset.currency;
            if (!currencyChosen && preferred) currency.value = preferred;
        });

        const renewal = scope.querySelector('[data-renewal-type]');
        const plannedEnd = scope.querySelector('[data-planned-end] input');
        if (renewal && plannedEnd) {
            const syncRenewal = () => {
                plannedEnd.required = renewal.value === 'one_time';
            };
            renewal.addEventListener('change', syncRenewal);
            syncRenewal();
        }
    });
};
