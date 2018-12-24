CREATE TABLE usuario (
  id_usuario    BIGSERIAL             NOT NULL PRIMARY KEY,
  login         VARCHAR(40)           NOT NULL UNIQUE,
  email         VARCHAR(40)           NOT NULL UNIQUE,
  senha         VARCHAR(200)          NOT NULL,
  excluido      CHAR(1) DEFAULT 'n'   NOT NULL,
  data_exclusao DATE,
  valido        BOOLEAN,
  admin         BOOLEAN DEFAULT FALSE NOT NULL
);

CREATE TABLE cliente (
  id_usuario    BIGINT              NOT NULL REFERENCES usuario PRIMARY KEY,
  nome          VARCHAR(30)         NOT NULL,
  sobrenome     VARCHAR(40)         NOT NULL,
  sexo          VARCHAR(1)          NOT NULL,
  data_nasc     DATE                NOT NULL,
  telefone      VARCHAR(14),
  celular       VARCHAR(14),
  excluido      CHAR(1) DEFAULT 'n' NOT NULL,
  data_exclusao DATE
);

CREATE TABLE endereco (
  id_endereco   BIGSERIAL                    NOT NULL PRIMARY KEY,
  id_usuario    BIGINT                       NOT NULL REFERENCES usuario,
  endereco      VARCHAR(255)                 NOT NULL,
  numero        VARCHAR(10)                  NOT NULL,
  complemento   VARCHAR(40),
  bairro        VARCHAR(255),
  cep           VARCHAR(10)                  NOT NULL,
  cidade        VARCHAR(40)                  NOT NULL,
  estado        VARCHAR(2) DEFAULT 'SP'      NOT NULL,
  pais          VARCHAR(30) DEFAULT 'Brasil' NOT NULL,
  excluido      CHAR(1) DEFAULT 'n',
  data_exclusao DATE
);

CREATE TABLE categorias (
  id_cat SERIAL  NOT NULL PRIMARY KEY,
  nome   varchar NOT NULL
);

INSERT INTO categorias
VALUES (1, 'Bottons');

INSERT INTO categorias
VALUES (2, 'Chaveiros');

INSERT INTO categorias
VALUES (3, 'Cartões');

INSERT INTO categorias
VALUES (4, 'Combos');

CREATE TABLE produtos (
  id_produto    BIGSERIAL     NOT NULL PRIMARY KEY,
  nome          VARCHAR(60)   NOT NULL,
  descricao     VARCHAR(255)  NOT NULL,
  preco         NUMERIC(5, 2) NOT NULL,
  excluido      BOOLEAN DEFAULT FALSE,
  data_exclusao DATE,
  max_qtde      INT,
  categoria     INT REFERENCES categorias
);

CREATE TABLE vendas (
  id_venda    BIGSERIAL     NOT NULL PRIMARY KEY,
  id_usuario  BIGINT        NOT NULL REFERENCES usuario,
  id_produto  BIGINT        NOT NULL REFERENCES produtos,
  preco_u     NUMERIC(5, 2) NOT NULL,
  preco_t     NUMERIC(6, 2) NOT NULL,
  qtde        INT           NOT NULL,
  data_compra DATE          NOT NULL,
  cf          BIGINT        NOT NULL
);

CREATE TABLE carrinho (
  id_item    BIGSERIAL NOT NULL PRIMARY KEY,
  id_usuario bigint    NOT NULL REFERENCES usuario,
  id_produto bigint    NOT NULL REFERENCES produtos,
  qtde       int       NOT NULL,
  id_venda   bigint REFERENCES vendas
);

CREATE TABLE operacaocaixa (
  id_operacao SERIAL  NOT NULL PRIMARY KEY,
  descricao   VARCHAR NOT NULL,
  tipo        INT     NOT NULL
);

INSERT INTO operacaocaixa
VALUES (DEFAULT, 'Capital inicial', 1);

INSERT INTO operacaocaixa
VALUES (DEFAULT, 'Aquisição de produtos', 2);

INSERT INTO operacaocaixa
VALUES (DEFAULT, 'Venda de produtos', 1);

CREATE TABLE fluxocaixa (
  id_fluxocaixa BIGSERIAL     NOT NULL PRIMARY KEY,
  dia           DATE          NOT NULL,
  operacao      INT           NOT NULL REFERENCES operacaocaixa,
  valor         NUMERIC(5, 2) NOT NULL
);