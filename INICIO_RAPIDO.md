# ⚡ Início Rápido - Vila Abandonada

Teste o jogo no seu PC em **3 passos simples!**

---

## 🎮 Método 1: Automático (Windows)

### Passo 1: Duplo-clique no arquivo
```
INICIAR_JOGO.bat
```

### Passo 2: Abra o navegador
```
http://localhost:8000/game-offline.html
```

✅ **PRONTO!**

---

## 🎮 Método 2: Manual

### Passo 1: Abra o CMD na pasta
- Abra a pasta `claude_oblitus` no Explorer
- Clique na barra de endereços
- Digite `cmd` e Enter

### Passo 2: Execute
```bash
python -m http.server 8000
```

### Passo 3: Abra o navegador
```
http://localhost:8000/game-offline.html
```

✅ **PRONTO!**

---

## 🗺️ Outros Arquivos para Testar

Depois que o servidor estiver rodando:

### Jogo:
```
http://localhost:8000/game-offline.html
```

### Mapa Interativo (ver imagens + conexões):
```
http://localhost:8000/interactive-map.html
```

### Visualizador de Conexões:
```
http://localhost:8000/connection-visualizer.html
```

### Gerador de Mapa:
```
http://localhost:8000/map-generator.html
```

---

## 🎯 Controles do Jogo

- **🔗** Ver conexões do local atual
- **💾** Salvar progresso
- **🗺️** Abrir mapa
- **🎒** Ver inventário
- **🔄** Resetar jogo

---

## 💡 Dicas

### Salvar Progresso:
- ✅ Salva automaticamente ao navegar
- ✅ Clique em 💾 para salvar manual
- ✅ Dados ficam no navegador

### Resetar:
- Clique no botão 🔄
- Ou: F12 → Console → `localStorage.clear()`

### Ver Erros:
- Pressione F12 (DevTools)
- Vá na aba Console
- Veja mensagens de erro

---

## ❓ Problemas?

### Imagens não aparecem?
✅ Confirme que está usando servidor HTTP (não abrindo direto o arquivo)
✅ Verifique que as imagens estão em `images/`

### Python não encontrado?
✅ Instale: https://www.python.org/downloads/
✅ Marque "Add to PATH" durante instalação

### Porta 8000 ocupada?
✅ Use outra porta: `python -m http.server 8080`
✅ Acesse: `http://localhost:8080/game-offline.html`

---

## 📚 Documentação Completa

Veja: **`COMO_RODAR_LOCALMENTE.md`**

---

## 🚀 Pronto para o Hostinger?

Quando tudo estiver funcionando:

1. ✅ Testou o jogo completo
2. ✅ Todos os locais funcionam
3. ✅ Puzzles resolvem
4. ✅ Salvamento funciona
5. ✅ Imagens aparecem

📤 **Veja:** `README.md` para instruções de deploy no Hostinger!

---

**Divirta-se jogando! 🎉**
