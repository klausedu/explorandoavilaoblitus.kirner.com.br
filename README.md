# Vila Abandonada - Jogo Point-and-Click 2D

Um jogo de exploração e mistério onde você navega por uma vila abandonada, resolve enigmas e coleta itens para encontrar a saída.

## 📋 Visão Geral

- **Estilo**: Point-and-click 2D inspirado em Blackthorne/Dimensia
- **Locais**: 15 ambientes únicos + 1 mapa visual
- **Enigmas**: 12 puzzles de diferentes tipos
- **Itens**: 15 itens colecionáveis
- **Objetivo**: Coletar 7 itens-chave e escapar pelo portão principal

## 🎮 Como Jogar Localmente

### Método 1: Usando o Batch File (Windows)
```
1. Clique duas vezes em INICIAR_JOGO.bat
2. Aguarde o servidor iniciar
3. Abra http://localhost:8000/game-offline.html no navegador
```

### Método 2: Python Manual
```bash
python -m http.server 8000
# Abra http://localhost:8000/game-offline.html
```

### Método 3: Arquivo Direto
Simplesmente abra `game-offline.html` no navegador (pode ter limitações com imagens locais).

## 🛠️ Ferramentas Incluídas

### 1. Editor de Locais e Enigmas ⭐ NOVO
**Arquivo**: `location-editor.html`

O editor completo permite:
- ✏️ Editar informações de todos os locais
- 🆕 Adicionar novos locais ao jogo
- 🗑️ Remover locais existentes
- 🔄 Renomear locais (atualiza conexões automaticamente)
- 📁 **Fazer upload de imagens dos locais**
- 🖼️ **Posicionar itens PNG visualmente sobre a imagem**
- 🧩 Criar e editar enigmas (6 tipos diferentes)
- 💎 Adicionar itens colecionáveis
- 🎯 Configurar hotspots interativos
- 🔗 Gerenciar conexões entre locais
- 🔀 Reordenar locais por drag-and-drop
- 📦 Exportar código JavaScript atualizado

**Novidades v2.1**:
- 📸 Upload de imagens diretamente no editor
- 🎨 Editor visual para posicionar itens PNG transparentes
- ✨ Efeito de hover com zoom nos itens do jogo
- 🖱️ Arrastar itens para posicioná-los visualmente
- 📏 **Redimensionar itens** com handles visuais ou inputs numéricos
- 🔄 **Rotação** de -180° a 180° com slider
- 📐 **Escala independente** em X e Y para perspectiva 3D
- ↔️ **Espelhar** horizontal e vertical
- 🎯 **Seleção visual** com painel de controles dedicado

**Guias**:
- 📖 Instruções completas: `EDITOR_INSTRUCOES.md`
- ⚡ Início rápido: `EDITOR_INICIO_RAPIDO.md`
- 🖼️ **Posicionamento visual**: `POSICIONAMENTO_VISUAL_GUIA.md`
- 🔄 **Transformações de itens**: `TRANSFORMACOES_ITENS.md`

### 2. Editor Visual de Mapa
**Arquivo**: `map-editor.html`

Editor drag-and-drop para posicionar locais visualmente:
- 🖐️ Modo Move: Arraste locais para posicioná-los
- 🔗 Modo Connect: Clique em dois locais para conectar/desconectar
- 💾 Salvar/carregar layout
- 🎨 Auto-organizar em círculo
- 📤 Exportar código com posições

### 3. Mapa Interativo
**Arquivo**: `interactive-map.html`

Visualização interativa de todos os locais:
- 🗺️ Canvas com todas as localizações
- 🖼️ Mostra imagens reais de cada local
- 📝 Informações completas (descrição, enigmas, itens)
- 🔗 Navegação entre locais conectados

### 4. Gerador de Mapa
**Arquivo**: `map-generator.html`

Gera automaticamente a imagem do mapa:
- 🎨 Desenha todos os 15 locais
- 📍 Mostra conexões entre locais
- 📊 Inclui legenda e estatísticas
- 💾 Baixar como `village_map.jpg`

### 5. Visualizador de Conexões
**Arquivo**: `connection-visualizer.html`

Diagrama interativo de conexões:
- 📊 Grafo SVG com nós e links
- 🎨 Cores por tipo (início, fim, hubs)
- 📈 Estatísticas (15 locais, 30 conexões)
- 🔍 Destacar hubs e caminhos

## 📁 Estrutura do Projeto

```
vila_abandonada/
│
├── index.html                      # Página de login/registro
├── game.html                       # Jogo completo (requer backend)
├── game-offline.html               # Jogo offline (sem autenticação)
│
├── location-editor.html            # ⭐ NOVO: Editor de locais e enigmas
├── map-editor.html                 # Editor visual de posições
├── interactive-map.html            # Mapa interativo com imagens
├── map-generator.html              # Gerador automático do mapa
├── connection-visualizer.html      # Visualizador de conexões
│
├── js/
│   ├── auth.js                     # Autenticação
│   ├── game.js                     # Engine do jogo
│   └── map.js                      # Dados dos locais e enigmas
│
├── css/
│   └── style.css                   # Estilos do jogo
│
├── api/                            # Backend PHP
│   ├── config.php                  # Configuração do banco
│   ├── register.php                # Registro de usuário
│   ├── login.php                   # Login
│   ├── save-progress.php           # Salvar progresso
│   ├── load-progress.php           # Carregar progresso
│   └── logout.php                  # Logout
│
├── images/                         # 16 imagens (15 locais + 1 mapa)
│   ├── forest_entrance.jpg
│   ├── main_square.jpg
│   ├── ...
│   └── village_map.jpg
│
├── database.sql                    # Schema do banco MySQL
│
├── INICIAR_JOGO.bat               # Iniciar servidor (Windows)
│
└── Documentação/
    ├── README.md                   # ⭐ Este arquivo
    ├── EDITOR_INSTRUCOES.md        # ⭐ Instruções do editor
    ├── EDITOR_INICIO_RAPIDO.md     # ⭐ Início rápido do editor
    ├── COMO_RODAR_LOCALMENTE.md    # Como testar localmente
    ├── INICIO_RAPIDO.md            # Guia de início rápido
    ├── COMO_CRIAR_MAPA.md          # Como criar o mapa visual
    ├── IMAGES_NEEDED.md            # Especificações das imagens
    ├── MAP_CONNECTIONS.md          # Documentação das conexões
    └── PUZZLE_SOLUTIONS.md         # Soluções dos enigmas
```

## 🎯 Fluxo de Trabalho Completo

### Para Desenvolvedores/Designers

1. **Editar Conteúdo do Jogo**
   ```
   → Abra location-editor.html
   → Edite locais, enigmas, itens
   → Exporte o código
   → Atualize js/map.js
   ```

2. **Posicionar Locais no Mapa**
   ```
   → Abra map-editor.html
   → Arraste locais para posicioná-los
   → Desenhe conexões
   → Exporte o código (opcional)
   ```

3. **Gerar Imagem do Mapa**
   ```
   → Abra map-generator.html
   → Clique em "Baixar Mapa"
   → Salve como images/village_map.jpg
   ```

4. **Testar o Jogo**
   ```
   → Execute INICIAR_JOGO.bat
   → Abra game-offline.html
   → Jogue e teste tudo
   ```

5. **Publicar**
   ```
   → Configure banco de dados MySQL
   → Faça upload de todos os arquivos
   → Configure api/config.php com dados do banco
   → Teste em https://seusite.com
   ```

## 🗺️ Os 15 Locais do Jogo

1. **Entrada da Floresta** (forest_entrance) - Início do jogo
2. **Praça Central** (main_square) - Hub principal
3. **Portão da Vila** (village_gate) - Saída final (requer 7 itens)
4. **Igreja Antiga** (old_church) - Enigma de charada
5. **Casa Abandonada** (abandoned_house) - Enigma de sequência
6. **Prefeitura** (town_hall) - Enigma matemático
7. **Moinho Antigo** (old_mill) - Enigma de código
8. **Jardim Esquecido** (forgotten_garden) - Itens especiais
9. **Túnel Subterrâneo** (underground_tunnel) - Passagem entre áreas
10. **Passagem Secreta** (secret_passage) - Conexão escondida
11. **Cemitério** (cemetery) - Área misteriosa
12. **Poço Antigo** (old_well) - Item especial
13. **Loja Abandonada** (abandoned_shop) - Item especial
14. **Ferraria** (blacksmith) - Enigma de combinação
15. **Biblioteca** (library) - Livros e segredos

## 🧩 Tipos de Enigmas

O jogo suporta 6 tipos diferentes de enigmas:

| Tipo | Descrição | Exemplo |
|------|-----------|---------|
| **direction** | 4 direções cardeais | "Qual direção a água fluía?" |
| **riddle** | Charada com múltiplas opções | "O que sou eu?" |
| **sequence_symbols** | Pressionar símbolos na ordem | Sol → Lua → Estrela |
| **math** | Problema matemático | População ÷ 25 = ? |
| **code** | Código numérico | Digite 1234 |
| **item_combination** | Combinar múltiplos itens | Use engrenagem + chave |

## 🎨 Sistema de Itens Visuais

### Itens PNG Transparentes
Os itens podem ser renderizados como imagens PNG transparentes posicionadas sobre a imagem do local:

**Características**:
- 📍 Posicionamento preciso com editor visual
- ✨ Efeito de hover com zoom suave (15%)
- 🖼️ PNGs transparentes para melhor integração
- 📐 Tamanho configurável com handles ou inputs
- 🔄 Rotação de -180° a 180°
- 📐 Escala independente em X e Y para perspectiva
- ↔️ Espelhamento horizontal e vertical
- 🎯 Clicáveis para coletar

**Como funciona**:
1. Faça upload da imagem PNG do item no editor
2. Arraste o item sobre a imagem do local para posicioná-lo
3. Clique no item para selecioná-lo
4. Use os controles para transformar (redimensionar, rotar, escalar, espelhar)
5. As transformações são salvas e aplicadas no jogo

**Formato dos dados**:
```javascript
item: {
    id: 'old_coin',
    name: 'Moeda Antiga',
    imageData: 'data:image/png;base64,...',  // PNG em base64
    position: { x: 45, y: 70 },              // % da imagem
    size: { width: 80, height: 80 },         // pixels
    transform: {
        rotation: 25,                        // graus
        scaleX: 1.2,                         // perspectiva X
        scaleY: 0.8,                         // perspectiva Y
        flipX: false,                        // espelhar X
        flipY: false                         // espelhar Y
    }
}
```

**Guias completos**:
- `POSICIONAMENTO_VISUAL_GUIA.md` para posicionamento
- `TRANSFORMACOES_ITENS.md` para transformações avançadas

## 💾 Sistema de Salvamento

### Versão Online (game.html)
- Salva no banco MySQL via PHP
- Login necessário
- Progresso sincronizado entre dispositivos

### Versão Offline (game-offline.html)
- Salva no localStorage do navegador
- Não requer login
- Progresso local apenas

Ambas as versões salvam:
- Local atual
- Locais visitados
- Itens coletados
- Enigmas resolvidos
- Inventário completo
- Imagens e posições dos itens PNG

## 🚀 Deploy no Hostinger

1. **Preparar Banco de Dados**
   - Crie banco MySQL no cPanel
   - Execute o script `database.sql`
   - Anote: nome do banco, usuário, senha, host

2. **Configurar Backend**
   - Abra `api/config.php`
   - Atualize credenciais do banco:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'seu_banco');
     define('DB_USER', 'seu_usuario');
     define('DB_PASS', 'sua_senha');
     ```

3. **Upload de Arquivos**
   - Faça upload de todos os arquivos via FTP/cPanel
   - Mantenha a estrutura de pastas

4. **Testar**
   - Acesse https://seudominio.com
   - Crie uma conta
   - Teste o jogo completo

## 🎨 Personalizando o Jogo

### Adicionar Novos Locais
1. Abra `location-editor.html`
2. Clique em "**+ Adicionar Novo Local**"
3. Configure todas as propriedades
4. Conecte com outros locais
5. Exporte e atualize `js/map.js`

### Modificar Enigmas Existentes
1. Abra `location-editor.html`
2. Selecione o local
3. Vá para aba **🧩 Enigma**
4. Edite conforme necessário
5. Exporte e atualize `js/map.js`

### Adicionar Imagens
1. Adicione a imagem na pasta `images/`
2. No `location-editor.html`, configure o caminho
3. Formato recomendado: 1920x1080, JPG, <500KB

### Alterar Tema Visual
1. Abra `css/style.css`
2. Modifique cores, fontes, espaçamentos
3. Teste no `game-offline.html`

## 📊 Estatísticas do Jogo

- **15 locais** únicos para explorar
- **12 enigmas** de diferentes tipos
- **15 itens** colecionáveis
- **7 itens-chave** necessários para vencer
- **30 conexões** bidirecionais entre locais
- **3 hubs** principais (Praça Central, Casa Abandonada, Passagem Secreta)

## 🔧 Solução de Problemas

### Imagens não aparecem
- Verifique se as imagens estão na pasta `images/`
- Use servidor HTTP (não abra direto do arquivo)
- Execute `INICIAR_JOGO.bat` ou `python -m http.server 8000`

### Mudanças no editor não aparecem no jogo
- Certifique-se de clicar em "**📦 Exportar Código**"
- Copie o código gerado
- Substitua o conteúdo de `js/map.js`
- Recarregue a página do jogo (Ctrl+F5)

### Progresso não salva (offline)
- Verifique se está usando um navegador moderno
- Verifique se o localStorage está habilitado
- Não use modo anônimo/privado

### Erro no backend (online)
- Verifique credenciais em `api/config.php`
- Verifique se o banco foi criado
- Verifique logs de erro do servidor
- Teste os endpoints PHP diretamente

### Editor não carrega dados
- Recarregue a página
- Limpe o cache do navegador
- Se perdeu dados, use um backup do código exportado

## 📖 Documentação Adicional

- **EDITOR_INSTRUCOES.md** - Guia completo do editor de locais
- **EDITOR_INICIO_RAPIDO.md** - Início rápido com exemplos
- **POSICIONAMENTO_VISUAL_GUIA.md** - ⭐ Guia de posicionamento de itens PNG
- **TRANSFORMACOES_ITENS.md** - ⭐ Guia de transformações (rotação, escala, flip)
- **PUZZLE_SOLUTIONS.md** - Todas as soluções dos enigmas
- **MAP_CONNECTIONS.md** - Diagrama completo de conexões
- **IMAGES_NEEDED.md** - Especificações das 16 imagens
- **COMO_RODAR_LOCALMENTE.md** - Múltiplas opções para testar
- **COMO_CRIAR_MAPA.md** - 4 formas de criar o mapa visual

## 🤝 Contribuindo

Este é um projeto de jogo point-and-click completo e personalizável. Sinta-se livre para:

- Adicionar novos locais
- Criar novos tipos de enigmas
- Melhorar o visual
- Adicionar sons e música
- Criar novos recursos

Use o `location-editor.html` para fazer mudanças facilmente!

## 📄 Licença

Projeto criado como exemplo educacional. Use e modifique como desejar.

---

**Desenvolvido com**: HTML5, CSS3, JavaScript Vanilla, PHP 7.4+, MySQL 5.7+

**Inspirado em**: Blackthorne, Dimensia, jogos point-and-click clássicos

**Versão**: 2.1 (com Posicionamento Visual de Itens)

---

## ⚡ Início Rápido em 3 Passos

```
1. Execute: INICIAR_JOGO.bat
2. Abra: http://localhost:8000/game-offline.html
3. Jogue!
```

**Para editar o jogo**:
```
1. Abra: http://localhost:8000/location-editor.html
2. Edite tudo que quiser
3. Exporte o código e atualize js/map.js
```

**Divirta-se! 🎮**