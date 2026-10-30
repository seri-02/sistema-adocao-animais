# LEGADO / HISTÓRICO

O projeto original incluía um dump do banco de dados (phpMyAdmin, 2022), versionado em `sql/abrigo_dos_gatos.sql`.

Esse dump **foi removido da árvore atual** porque continha dados legados e pessoais que não são necessários
para reproduzir o projeto. Ele permanece apenas no histórico do Git e **não deve ser usado para instalação**:
não representa o banco atual e apaga as tabelas existentes ao ser importado.

A instalação atual usa:

```text
sql/baseline/     ← estrutura do banco legado, sem dados
+
sql/migrations/   ← aplicadas em ordem
```

Passo a passo em [`sql/README.md`](../README.md).
