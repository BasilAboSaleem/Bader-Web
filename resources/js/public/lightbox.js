/**
 * Image lightbox: <a href="full.jpg" data-lightbox="group" data-close-label="…"><img …></a>
 * Links sharing a non-empty data-lightbox value form a gallery with previous/next navigation.
 * Without JavaScript the link simply opens the image.
 */
const initGalleryExpanders = () => {
    document.querySelectorAll('[data-gallery]').forEach((gallery) => {
        const button = gallery.querySelector('[data-gallery-more]');
        if (!button) return;

        button.addEventListener('click', () => {
            gallery.querySelectorAll('[data-gallery-extra]').forEach((item) => item.removeAttribute('hidden'));
            button.parentElement.remove();
        });
    });
};

export const initLightboxes = () => {
    initGalleryExpanders();

    const triggers = [...document.querySelectorAll('[data-lightbox]')];
    if (!triggers.length || typeof HTMLDialogElement === 'undefined') return;

    const isRtl = document.documentElement.dir === 'rtl';
    const chevron = (points) => `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="${points}"/></svg>`;
    const backChevron = isRtl ? 'm9 18 6-6-6-6' : 'm15 18-6-6 6-6';
    const forwardChevron = isRtl ? 'm15 18-6-6 6-6' : 'm9 18 6-6-6-6';

    const dialog = document.createElement('dialog');
    dialog.className = 'lightbox';
    dialog.innerHTML = `
        <button type="button" class="lightbox-close" data-lightbox-close>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
        <button type="button" class="lightbox-nav lightbox-prev" data-lightbox-prev hidden>${chevron(backChevron)}</button>
        <img class="lightbox-image" alt="">
        <button type="button" class="lightbox-nav lightbox-next" data-lightbox-next hidden>${chevron(forwardChevron)}</button>
        <p class="lightbox-counter" data-lightbox-counter hidden></p>
    `;
    document.body.appendChild(dialog);

    const image = dialog.querySelector('.lightbox-image');
    const closeButton = dialog.querySelector('[data-lightbox-close]');
    const prevButton = dialog.querySelector('[data-lightbox-prev]');
    const nextButton = dialog.querySelector('[data-lightbox-next]');
    const counter = dialog.querySelector('[data-lightbox-counter]');
    closeButton.setAttribute('aria-label', triggers[0].dataset.closeLabel || 'Close');

    let group = [];
    let index = 0;

    const show = (position) => {
        index = (position + group.length) % group.length;
        const trigger = group[index];
        image.src = trigger.getAttribute('href');
        image.alt = trigger.querySelector('img')?.alt || '';

        const hasSiblings = group.length > 1;
        prevButton.hidden = !hasSiblings;
        nextButton.hidden = !hasSiblings;
        counter.hidden = !hasSiblings;
        counter.textContent = `${index + 1} / ${group.length}`;
        prevButton.setAttribute('aria-label', trigger.dataset.prevLabel || 'Previous');
        nextButton.setAttribute('aria-label', trigger.dataset.nextLabel || 'Next');
    };

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            const groupName = trigger.dataset.lightbox;
            group = groupName ? triggers.filter((item) => item.dataset.lightbox === groupName) : [trigger];
            show(group.indexOf(trigger));
            dialog.showModal();
        });
    });

    prevButton.addEventListener('click', () => show(index - 1));
    nextButton.addEventListener('click', () => show(index + 1));
    dialog.addEventListener('keydown', (event) => {
        if (group.length < 2 || (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight')) return;
        event.preventDefault();
        const goesForward = (event.key === 'ArrowLeft') === isRtl;
        show(index + (goesForward ? 1 : -1));
    });

    closeButton.addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) dialog.close();
    });
    dialog.addEventListener('close', () => image.removeAttribute('src'));
};
