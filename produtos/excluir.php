<?php
require_once __DIR__ . '/../includes/helpers.php';

excluirRegistro('produtos', (int) ($_GET['id'] ?? 0), 'produtos/index.php', 'Produto');
