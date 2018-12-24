/**
 * @summary Quando um input[type=text] lançar um evento do tipo onkeyup ou onchange, altera a propriedade value para o DOMValue
 */
jQuery('input.textInput').keyup(function () {
    this.setAttribute('value', this.value);
}).change(function () {
    this.setAttribute('value', this.value);
});

/**
 * @summary remove as mensagens de erro de cada input[type=text]
 */
jQuery('.field').click(function () {
    if (this.getElementsByTagName('p')[0] !== null) {
        this.getElementsByTagName('p')[0].style.display = 'none';
    }
});