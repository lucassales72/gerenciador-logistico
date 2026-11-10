// Campos dinamicos do formulario de frete: adicionar/remover linhas de produto.
document.addEventListener('DOMContentLoaded', function () {
    var container = document.getElementById('itens');
    var modelo = document.getElementById('modelo-item');
    var botaoAdd = document.getElementById('btn-add-item');

    if (container && modelo && botaoAdd) {
        botaoAdd.addEventListener('click', function () {
            container.appendChild(modelo.content.cloneNode(true));
        });

        container.addEventListener('click', function (ev) {
            var botao = ev.target.closest('.remover-item');
            if (!botao) { return; }
            if (container.querySelectorAll('.item-frete').length > 1) {
                botao.closest('.item-frete').remove();
            } else {
                alert('O frete precisa ter pelo menos um produto.');
            }
        });
    }

    // Mascara simples de CEP (00000-000).
    document.querySelectorAll('input[data-mask="cep"]').forEach(function (campo) {
        campo.addEventListener('input', function () {
            var n = campo.value.replace(/\D/g, '').slice(0, 8);
            campo.value = n.length > 5 ? n.slice(0, 5) + '-' + n.slice(5) : n;
        });
    });

    // UF sempre em maiusculas e placa tambem.
    document.querySelectorAll('input[data-mask="upper"]').forEach(function (campo) {
        campo.addEventListener('input', function () {
            campo.value = campo.value.toUpperCase();
        });
    });
});
