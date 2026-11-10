-- Gerenciador Logistico - estrutura do banco (MySQL / MariaDB do XAMPP)
-- Importe pelo phpMyAdmin (aba "Importar") ou rode: mysql -u root < database/schema.sql

CREATE DATABASE IF NOT EXISTS gerenciador_logistico
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gerenciador_logistico;

CREATE TABLE IF NOT EXISTS produtos (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(120)  NOT NULL,
    descricao  VARCHAR(255)  NULL,
    peso_kg    DECIMAL(10,3) NOT NULL DEFAULT 0,
    valor      DECIMAL(12,2) NOT NULL DEFAULT 0,
    criado_em  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS enderecos (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    apelido     VARCHAR(80)  NOT NULL,           -- ex.: "Deposito Central"
    cep         VARCHAR(9)   NOT NULL,
    logradouro  VARCHAR(150) NOT NULL,
    numero      VARCHAR(10)  NOT NULL,
    complemento VARCHAR(80)  NULL,
    bairro      VARCHAR(80)  NOT NULL,
    cidade      VARCHAR(80)  NOT NULL,
    uf          CHAR(2)      NOT NULL,
    criado_em   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS transportadores (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(120) NOT NULL,
    cpf_cnpj    VARCHAR(18)  NOT NULL,
    telefone    VARCHAR(20)  NULL,
    veiculo     VARCHAR(60)  NULL,               -- ex.: "Caminhao bau"
    placa       VARCHAR(8)   NULL,
    criado_em   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Um frete tem 1 transportador, 1 origem, 1 destino e VARIOS produtos.
CREATE TABLE IF NOT EXISTS fretes (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transportador_id INT UNSIGNED  NOT NULL,
    origem_id        INT UNSIGNED  NOT NULL,
    destino_id       INT UNSIGNED  NOT NULL,
    data_frete       DATE          NOT NULL,
    valor_frete      DECIMAL(12,2) NOT NULL DEFAULT 0,
    observacao       VARCHAR(255)  NULL,
    criado_em        TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transportador_id) REFERENCES transportadores(id),
    FOREIGN KEY (origem_id)        REFERENCES enderecos(id),
    FOREIGN KEY (destino_id)       REFERENCES enderecos(id)
) ENGINE=InnoDB;

-- Tabela de ligacao: frete (1) -> produtos (N)
CREATE TABLE IF NOT EXISTS frete_produtos (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    frete_id   INT UNSIGNED NOT NULL,
    produto_id INT UNSIGNED NOT NULL,
    quantidade INT UNSIGNED NOT NULL DEFAULT 1,
    FOREIGN KEY (frete_id)   REFERENCES fretes(id)   ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
) ENGINE=InnoDB;
