SELECT t.dia,
       descricao,
    --CASE WHEN (tipo = 1) THEN ('E') ELSE ('S') END AS tipo,
       sum(CASE WHEN (tipo = 1) THEN (valor) ELSE (-valor) END) OVER (ORDER BY id_fluxocaixa ASC) -
       (CASE WHEN (tipo = 1) THEN (valor) ELSE (-valor) END)                                      AS saldo_anterior,
       CASE WHEN (tipo = 1) THEN (valor) ELSE (0.00) END                                          AS entrada,
       CASE WHEN (tipo = 2) THEN (valor) ELSE (0.00) END                                          AS saida,
    --CASE WHEN (tipo = 1) THEN (valor) ELSE (-valor) END AS valor,
       sum(CASE WHEN (tipo = 1) THEN (valor) ELSE (-valor) END) OVER (ORDER BY id_fluxocaixa ASC) AS saldo
FROM fluxocaixa t
       INNER JOIN operacaocaixa o on t.operacao = o.id_operacao
WHERE dia <= '2018-10-11'
GROUP BY id_fluxocaixa, dia, descricao, tipo, valor
ORDER BY dia;