# Sistema Dinâmico de Localizações - Vila Abandonada

## 📋 Visão Geral

Este documento descreve o **Sistema Dinâmico v2.0** que permite criar, editar e salvar localizações do jogo diretamente no banco de dados MySQL, sem necessidade de exportar código JavaScript.

## 🎯 Funcionalidades

### Antes (v1.0)
- ❌ Editar localizações no location-editor.html
- ❌ Exportar código JavaScript
- ❌ Copiar e colar manualmente no map.js
- ❌ Risco de erros de sintaxe
- ❌ Difícil colaboração

### Agora (v2.0)
- ✅ Editar localizações no location-editor-v2.html
- ✅ Clicar em "Salvar" → Salvo automaticamente no banco
- ✅ Jogo carrega dados do banco automaticamente
- ✅ Sem arquivos JavaScript para editar manualmente
- ✅ Fácil colaboração entre administradores

## 🗄️ Estrutura do Banco de Dados

### Tabelas Criadas

#### `locations`
Armazena as localizações do jogo:
- `id` (VARCHAR) - ID único da localização (ex: "floresta")
- `name` (VARCHAR) - Nome exibido
- `description` (TEXT) - Descrição
- `background_image` (VARCHAR) - Caminho da imagem de fundo
- `created_at`, `updated_at` - Timestamps

#### `hotspots`
Áreas clicáveis dentro das localizações:
- `id` (INT) - ID auto-increment
- `location_id` (VARCHAR) - Localização pai
- `type` (ENUM) - Tipo: navigation, item, interaction
- `x`, `y`, `width`, `height` (DECIMAL) - Posição e tamanho
- `label` (VARCHAR) - Texto exibido
- `target_location` (VARCHAR) - Para navegação
- `item_id` (VARCHAR) - Para itens
- `interaction_data` (TEXT) - Para interações personalizadas

#### `items`
Itens colecionáveis do jogo:
- `id` (VARCHAR) - ID único do item
- `name` (VARCHAR) - Nome
- `description` (TEXT) - Descrição
- `image` (VARCHAR) - Caminho da imagem
- `type` (ENUM) - key, tool, collectible, quest

#### `connections`
Conexões explícitas entre localizações:
- `from_location` (VARCHAR)
- `to_location` (VARCHAR)

## 🔌 APIs Criadas

### Localizações

#### `GET /api/locations/list.php`
Lista todas as localizações com seus hotspots.

**Resposta:**
```json
{
  "success": true,
  "data": {
    "locations": [...],
    "connections": [...],
    "count": 15
  }
}
```

#### `GET /api/locations/get.php?id=floresta`
Busca uma localização específica.

#### `POST /api/locations/save.php`
Salva ou atualiza uma localização completa.

**Body:**
```json
{
  "id": "floresta",
  "name": "Floresta Escura",
  "description": "Uma floresta densa...",
  "background_image": "images/floresta.jpg",
  "hotspots": [
    {
      "type": "navigation",
      "x": 50,
      "y": 50,
      "width": 10,
      "height": 10,
      "label": "Portão",
      "target_location": "portao_entrada"
    }
  ]
}
```

#### `DELETE /api/locations/delete.php?id=floresta`
Deleta uma localização (cascade para hotspots).

### Itens

#### `GET /api/items/list.php`
Lista todos os itens do jogo.

#### `POST /api/items/save.php`
Salva ou atualiza um item.

## 🛠️ Componentes do Sistema

### 1. DatabaseLoader.js
Carrega dados do banco e converte para formato compatível com o jogo.

**Localização:** `js/phaser/managers/DatabaseLoader.js`

**Funcionalidades:**
- Carrega localizações via API
- Converte formato do banco para gameMap
- Fallback automático para map.js se banco falhar
- Exporta `GAME_MAP` global para compatibilidade

### 2. Location Editor v2
Interface visual para editar localizações.

**Localização:** `location-editor-v2.html`

**Funcionalidades:**
- Lista todas as localizações
- Criar nova localização
- Editar campos (nome, descrição, imagem)
- Adicionar/editar/remover hotspots
- Salvar diretamente no banco com um clique

### 3. BootScene Modificado
Carrega dados do banco antes de iniciar o jogo.

**Modificações em:** `js/phaser/scenes/BootScene.js`

- Método `preload()` agora é `async`
- Chama `await databaseLoader.loadGameData()` antes de carregar imagens
- Usa `databaseLoader.gameMap` para preload de assets

## 📦 Migração de Dados

### Script de Migração
**Localização:** `api/migrate_mapjs.php`

Este script importa dados do `map.js` existente para o banco de dados.

**Como usar:**
1. Acesse: `http://seu-site.com/api/migrate_mapjs.php`
2. O script analisa map.js
3. Insere localizações, hotspots e conexões no banco
4. Exibe relatório de migração

**Importante:** Execute apenas uma vez após configurar o banco.

## 🚀 Como Usar

### Passo 1: Configurar Banco de Dados
```bash
# No Hostinger ou seu servidor MySQL
mysql -u seu_usuario -p

# Executar o schema
SOURCE database.sql;
```

### Passo 2: Configurar api/config.php
```php
<?php
$host = 'localhost';
$dbname = 'vila_abandonada';
$username = 'seu_usuario';
$password = 'sua_senha';

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
?>
```

### Passo 3: Migrar Dados Existentes (Opcional)
Acesse: `http://seu-site.com/api/migrate_mapjs.php`

### Passo 4: Usar o Editor v2
1. Faça login como admin
2. Acesse o Admin Panel
3. Clique em "Editor de Localizações"
4. Crie ou edite localizações
5. Clique em **"💾 Salvar"**
6. Pronto! O jogo já usa os novos dados

### Passo 5: Testar o Jogo
- Acesse `game-phaser.html`
- O jogo carrega automaticamente do banco
- Se o banco falhar, usa `map.js` como fallback

## 🔄 Fluxo de Dados

```
┌─────────────────────┐
│   Admin cria/edita  │
│   localização no    │
│   Editor v2         │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│  Clica em "Salvar"  │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│  POST /api/         │
│  locations/save.php │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│  Salvo no MySQL     │
│  (locations,        │
│   hotspots)         │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│  Jogador inicia     │
│  game-phaser.html   │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│  BootScene carrega  │
│  DatabaseLoader     │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│  GET /api/          │
│  locations/list.php │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│  Dados convertidos  │
│  para GAME_MAP      │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│  Jogo inicia com    │
│  dados do banco     │
└─────────────────────┘
```

## 🔧 Troubleshooting

### Erro: "Failed to load game data"
**Causa:** Banco de dados não configurado ou API inacessível
**Solução:**
1. Verifique `api/config.php`
2. Verifique se as tabelas foram criadas (execute `database.sql`)
3. Verifique permissões do MySQL
4. O jogo usará `map.js` como fallback

### Erro: "Location not found"
**Causa:** Localização não existe no banco
**Solução:**
1. Execute o script de migração: `api/migrate_mapjs.php`
2. Ou crie localizações manualmente no Editor v2

### Editor v2 não carrega lista
**Causa:** API não acessível
**Solução:**
1. Verifique se `api/locations/list.php` está acessível
2. Verifique console do navegador (F12) para erros
3. Verifique configuração do banco em `api/config.php`

## 📊 Comparação de Arquivos

| Arquivo | v1.0 | v2.0 |
|---------|------|------|
| `location-editor.html` | Editor estático, exporta JS | Mantido para compatibilidade |
| `location-editor-v2.html` | ❌ Não existe | ✅ Editor dinâmico com banco |
| `map.js` | ✅ Fonte de dados | ✅ Fallback apenas |
| `database.sql` | ❌ Não existe | ✅ Schema completo |
| `api/locations/*.php` | ❌ Não existe | ✅ APIs completas |
| `DatabaseLoader.js` | ❌ Não existe | ✅ Carrega do banco |
| `BootScene.js` | Carrega de map.js | Carrega via DatabaseLoader |

## 🎓 Próximos Passos

1. ✅ Sistema dinâmico funcional
2. ⏳ Interface visual para editar hotspots (drag-and-drop)
3. ⏳ Preview de imagens no editor
4. ⏳ Versionamento de localizações
5. ⏳ Sistema de rollback
6. ⏳ Importar/Exportar localizações em JSON

## 📝 Notas Importantes

- O sistema v2.0 é **totalmente compatível** com v1.0
- Se o banco falhar, o jogo usa `map.js` automaticamente
- Administradores podem editar simultaneamente (último salvo prevalece)
- Todas as alterações são salvas imediatamente no banco
- Não há necessidade de editar código JavaScript manualmente

## 🤝 Contribuindo

Para adicionar novas features ao sistema:

1. **Backend (PHP)**
   - Adicione novas APIs em `api/locations/` ou `api/items/`
   - Siga o padrão de resposta JSON existente

2. **Frontend (Editor)**
   - Modifique `location-editor-v2.html`
   - Use as APIs existentes via `fetch()`

3. **Game (Phaser)**
   - O jogo usa `GAME_MAP` global
   - Compatível automaticamente com dados do banco

## 📞 Suporte

Para problemas ou dúvidas:
1. Verifique este documento primeiro
2. Verifique logs do navegador (F12 → Console)
3. Verifique logs do PHP (error_log)
4. Contate o administrador do sistema
