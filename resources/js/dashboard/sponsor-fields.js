/**
 * Sponsor details: inside each [data-sponsor-scope], the [data-sponsor-fields] fieldset opens only while the
 * [data-sponsor-status] select is on "sponsored"; while closed it is disabled, so its required inputs do not block saving.
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
    });
};
