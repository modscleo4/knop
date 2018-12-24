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
 * @summary Esta função obtém os dados de um endereço já existente no banco de dados
 */
function getAddress() {
    let xhttp = new XMLHttpRequest();
    xhttp.open("POST", "query.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            //Conexão OK (dados recebidos)
            if (this.responseText !== "") {
                let address = JSON.parse(this.responseText);
                jQuery('#txtCep').val(address["cep"]).change();
                jQuery('#txtRua').val(address["endereco"]).change();
                jQuery('#txtNumero').val(address["numero"]).change();
                jQuery('#txtComplemento').val(address["complemento"]).change();
                jQuery('#txtBairro').val(address["bairro"]).change();
                jQuery('#txtCidade').val(address["cidade"]).change();
                jQuery('#txtUF').val(address["estado"]).change();
                jQuery('#txtPais').val(address["pais"]).change();
            }
        }
    };
    xhttp.send("query=getAddress&addressId=" + this.value);
}

jQuery('.radExistingAddress').change(getAddress);
jQuery('.address #radEdit').change(getAddress);

function open(element, btn) {
    //element.style.height = height;
    element.classList.add('open');
    btn.classList.add('btnOpen');
}

function close(element, btn) {
    element.classList.remove('open');
    btn.classList.remove('btnOpen');
}

/**
 * Aplica a propriedade onclick para cada header no form, onde irá abrir o menu ou fechar
 */
let headers = document.getElementById('fields').getElementsByTagName('h2');
for (let i = 0; i < headers.length; i++) {
    headers[i].onclick = function () {
        let div = jQuery('#header + div');
        if (div[i].className === 'open') {
            close(div[i], jQuery('#header #btnSend input')[i]);
        } else {
            open(div[i], jQuery('#header #btnSend input')[i]);
        }

        for (let j = 0; j < div.length; j++) {
            if (j !== i) {
                close(div[j], jQuery('#header #btnSend input')[j]);
            }
        }
    }
}

/**
 * Executa tudo quando a página estiver pronta
 */
jQuery(document).ready(function () {
    /**
     * Abre o primeiro menu
     */
    headers[0].onclick();

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