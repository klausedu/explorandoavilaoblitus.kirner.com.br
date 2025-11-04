<?php
/**
 * Teste DIRETO de INSERT no banco
 * Este arquivo testa se conseguimos gravar dados no banco
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');

echo "<h1>🧪 Teste de INSERT Direto no Banco</h1>";
echo "<style>body{font-family:monospace; background:#1a1a2e; color:#eee; padding:20px;} .success{color:#28a745;} .error{color:#dc3545;} .info{color:#17a2b8;} pre{background:#000; padding:10px; border-radius:5px; overflow-x:auto;}</style>";

// Passo 1: Incluir config
echo "<h2>Passo 1: Carregar config.php</h2>";
try {
    require_once 'config.php';
    echo "<p class='success'>✅ config.php carregado com sucesso</p>";
    echo "<p class='info'>DB_NAME: " . DB_NAME . "</p>";
    echo "<p class='info'>DB_USER: " . DB_USER . "</p>";
    echo "<p class='info'>DB_HOST: " . DB_HOST . "</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Erro ao carregar config: " . $e->getMessage() . "</p>";
    exit;
}

// Passo 2: Conectar ao banco
echo "<h2>Passo 2: Conectar ao banco</h2>";
try {
    $pdo = getDBConnection();
    echo "<p class='success'>✅ Conexão estabelecida com sucesso</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Erro de conexão: " . $e->getMessage() . "</p>";
    exit;
}

// Passo 3: Verificar se tabela locations existe
echo "<h2>Passo 3: Verificar tabela 'locations'</h2>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'locations'");
    $exists = $stmt->fetch();
    if ($exists) {
        echo "<p class='success'>✅ Tabela 'locations' existe</p>";

        // Mostrar estrutura da tabela
        $desc = $pdo->query("DESCRIBE locations");
        echo "<p class='info'>Estrutura da tabela:</p>";
        echo "<pre>";
        while ($col = $desc->fetch()) {
            echo "{$col['Field']} - {$col['Type']} - {$col['Null']} - {$col['Key']}\n";
        }
        echo "</pre>";
    } else {
        echo "<p class='error'>❌ Tabela 'locations' NÃO existe!</p>";
        echo "<p class='info'>Execute o arquivo database.sql primeiro!</p>";
        exit;
    }
} catch (PDOException $e) {
    echo "<p class='error'>❌ Erro ao verificar tabela: " . $e->getMessage() . "</p>";
    exit;
}

// Passo 4: Contar registros existentes
echo "<h2>Passo 4: Contar registros existentes</h2>";
try {
    $count = $pdo->query("SELECT COUNT(*) FROM locations")->fetchColumn();
    echo "<p class='info'>Total de localizações no banco: <strong>$count</strong></p>";
} catch (PDOException $e) {
    echo "<p class='error'>❌ Erro ao contar: " . $e->getMessage() . "</p>";
}

// Passo 5: Tentar INSERT de teste
echo "<h2>Passo 5: Tentar INSERT de teste</h2>";
$testId = 'test_insert_' . time();
$testName = 'Teste INSERT ' . date('H:i:s');

try {
    echo "<p class='info'>Tentando inserir:</p>";
    echo "<pre>ID: $testId\nNome: $testName</pre>";

    $stmt = $pdo->prepare("
        INSERT INTO locations (id, name, description, background_image)
        VALUES (?, ?, ?, ?)
    ");

    $result = $stmt->execute([
        $testId,
        $testName,
        'Descrição de teste',
        'test.jpg'
    ]);

    if ($result) {
        echo "<p class='success'>✅ INSERT executado com sucesso!</p>";
        echo "<p class='info'>Linhas afetadas: " . $stmt->rowCount() . "</p>";
    } else {
        echo "<p class='error'>❌ INSERT falhou mas não deu exception</p>";
    }

} catch (PDOException $e) {
    echo "<p class='error'>❌ Erro ao fazer INSERT: " . $e->getMessage() . "</p>";
    echo "<p class='error'>Código do erro: " . $e->getCode() . "</p>";
}

// Passo 6: Verificar se realmente gravou
echo "<h2>Passo 6: Verificar se gravou no banco</h2>";
try {
    $stmt = $pdo->prepare("SELECT * FROM locations WHERE id = ?");
    $stmt->execute([$testId]);
    $found = $stmt->fetch();

    if ($found) {
        echo "<p class='success'>✅ REGISTRO ENCONTRADO NO BANCO!</p>";
        echo "<pre>" . print_r($found, true) . "</pre>";
    } else {
        echo "<p class='error'>❌ REGISTRO NÃO ENCONTRADO! Não foi persistido.</p>";
    }
} catch (PDOException $e) {
    echo "<p class='error'>❌ Erro ao verificar: " . $e->getMessage() . "</p>";
}

// Passo 7: Listar todas as localizações
echo "<h2>Passo 7: Listar TODAS as localizações</h2>";
try {
    $stmt = $pdo->query("SELECT id, name, created_at, updated_at FROM locations ORDER BY created_at DESC LIMIT 10");
    $locations = $stmt->fetchAll();

    echo "<p class='info'>Total: " . count($locations) . " localizações (últimas 10)</p>";
    echo "<table style='width:100%; border-collapse:collapse;'>";
    echo "<tr style='background:#0f3460;'><th style='padding:8px; border:1px solid #333;'>ID</th><th style='padding:8px; border:1px solid #333;'>Nome</th><th style='padding:8px; border:1px solid #333;'>Criado</th><th style='padding:8px; border:1px solid #333;'>Atualizado</th></tr>";

    foreach ($locations as $loc) {
        $highlight = ($loc['id'] === $testId) ? "background:#28a745;" : "";
        echo "<tr style='$highlight'>";
        echo "<td style='padding:8px; border:1px solid #333;'>{$loc['id']}</td>";
        echo "<td style='padding:8px; border:1px solid #333;'>{$loc['name']}</td>";
        echo "<td style='padding:8px; border:1px solid #333;'>{$loc['created_at']}</td>";
        echo "<td style='padding:8px; border:1px solid #333;'>{$loc['updated_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";

} catch (PDOException $e) {
    echo "<p class='error'>❌ Erro ao listar: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>📝 Conclusão</h2>";
echo "<p>Se você vê '✅ REGISTRO ENCONTRADO NO BANCO!' acima, significa que o INSERT está funcionando.</p>";
echo "<p>Se não vê, há um problema com permissões ou configuração do banco.</p>";
echo "<p><a href='test-insert.php' style='color:#4ecca3;'>🔄 Executar teste novamente</a></p>";
?>
