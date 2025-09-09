document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.cep-input').forEach(function(cepInput) {

        cepInput.addEventListener('blur', function() {

            const cep = this.value.replace(/\D/g, '');
            const form = this.closest('form');
            if (!form) return;

            const logradouroInput = form.querySelector('.logradouro-input');
            const bairroInput = form.querySelector('.bairro-input');
            const cidadeInput = form.querySelector('.cidade-input');
            const estadoInput = form.querySelector('.estado-input');

            // Limpa os campos de endereço
            logradouroInput.value = '';
            bairroInput.value = '';
            cidadeInput.value = '';
            estadoInput.value = '';

            if (cep.length !== 8) {
                return;
            }

            fetch(`/api/cep/${cep}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na requisição');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    logradouroInput.value = data.logradouro || '';
                    bairroInput.value = data.bairro || '';
                    cidadeInput.value = data.localidade || '';
                    estadoInput.value = data.uf || '';
                })
                .catch(() => alert('Erro ao buscar dados do CEP'));
        });
    });
});
