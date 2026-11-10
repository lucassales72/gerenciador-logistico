<?php
require_once __DIR__ . '/../includes/helpers.php';

excluirRegistro('enderecos', (int) ($_GET['id'] ?? 0), 'enderecos/index.php', 'Endereco');
