"use strict"
function typeWriter(type) {
    const text = type.innerHTML.split("");
    type.innerHTML = "";

    text.forEach((letter, i) => {
       setTimeout(() => {
        type.innerHTML += letter
       }, 120 * i)
    })
}

typeWriter(titulo)