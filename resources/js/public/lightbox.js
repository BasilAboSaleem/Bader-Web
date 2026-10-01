/**
 * Image lightbox: <a href="full.jpg" data-lightbox data-close-label="…"><img …></a>
 * Without JavaScript the link simply opens the image.
 */
export const initLightboxes = () => {
    const triggers = [...document.querySelectorAll('[data-lightbox]')];
    if (!triggers.length || typeof HTMLDialogElement === 'undefined') return;

    const dialog = document.createElement('dialog');
    dialog.className = 'lightbox';
    dialog.innerHTML = `
        <button type="button" class="lightbox-close" data-lightbox-close>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
        <img class="lightbox-image" alt="">
    `;
    document.body.appendChild(dialog);

    const image = dialog.querySelector('.lightbox-image');
    const closeButton = dialog.querySelector('[data-lightbox-close]');
    closeButton.setAttribute('aria-label', triggers[0].dataset.closeLabel || 'Close');

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            image.src = trigger.getAttribute('href');
            image.alt = trigger.querySelector('img')?.alt || '';
            dialog.showModal();
        });
    });

    closeButton.addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) dialog.close();
    });
    dialog.addEventListener('close', () => image.removeAttribute('src'));
};
