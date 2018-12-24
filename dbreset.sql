DELETE
FROM vendas;

DELETE
FROM carrinho;

ALTER SEQUENCE carrinho_id_item_seq
  RESTART WITH 1;

ALTER SEQUENCE vendas_id_venda_seq
  RESTART WITH 1;

ALTER SEQUENCE cf_seq
  RESTART WITH 1;
