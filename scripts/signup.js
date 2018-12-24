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
    if (this.getElementsByTagName('p')[0] != null) {
        this.getElementsByTagName('p')[0].style.display = 'none';
    }
});

jQuery('#txtEmail').change(function() {

});

/**
 * @summary Toda vez que o #txtCep lançar o evento onchange, receba os dados do CEP informado e exibir uma mensagem de erro caso seja inválido
 */
jQuery('#txtCep').change(function () {
    this.setAttribute('value', this.value);

    let xhttp = new XMLHttpRequest();
    xhttp.open("GET", "https://viacep.com.br/ws/" + this.value + "/json/", true);
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200 && this.responseText !== "ViaCEP Bad Request (400)") {
            // O CEP Existe
            jQuery('#cep')[0].getElementsByTagName('p')[0].style.display = 'none';
            let address = JSON.parse(xhttp.responseText);
            jQuery('#txtRua').val(address["logradouro"]).change();
            jQuery('#txtComplemento').val(address["complemento"]).change();
            jQuery('#txtBairro').val(address["bairro"]).change();
            jQuery('#txtCidade').val(address["localidade"]).change();
            jQuery('#txtUF').val(address["uf"]).change();

            let fields = [
                "#txtRua",
                "#txtComplemento",
                "#txtBairro",
                "#txtCidade",
                "#txtUF"
            ];

            for (let i = 0; i < fields.length; i++) {
                jQuery(fields[i]).prop('disabled', false);
            }
            jQuery('#txtNumero').prop('disabled', false);

            for (let i = 0; i < fields.length; i++) {
                jQuery(fields[i])[0].readOnly = jQuery(fields[i]).val() !== "";
            }
        } else {
            // O CEP talvez não exista ou ocorreu um erro de rede
            jQuery('#cep')[0].getElementsByTagName('p')[0].style.display = 'block';
            jQuery('#btnAddress')[0].enabled = false;

        }
    };
    xhttp.send();
});

/**
 * Executa tudo quando a página estiver pronta
 */
jQuery(document).ready(function () {
    jQuery('#numTelefone').mask('(00) 0000-0000');
    jQuery('#numCelular').mask('(00) 00000-0000');

    jQuery('#txtCep').mask('00000-000');
});

jQuery('#frmDP').submit(function () {
    jQuery('#numTelefone').unmask();
    jQuery('#numCelular').unmask();
});

jQuery('#frmEndereco').submit(function () {
    jQuery('#txtCep').unmask();
});