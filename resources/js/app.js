import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

//js para fechar a sidebar apenas ao clicar fora dela
document.addEventListener('DOMContentLoaded', function () {
    const body = document.body;
    const sidebar = document.querySelector('.main-sidebar');
    const contentWrapper = document.querySelector('.content-wrapper');

    // Remove a classe ao carregar a página
    if(body.classList.contains('sidebar-collapse')) {
        body.classList.remove('sidebar-collapse');
    }

    // Fecha a sidebar ao clicar fora dela
    if (sidebar && contentWrapper) {
        contentWrapper.addEventListener('click', function (e) {
            // Só fecha se a sidebar estiver aberta
            if (!body.classList.contains('sidebar-collapse')) {
                body.classList.add('sidebar-collapse');
            }
        });

        // Clicar na sidebar não fecha ela
        sidebar.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    }
});
