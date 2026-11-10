<?php
require_once __DIR__ . '/../includes/helpers.php';

excluirRegistro('transportadores', (int) ($_GET['id'] ?? 0), 'transportadores/index.php', 'Transportador');
