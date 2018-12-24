/**
 * @summary Toda vez que o radio de Todos for selecionado, desmarca os outros radio
 */
jQuery('#all #radio1').change(function () {
    if (jQuery('#all #radio1')[0].checked) {
        let jq = jQuery('#specific input');
        for (let i = 0; i < jq.length; i++) {
            jq[i].checked = false;
        }
    }
});

/**
 * @summary Desmarca o radio Todos quando qualquer outro for selecionado (ou o remarca caso nenhum esteja selecionado)
 */
jQuery('#specific input').change(function () {
    let jq = jQuery('#specific input');
    let a = false;

    for (let i = 0; i < jq.length; i++) {
        if (!jq[i].checked) {
            a = false;
            break;
        } else {
            a = true;
        }
    }

    jQuery('#all #radio1')[0].checked = a;

    if (a) {
        for (let i = 0; i < jq.length; i++) {
            jq[i].checked = false;
        }
    }
});

/**
 * @summary Toda vez que um filtro for alterado, envia o form
 */
jQuery('#frmFiltros input').change(function () {
    jQuery('#frmFiltros').submit();
});

let btnProd = jQuery('.btnProd');

/**
 * @summary Envia o frmFiltros toda vez que o frmSearch receber uma solicitação de submit
 * */
jQuery('#search').submit(function (e) {
    e.preventDefault(e);
    let frmFiltros = jQuery('#frmFiltros');
    jQuery('#frmFiltros #txtSearch').val(jQuery('#search #search-bar').val());
    frmFiltros.submit();
});

if (isLogado) {
    for (let i = 0; i < btnProd.length; i++) {
        btnProd[i].onclick = function () {
            jQuery('.btnProd ~ .prodQtde')[i].style.display = 'grid';
            btnProd[i].style.display = 'none';
        };
    }

    for (let i = 0; i < btnProd.length; i++) {
        jQuery('.prodQtde #prodQtdeSend')[i].onclick = function () {
            let xhttp = new XMLHttpRequest();
            xhttp.open("POST", "cart.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    console.log(xhttp.responseText);
                    if (this.responseText === "add") {
                        alert('Produto adicionado ao carrinho.');

                        window.location.reload();
                    }
                }
            };

            xhttp.send("add=true&id=" + jQuery('.prodQtde #numId')[i].value + "&qtde=" + jQuery('.prodQtde #numQtde')[i].value);
        };
    }
} else {
    for (let i = 0; i < btnProd.length; i++) {
        btnProd[i].onclick = function () {
            window.location = "login.php?redirect=" + window.location.href.substr(window.location.origin.length);
        }
    }
}
