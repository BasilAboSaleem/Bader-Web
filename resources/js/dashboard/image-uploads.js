/**
 * Shrinks large photos picked in dashboard file inputs before the form is sent, so phone photos
 * stay under the server's upload limit (often 2 MB). Small files and non-images pass through untouched.
 */
const SIZE_LIMIT = 1.5 * 1024 * 1024;
const MAX_EDGE = 2000;
const RESIZABLE_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

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

const shrink = async (file) => {
    if (!RESIZABLE_TYPES.includes(file.type) || file.size <= SIZE_LIMIT) return file;

    const image = await loadImage(file);
    const scale = Math.min(1, MAX_EDGE / Math.max(image.naturalWidth, image.naturalHeight));
    const canvas = document.createElement('canvas');
    canvas.width = Math.round(image.naturalWidth * scale);
    canvas.height = Math.round(image.naturalHeight * scale);
    canvas.getContext('2d').drawImage(image, 0, 0, canvas.width, canvas.height);

    const keepsTransparency = file.type === 'image/png';
    const type = keepsTransparency ? 'image/png' : 'image/jpeg';
    const blob = await new Promise((resolve) => canvas.toBlob(resolve, type, 0.85));
    if (!blob || blob.size >= file.size) return file;

    const name = file.name.replace(/\.[^.]+$/, '') + (keepsTransparency ? '.png' : '.jpg');

    return new File([blob], name, { type, lastModified: Date.now() });
};

const handleChange = async (input) => {
    const files = [...(input.files ?? [])];
    if (!files.some((file) => RESIZABLE_TYPES.includes(file.type) && file.size > SIZE_LIMIT)) return;

    const form = input.form;
    const submitButtons = form ? [...form.querySelectorAll('[type="submit"]')] : [];
    submitButtons.forEach((button) => { button.disabled = true; });

    try {
        const resized = await Promise.all(files.map((file) => shrink(file).catch(() => file)));
        const transfer = new DataTransfer();
        resized.forEach((file) => transfer.items.add(file));
        input.files = transfer.files;
    } finally {
        submitButtons.forEach((button) => { button.disabled = false; });
    }
};

export const initImageUploads = () => {
    if (typeof DataTransfer === 'undefined') return;

    document.querySelectorAll('input[type="file"]').forEach((input) => {
        input.addEventListener('change', () => handleChange(input));
    });
};
