/**
 * Orphan sponsorship form: [data-orphan-form].
 * - [data-orphan-type] radios show the matching [data-show-for-type] fieldset and disable the others,
 *   so hidden fields are neither required nor sent
 * - [data-orphan-count] grows or shrinks [data-orphan-list] using template[data-orphan-template],
 *   whose markup carries __INDEX__ / __NUMBER__ placeholders; [data-orphan-remove] drops one orphan
 * - file inputs with data-max-bytes block the submit while a chosen file is still too large
 * - submit buttons with data-sending-label lock while the upload is on its way, so a slow
 *   connection does not tempt a second tap
 */
const initOrphanForm = (root) => {
    const list = root.querySelector('[data-orphan-list]');
    const template = root.querySelector('template[data-orphan-template]');
    const countInput = root.querySelector('[data-orphan-count]');
    const maxCount = Number(countInput?.max) || 15;
    let nextIndex = list ? list.querySelectorAll('[data-orphan-item]').length : 0;

    const syncType = () => {
        const selected = root.querySelector('[data-orphan-type]:checked')?.value;
        root.querySelectorAll('[data-show-for-type]').forEach((fieldset) => {
            const isActive = fieldset.dataset.showForType === selected;
            fieldset.hidden = !isActive;
            fieldset.disabled = !isActive;
        });
    };

    const items = () => [...list.querySelectorAll('[data-orphan-item]')];

    const renumber = () => {
        const current = items();
        current.forEach((item, position) => {
            const number = item.querySelector('[data-orphan-number]');
            if (number) number.textContent = String(position + 1);
            const remove = item.querySelector('[data-orphan-remove]');
            if (remove) remove.hidden = current.length === 1;
        });
        if (countInput) countInput.value = String(current.length);
    };

    const addItem = () => {
        const markup = template.innerHTML.replaceAll('__INDEX__', String(nextIndex)).replaceAll('__NUMBER__', '');
        nextIndex += 1;
        list.insertAdjacentHTML('beforeend', markup);
    };

    const resizeTo = (wanted) => {
        const target = Math.min(maxCount, Math.max(1, Math.round(wanted) || 1));
        while (items().length < target) addItem();
        while (items().length > target) items().at(-1).remove();
        renumber();
    };

    root.querySelectorAll('[data-orphan-type]').forEach((radio) => radio.addEventListener('change', syncType));
    countInput?.addEventListener('change', () => resizeTo(Number(countInput.value)));
    list?.addEventListener('click', (event) => {
        const remove = event.target.closest('[data-orphan-remove]');
        if (!remove || items().length === 1) return;
        remove.closest('[data-orphan-item]').remove();
        renumber();
    });

    const form = root.closest('form');
    const sendingButtons = form ? [...form.querySelectorAll('button[data-sending-label]')] : [];
    sendingButtons.forEach((button) => { button.dataset.idleLabel = button.textContent; });
    const setSending = (isSending) => sendingButtons.forEach((button) => {
        button.disabled = isSending;
        button.textContent = isSending ? button.dataset.sendingLabel : button.dataset.idleLabel;
    });

    form?.addEventListener('submit', (event) => {
        const tooLarge = [...root.querySelectorAll('input[type="file"][data-max-bytes]')]
            .find((input) => [...(input.files ?? [])].some((file) => file.size > Number(input.dataset.maxBytes)));
        if (!tooLarge) {
            setSending(true);
            return;
        }

        event.preventDefault();
        tooLarge.setCustomValidity(root.dataset.tooLargeMessage || '');
        tooLarge.reportValidity();
        tooLarge.addEventListener('change', () => tooLarge.setCustomValidity(''), { once: true });
    });
    window.addEventListener('pageshow', (event) => { if (event.persisted) setSending(false); });

    syncType();
    if (list && template) renumber();
};

export const initOrphanForms = () => {
    document.querySelectorAll('[data-orphan-form]').forEach(initOrphanForm);
};
