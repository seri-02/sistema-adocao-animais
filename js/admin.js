"use strict"
// Menu lateral da área administrativa em telas menores. No desktop a sidebar é fixa e o
// botão fica oculto pelo CSS; sem JavaScript a navegação aparece sempre, acima do conteúdo.
const botaoMenu = document.getElementById('admin-menu-botao')
const sidebar = document.getElementById('admin-sidebar')

if (botaoMenu && sidebar) {
    let fundo = null

    function definirMenu(aberto) {
        sidebar.classList.toggle('aberta', aberto)
        botaoMenu.setAttribute('aria-expanded', String(aberto))
        if (aberto && !fundo) {
            // Fundo escurecido: clicar fora fecha o menu
            fundo = document.createElement('div')
            fundo.className = 'admin-fundo'
            fundo.addEventListener('click', () => definirMenu(false))
            document.body.appendChild(fundo)
        } else if (!aberto && fundo) {
            fundo.remove()
            fundo = null
        }
        if (aberto) {
            const primeiroLink = sidebar.querySelector('a')
            if (primeiroLink) {
                primeiroLink.focus()
            }
        }
    }

    botaoMenu.addEventListener('click', () => definirMenu(!sidebar.classList.contains('aberta')))

    // Escolher um item fecha o menu
    sidebar.addEventListener('click', (evento) => {
        if (evento.target.closest('a')) {
            definirMenu(false)
        }
    })

    document.addEventListener('keydown', (evento) => {
        if (evento.key === 'Escape' && sidebar.classList.contains('aberta')) {
            definirMenu(false)
            botaoMenu.focus()
        }
    })
}
