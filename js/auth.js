"use strict"
// Mostrar/ocultar senha no login e no cadastro públicos.
// Só alterna input.type (password <-> text): o valor nunca é lido, copiado nem guardado.
// Cada botão aponta para o seu campo por aria-controls; sem JavaScript os botões ficam ocultos.
// O ícone (olho aberto/fechado) acompanha o type do campo só pelo CSS.
document.querySelectorAll('.mostrar-senha[aria-controls]').forEach((botao) => {
    const campo = document.getElementById(botao.getAttribute('aria-controls'))
    if (!campo) {
        return
    }

    function exibir(visivel) {
        campo.type = visivel ? 'text' : 'password'
        botao.setAttribute('aria-label', (visivel ? 'Ocultar ' : 'Mostrar ') + botao.dataset.rotulo)
    }

    botao.hidden = false
    botao.addEventListener('click', () => exibir(campo.type === 'password'))

    // Ao enviar, o campo volta a ser password (o navegador não trata a senha como texto comum)
    if (campo.form) {
        campo.form.addEventListener('submit', () => exibir(false))
    }
})
