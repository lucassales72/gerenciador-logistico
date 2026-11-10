<?php
/**
 * Configuracao e conexao com o banco de dados (PDO).
 * Padrao do XAMPP: usuario "root" e senha vazia.
 */

// Caminho da aplicacao dentro do htdocs (ajuste se renomear a pasta).
const BASE_URL = '/gerenciador-logistico';

const DB_HOST = 'localhost';
const DB_NAME = 'gerenciador_logistico';
const DB_USER = 'root';
const DB_PASS = '';

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        // DB_DSN permite trocar o banco em testes automatizados; no XAMPP usa-se o MySQL.
        $dsn = getenv('DB_DSN') ?: 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
            $pdo->exec('PRAGMA foreign_keys = ON');
        }
    }
    return $pdo;
}
