<script>
document.querySelectorAll('[data-profile-input]').forEach((input) => input.addEventListener('change', () => {
    const file = input.files?.[0];
    const preview = input.closest('form')?.querySelector('[data-profile-preview]');
    if (!file || !preview) return;

    const previousUrl = preview.dataset.previewUrl;
    if (previousUrl) URL.revokeObjectURL(previousUrl);
    preview.innerHTML = '';
    const image = document.createElement('img');
    image.src = URL.createObjectURL(file);
    image.alt = 'Preview foto profil yang dipilih';
    image.onload = () => URL.revokeObjectURL(image.src);
    preview.dataset.previewUrl = image.src;
    preview.appendChild(image);
}));
</script>
