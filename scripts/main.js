/* A porra do código que um dia eu deixo decente */

let bgBlack = document.createElement('div');
bgBlack.style.position = 'fixed';
bgBlack.style.width = '100vw';
bgBlack.style.height = '100vh';
bgBlack.style.zIndex = '999';
bgBlack.style.top = '0';
bgBlack.style.left = '0';
bgBlack.style.backgroundColor = 'rgba(64, 64, 64, 0.7)';

function parseBoolean(string) {
    if (typeof(string) === "string") {
        if (string.toUpperCase() === "TRUE") {
            return true;
        } else if (string.toUpperCase() === "FALSE") {
            return false;
        }
    }
}

/*
* false: Tema claro
* true: Tema escuro
* */
let darkTheme = false;

let base = document.querySelector("html");
let search = document.getElementById("search-icon-img");
let cart = document.getElementById("cart-img");
let logo = document.getElementById("logo-img");
let button = document.getElementById("theme-changer");

/**
 * @summary Esta função obtém o tema salvo no LocalStorage e altera o background-image do botão #theme-changer
 * @author Francisco Pinheiro <fpds26@gmail.com>
 */
function getTheme() {
    if (localStorage.getItem('darkTheme') !== undefined) {
        darkTheme = parseBoolean(localStorage.getItem('darkTheme'));

        if (darkTheme) {
            $("#theme-changer").css("background-image", "url('res/svg/sun.svg')");
        } else {
            $("#theme-changer").css("background-image", "url('res/svg/moon.svg')");
        }
    }

    getCartItems();

    changeColor();
}

/**
 * @summary Esta função aplica o tema salvo do LocalStorage
 * @author Francisco Pinheiro <fpds26@gmail.com>
 */
function changeTheme() {
    darkTheme = !darkTheme;
    changeColor();

    localStorage.setItem('darkTheme', darkTheme);

    getTheme();
}

/**
 * @summary Função para a aplicação do tema nos elementos HTML
 * @author Dhiego Cassiano Fogaça Barbosa <modscleo4@outlook.com>
 * @author Francisco Pinheiro <fpds26@gmail.com>
 */
function changeColor() {
    let style = getComputedStyle(document.body);
    let properties = [
        "bg-color-1",
        "bg-color-2",
        "bg-color-3",
        "color-txt",
        "color-txt-hover"
    ];

    let logoImg = jQuery('#logo_img');

    if (!darkTheme) {
        // tema claro
        for (let i = 0; i < properties.length; i++) {
            base.style.setProperty('--' + properties[i], style.getPropertyValue('--white-' + properties[i]));
        }

        cart.src = "res/cart.svg";
        search.src = "res/search.svg";
        logo.src = "res/logo.svg";
        if (logoImg[0] != null) {
            logoImg.attr("src", "res/logo.svg");
        }
    } else {
        // tema escuro
        for (let i = 0; i < properties.length; i++) {
            base.style.setProperty('--' + properties[i], style.getPropertyValue('--dark-' + properties[i]));
        }

        cart.src = "res/cart_dark.svg";
        search.src = "res/search_dark.svg";
        logo.src = "res/logo_dark.svg";
        if (logoImg[0] != null) {
            logoImg.attr("src", "res/logo_dark.svg");
        }
    }
}

jQuery(document).scroll(function () {
    let backToTop = document.getElementById('backToTop');

    let scrolled = window.scrollY;
    let scrollHeight = document.documentElement.scrollHeight;
    if (scrolled > 60) {
        if (scrolled + window.innerHeight >= scrollHeight - document.getElementById('footer').offsetHeight + 56) {
            backToTop.className = 'bot'
        } else {
            backToTop.className = 'mid';
        }
    } else {
        backToTop.className = 'top';
    }
});

/**
 * @summary Função para reenviar o email de confirmação via Ajax
 * @author Dhiego Cassiano Fogaça Barbosa <modscleo4@outlook.com>
 * @param email O email destinatário da confirmação
 */
function resend(email) {
    let xhttp = new XMLHttpRequest();
    xhttp.open("POST", "signup.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhttp.onreadystatechange = function () {
        if (xhttp.readyState === 4 && xhttp.status === 200) {
            if (xhttp.responseText === "Sent") {
                let popup = new Popup("Email enviado");
                popup.addContent("Email enviado. Verifique sua caixa de entrada.");
                popup.open();
            }
        }
    };

    xhttp.send("resend=" + email);
}

/**
 * @summary Recebe o número de itens no carrinho do usuário
 * @author Dhiego Cassiano Fogaça Barbosa <modscleo4@outlook.com>
 */
function getCartItems() {
    let xhttp = new XMLHttpRequest();
    xhttp.open("POST", "cart.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhttp.onreadystatechange = function () {
        if (xhttp.readyState === 4 && xhttp.status === 200) {
            let qtde = parseInt(this.responseText);
            if (qtde > 0) {
                if (darkTheme) {
                    jQuery('#cart-img')[0].src = 'res/cart_fill_dark.svg';
                } else {
                    jQuery('#cart-img')[0].src = 'res/cart_fill.svg';
                }

            }
        }
    };

    xhttp.send("query=qtde");
}

/* Popup */
class Popup {
    constructor(title) {
        this.html_popup = document.createElement('div');
        this.title = document.createElement('p');
        this.content = document.createElement('div');
        this.buttons = document.createElement('div');
        this.button = [];

        this.html_popup.className = "popup";
        this.title.className = "popup_title";
        this.content.className = "popup_content";
        this.buttons.className = "popup_buttons";

        this._title = title;

        this.html_popup.appendChild(this.title);
        this.html_popup.appendChild(this.content);
        this.html_popup.appendChild(this.buttons);

        this.addButton('Fechar');
        let mvlira = this;
        this.button[0].onclick = function () {
            mvlira.close();
        }
    }

    set _title(title) {
        this.title.innerHTML = title;
    }

    open() {
        document.body.appendChild(bgBlack);
        document.body.style.overflow = 'hidden';

        document.body.appendChild(this.html_popup);
        //document.body.appendChild();
    }

    close() {
        document.body.removeChild(this.html_popup);

        document.body.removeChild(bgBlack);
        document.body.style.overflow = 'scroll';
    }

    addContent(content) {
        this.content.innerHTML += content;
    }

    deleteContent() {
        this.content.innerHTML = "";
    }

    addButton(buttonText) {
        let btn = document.createElement('button');
        btn.innerHTML = buttonText;
        this.buttons.appendChild(btn);
        this.button.push(btn);
        return btn;
    }

    removeButton(button) {
        this.buttons.removeChild(button);

    }
}

jQuery(document).ready(function () {
    jQuery('#form-login').draggable({
        containment: "parent"
    });

    getTheme();
});
