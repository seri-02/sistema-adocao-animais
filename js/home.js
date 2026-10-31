"use strict"
// Menu recolhível da Home em telas pequenas (no desktop o botão fica oculto pelo CSS)
const btnMobile = document.getElementById('btn-mobile')
const nav = document.getElementById('nav')

function definirMenu(aberto) {
    nav.classList.toggle('active', aberto)
    btnMobile.setAttribute('aria-expanded', String(aberto))
}

btnMobile.addEventListener('click', () => definirMenu(!nav.classList.contains('active')))

// Fecha ao escolher um link (ex.: "Como adotar" leva a uma seção da própria página)
nav.addEventListener('click', (evento) => {
    if (evento.target.closest('a')) {
        definirMenu(false)
    }
})

document.addEventListener('keydown', (evento) => {
    if (evento.key === 'Escape' && nav.classList.contains('active')) {
        definirMenu(false)
        btnMobile.focus()
    }
})
