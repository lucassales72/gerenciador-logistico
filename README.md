# Gerenciador Logístico

Aplicação web para gerenciamento de **fretes**, desenvolvida como Atividade Prática Orientada (APO) da disciplina **Fundamentos de Programação para Internet** (UNIPAR EAD).

O sistema permite cadastrar produtos, endereços (origem e destino) e transportadores, e depois **gerar fretes** que relacionam esses dados — um único transportador pode levar **vários produtos** (relação 1 para muitos). Também é possível **baixar em PDF o relatório dos fretes de um dia**.

## Tecnologias

- **PHP 8** (backend, PDO com *prepared statements*)
- **MySQL / MariaDB** (XAMPP)
- **HTML5, CSS3 e JavaScript** (campos dinâmicos e máscaras)
- **Bootstrap 5** (via CDN) e Bootstrap Icons
- **Git/GitHub** para versionamento

## Funcionalidades

- CRUD de **Produtos** (nome, descrição, peso, valor)
- CRUD de **Endereços** (CEP, logradouro, número, bairro, cidade, UF) usados como origem/destino
- CRUD de **Transportadores** (nome, CPF/CNPJ, telefone, veículo, placa)
- **Gerar/editar frete**: escolhe transportador, origem, destino, data, valor e adiciona N produtos com quantidade (linhas adicionadas dinamicamente com JavaScript)
- Detalhe do frete com peso total e valor da mercadoria
- Filtro de fretes por data
- **Relatório PDF dos fretes do dia** (gerado em PHP puro, sem bibliotecas externas)
- Menu de navegação em todas as telas, validações no servidor e proteção contra XSS (`htmlspecialchars`)

## Estrutura de diretórios

```
gerenciador-logistico/
├── index.php                 # painel inicial
├── config/database.php       # conexão PDO e BASE_URL
├── database/schema.sql       # criação do banco e tabelas
├── includes/                 # header, footer, helpers e SimplePDF (gerador de PDF)
├── produtos/                 # index, form (cadastro/edição), excluir
├── enderecos/                # idem
├── transportadores/          # idem
├── fretes/                   # index, form, ver, excluir, relatorio (PDF)
├── assets/css, assets/js     # estilos e JavaScript (campos dinâmicos)
└── docs/esboco-das-telas.pdf # esboço das telas (Etapa 1)
```

## Modelo de dados

```
produtos ──┐
           ├─< frete_produtos >── fretes ──> transportadores
           │                        ├────> enderecos (origem)
           │                        └────> enderecos (destino)
```

## Como executar (XAMPP)

1. Instale o [XAMPP](https://www.apachefriends.org/) e inicie **Apache** e **MySQL**.
2. Copie a pasta do projeto para `C:\xampp\htdocs\gerenciador-logistico` (mantenha esse nome ou ajuste `BASE_URL` em `config/database.php`).
3. Abra o **phpMyAdmin** (`http://localhost/phpmyadmin`), aba **Importar**, e importe `database/schema.sql`.
4. Confira as credenciais em `config/database.php` (padrão do XAMPP: usuário `root`, senha vazia).
5. Acesse **http://localhost/gerenciador-logistico/**.

> O Bootstrap é carregado por CDN, então é necessário estar conectado à internet.

## Como usar

1. Cadastre ao menos **1 produto**, **2 endereços** (origem e destino) e **1 transportador**.
2. Em **Fretes → Gerar frete**, preencha os dados e use **Adicionar produto** para incluir quantos itens quiser.
3. Em **Fretes**, escolha uma data e clique em **Baixar relatório do dia (PDF)**.

## Autor

Nome: Lucas Gabriel Ferreira de Sales · RA: 09065280 · Curso: ADS · Polo:UMUARAMA — UNIPAR EAD
