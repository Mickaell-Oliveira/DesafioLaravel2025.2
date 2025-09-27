import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

window.previewUserImage = window.previewImage = function(event, id) {
    const input = event.target;
    const previewId = `preview-photo-${id}`;
    const preview = document.getElementById(previewId);

    if (!preview) {
        console.warn('Elemento de preview não encontrado:', previewId);
        return;
    }

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        // fallback — placeholder se não tiver arquivo
        preview.src = 'https://via.placeholder.com/150';
    }
};
