<?php
require_once __DIR__ . '/../includes/helpers.php';

// Os itens (frete_produtos) sao apagados junto, por ON DELETE CASCADE.
excluirRegistro('fretes', (int) ($_GET['id'] ?? 0), 'fretes/index.php', 'Frete');
