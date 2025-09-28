import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

window.previewUserImage = window.previewImage = function(event, id) { // função para preview da imagem nos modais
    const input = event.target; // input file
    const previewId = `preview-photo-${id}`; // id do elemento de preview
    const preview = document.getElementById(previewId); // elemento de preview

    if (!preview) { // Coloquei pra debug e resolvi manter
        console.warn('Elemento de preview não encontrado:', previewId);
        return;
    }

    if (input.files && input.files[0]) {
        const reader = new FileReader(); // lê o arquivo
        reader.onload = function(e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        // placeholder se não tiver arquivo
        preview.src = 'https://via.placeholder.com/150';
    }
};
