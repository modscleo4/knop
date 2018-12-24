SELECT t.dia,
       CASE WHEN (tipo = 1) THEN (valor) ELSE (0.00) END AS receitas,
       CASE WHEN (tipo = 2) THEN (valor) ELSE (0.00) END AS despesas
FROM fluxocaixa t
       INNER JOIN operacaocaixa o ON t.operacao = o.id_operacao
WHERE operacao <> 1
  AND t.dia <= '2018-10-11'
GROUP BY id_fluxocaixa, dia, tipo
ORDER BY dia