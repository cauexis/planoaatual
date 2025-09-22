document.addEventListener('DOMContentLoaded', function() {
    const cotadorBtn = document.getElementById('cotador-btn');
    const resultadoDiv = document.getElementById('cotador-resultado');
    const form = document.getElementById('cotador-form');
    const modeSwitch = form.querySelectorAll('input[name="cotador-mode"]');
    const faixaFields = document.querySelector('.faixa-mode');
    const idadeFields = document.querySelector('.idade-mode');
    const addIdadeBtn = document.getElementById('btn-add-idade');
    const idadesList = document.getElementById('idades-individuais-list');

    // Alterna modos de cotação
    modeSwitch.forEach(input => {
        input.addEventListener('change', function() {
            if (input.value === 'faixa') {
                faixaFields.style.display = '';
                idadeFields.style.display = 'none';
            } else {
                faixaFields.style.display = 'none';
                idadeFields.style.display = '';
                if (!idadesList.querySelector('.cotador-field')) addIdadeInput();
            }
        });
    });

    // Função para adicionar campo de idade
    function addIdadeInput(value = '') {
        const wrapper = document.createElement('div');
        wrapper.classList.add('cotador-field');
        const label = document.createElement('label');
        label.textContent = 'Idade:';
        const input = document.createElement('input');
        input.type = 'number';
        input.min = '0';
        input.max = '120';
        input.placeholder = 'Ex: 34';
        input.value = value;
        input.classList.add('idade-individual');
        const btnRemove = document.createElement('button');
        btnRemove.type = 'button';
        btnRemove.textContent = 'Remover';
        btnRemove.classList.add('btn-add-idade');
        btnRemove.style.background = '#eee';
        btnRemove.style.color = '#333';
        btnRemove.style.marginLeft = '8px';
        btnRemove.addEventListener('click', function() {
            wrapper.remove();
        });
        wrapper.appendChild(label);
        wrapper.appendChild(input);
        wrapper.appendChild(btnRemove);
        idadesList.appendChild(wrapper);
    }

    addIdadeBtn.addEventListener('click', function(e){
        e.preventDefault();
        addIdadeInput();
    });

    cotadorBtn.addEventListener('click', function() {
        const mode = form.querySelector('input[name="cotador-mode"]:checked').value;
        const planoIdInput = form.querySelector('input[name="id"]');
        const entidadeInput = form.querySelector('select[name="cotador-entidade"]');

        if (!planoIdInput || !entidadeInput) {
            resultadoDiv.innerHTML = '<p>Erro de configuração: Campos do formulário não encontrados.</p>';
            resultadoDiv.style.display = 'block';
            return;
        }

        const planoId = planoIdInput.value;
        const codigoGrupo = entidadeInput.value;

        resultadoDiv.style.display = 'block';
        resultadoDiv.innerHTML = '<p>Calculando...</p>';

        if (mode === 'faixa') {
            let faixas = [];
            let totalPessoas = 0;
            document.querySelectorAll('#faixas-etarias-inputs input[type="number"]').forEach(input => {
                const numPessoas = parseInt(input.value, 10) || 0;
                if (numPessoas > 0) {
                    faixas.push({
                        idadeMin: input.dataset.idadeMin,
                        idadeMax: input.dataset.idadeMax,
                        numPessoas: numPessoas
                    });
                    totalPessoas += numPessoas;
                }
            });
            if (faixas.length === 0) {
                resultadoDiv.innerHTML = '<p>Informe ao menos uma quantidade nas faixas!</p>';
                return;
            }
            const faixasJson = JSON.stringify(faixas);
            const url = `plano_detalhe.php?cotar=true&id=${planoId}&entidade=${codigoGrupo}&faixas=${encodeURIComponent(faixasJson)}`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        let detalhesHtml = data.data.detalhes.map(detalhe => `
                            <li>
                                ${detalhe.pessoas} pessoa(s) de ${detalhe.faixa}: <strong>R$ ${detalhe.subtotal}</strong>
                            </li>
                        `).join('');
                        resultadoDiv.innerHTML = `
                            <h3>Cotação para ${totalPessoas} pessoa(s)</h3>
                            <p style="font-size: 1.5em; margin: 15px 0;">
                                <strong>Preço total: R$ ${data.data.precoTotal}</strong>
                            </p>
                            <ul>${detalhesHtml}</ul>
                        `;
                    } else {
                        resultadoDiv.innerHTML = `<h3>Erro na Cotação</h3><p>${data.message || 'Não foi possível calcular o valor.'}</p>`;
                    }
                })
                .catch(error => {
                    resultadoDiv.innerHTML = `<h3>Erro de Exibição</h3>
                    <p>O cálculo foi feito, mas houve um erro ao exibir o resultado. Verifique o console (F12) para mais detalhes.</p>`;
                    console.error('Erro ao processar a resposta do fetch:', error);
                });
        } else if (mode === 'idade') {
            let idades = [];
            let totalPessoas = 0;
            idadesList.querySelectorAll('input.idade-individual').forEach(input => {
                const idade = parseInt(input.value, 10);
                if (!isNaN(idade) && idade >= 0 && idade <= 120) {
                    idades.push(idade);
                    totalPessoas += 1;
                }
            });
            if (idades.length === 0) {
                resultadoDiv.innerHTML = '<p>Adicione pelo menos uma idade válida!</p>';
                return;
            }
            const idadesJson = JSON.stringify(idades);
            const url = `plano_detalhe.php?cotar=true&id=${planoId}&entidade=${codigoGrupo}&idades=${encodeURIComponent(idadesJson)}`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        let detalhesHtml = data.data.detalhes.map(detalhe => `
                            <li>
                                Idade ${detalhe.idade}: <strong>R$ ${detalhe.subtotal}</strong> (${detalhe.faixa})
                            </li>
                        `).join('');
                        resultadoDiv.innerHTML = `
                            <h3>Cotação para ${totalPessoas} pessoa(s)</h3>
                            <p style="font-size: 1.5em; margin: 15px 0;">
                                <strong>Preço total: R$ ${data.data.precoTotal}</strong>
                            </p>
                            <ul>${detalhesHtml}</ul>
                        `;
                    } else {
                        resultadoDiv.innerHTML = `<h3>Erro na Cotação</h3><p>${data.message || 'Não foi possível calcular o valor.'}</p>`;
                    }
                })
                .catch(error => {
                    resultadoDiv.innerHTML = `<h3>Erro de Exibição</h3>
                    <p>O cálculo foi feito, mas houve um erro ao exibir o resultado. Verifique o console (F12) para mais detalhes.</p>`;
                    console.error('Erro ao processar a resposta do fetch:', error);
                });
        }
    });
});