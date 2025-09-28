document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.cep-input').forEach(function(cepInput) { // função para buscar o endereço pelo CEP

        cepInput.addEventListener('blur', function() { // ao sair do campo

            const cep = this.value.replace(/\D/g, ''); // remove tudo que não é dígito
            const form = this.closest('form');
            if (!form) return;

            const logradouroInput = form.querySelector('.logradouro-input'); // campo de logradouro
            const bairroInput = form.querySelector('.bairro-input'); // campo do bairro
            const cidadeInput = form.querySelector('.cidade-input'); // campo da cidade
            const estadoInput = form.querySelector('.estado-input'); // campo do estado

            // Limpa os campos de endereço
            logradouroInput.value = '';
            bairroInput.value = '';
            cidadeInput.value = '';
            estadoInput.value = '';

            if (cep.length !== 8) { // se o CEP não tiver 8 dígitos
                return;
            }

            fetch(`/api/cep/${cep}`) // busca o endereço na API
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
                    // Preenche os campos com os dados retornados
                    logradouroInput.value = data.logradouro || '';
                    bairroInput.value = data.bairro || '';
                    cidadeInput.value = data.localidade || '';
                    estadoInput.value = data.uf || '';
                })
                .catch(() => alert('Erro ao buscar dados do CEP'));
        });
    });
});
