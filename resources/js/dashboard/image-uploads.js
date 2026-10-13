/**
 * Shrinks large photos picked in file inputs before the form is sent, so phone photos stay under the
 * server's upload limit (often 2 MB). An input's data-max-bytes sets its own limit; the image is
 * re-encoded with stronger compression until it fits. Small files and non-images pass through untouched.
 */
const SIZE_LIMIT = 1.5 * 1024 * 1024;
const RESIZABLE_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
const ATTEMPTS = [
    { edge: 2000, quality: 0.85 },
    { edge: 2000, quality: 0.75 },
    { edge: 1600, quality: 0.7 },
    { edge: 1280, quality: 0.65 },
    { edge: 1024, quality: 0.6 },
];

const loadImage = (file) => new Promise((resolve, reject) => {
    const url = URL.createObjectURL(file);
    const image = new Image();
    image.onload = () => {
        URL.revokeObjectURL(url);
        resolve(image);
    };
    image.onerror = () => {
        URL.revokeObjectURL(url);
        reject(new Error('unreadable image'));
    };
    image.src = url;
});

const encode = (image, edge, type, quality) => {
    const scale = Math.min(1, edge / Math.max(image.naturalWidth, image.naturalHeight));
    const canvas = document.createElement('canvas');
    canvas.width = Math.round(image.naturalWidth * scale);
    canvas.height = Math.round(image.naturalHeight * scale);
    const context = canvas.getContext('2d');
    if (type === 'image/jpeg') {
        context.fillStyle = '#ffffff';
        context.fillRect(0, 0, canvas.width, canvas.height);
    }
    context.drawImage(image, 0, 0, canvas.width, canvas.height);

    return new Promise((resolve) => canvas.toBlob(resolve, type, quality));
};

const shrink = async (file, limit) => {
    if (!RESIZABLE_TYPES.includes(file.type) || file.size <= limit) return file;

    const image = await loadImage(file);
    const baseName = file.name.replace(/\.[^.]+$/, '');

    if (file.type === 'image/png') {
        const png = await encode(image, ATTEMPTS[0].edge, 'image/png');
        if (png && png.size <= limit && png.size < file.size) {
            return new File([png], `${baseName}.png`, { type: 'image/png', lastModified: Date.now() });
        }
    }

    let best = null;
    for (const { edge, quality } of ATTEMPTS) {
        const blob = await encode(image, edge, 'image/jpeg', quality);
        if (blob && (!best || blob.size < best.size)) best = blob;
        if (best && best.size <= limit) break;
    }

    if (!best || best.size >= file.size) return file;

    return new File([best], `${baseName}.jpg`, { type: 'image/jpeg', lastModified: Date.now() });
};

const handleChange = async (input) => {
    const limit = Number(input.dataset.maxBytes) || SIZE_LIMIT;
    const files = [...(input.files ?? [])];
    if (!files.some((file) => RESIZABLE_TYPES.includes(file.type) && file.size > limit)) return;

    const form = input.form;
    const submitButtons = form ? [...form.querySelectorAll('[type="submit"]')] : [];
    submitButtons.forEach((button) => { button.disabled = true; });

    try {
        const resized = await Promise.all(files.map((file) => shrink(file, limit).catch(() => file)));
        const transfer = new DataTransfer();
        resized.forEach((file) => transfer.items.add(file));
        input.files = transfer.files;
    } finally {
        submitButtons.forEach((button) => { button.disabled = false; });
    }
};

export const initImageUploads = (selector = 'input[type="file"]') => {
    if (typeof DataTransfer === 'undefined') return;

    document.querySelectorAll(selector).forEach((input) => {
        input.addEventListener('change', () => handleChange(input));
    });
};
