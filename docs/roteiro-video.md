# Roteiro do vídeo (5 a 10 minutos)

Publique no YouTube como **Não listado** e envie o link no ambiente virtual.

| Tempo | O que fazer / falar |
|---|---|
| 0:00–0:30 | **Apareça na câmera** e diga: nome, RA, curso, polo e que é aluno(a) da **UNIPAR EAD**. Diga que é a APO de Fundamentos de Programação para Internet: um gerenciador logístico de fretes em PHP. |
| 0:30–1:30 | Mostre o **esboço das telas** (`docs/esboco-das-telas.pdf`) e explique rapidamente a ideia de cada tela. |
| 1:30–2:30 | Abra o sistema no navegador (`localhost/gerenciador-logistico`). Mostre o **menu de navegação** e o painel inicial. |
| 2:30–4:30 | **Cadastre** (pelo menos um dos três; o ideal é os três): produto, 2 endereços (origem e destino) e transportador. Mostre a lista, edite um registro e mostre a validação de erro (ex.: deixar campo vazio). |
| 4:30–6:00 | **Gere um frete**: escolha transportador, origem, destino; clique em *Adicionar produto* (campo dinâmico em JavaScript) e inclua 2–3 produtos. Salve e mostre o detalhe do frete. |
| 6:00–7:00 | Em **Fretes**, filtre pela data e **baixe o relatório PDF do dia**; abra o PDF. |
| 7:00–8:00 | **Compare as telas com o Figma/esboço**: mostre lado a lado e comente as diferenças (se houver). |
| 8:00–9:30 | **Mostre o código** (no VS Code) explicando por alto onde está cada coisa: `config/database.php` (conexão), `database/schema.sql` (tabelas), `produtos/form.php` (cadastro/validação/INSERT-UPDATE), `fretes/form.php` (relação 1→N e transação), `assets/js/app.js` (campos dinâmicos), `fretes/relatorio.php` + `includes/SimplePDF.php` (PDF). |
| 9:30–10:00 | Mostre o **repositório no GitHub** (commits e README) e encerre. |

Dicas: grave a tela com OBS Studio ou o gravador do Windows (Win+G), fale devagar, e confira se o vídeo tem **entre 5 e 10 minutos**.
