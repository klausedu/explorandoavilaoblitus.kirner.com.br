# Como Deve Aparecer a UI do Jogo

## Top Bar (Barra Superior)

```
┌───────────────────────────────────────────────────────────┐
│ Vila Abandonada | 👤 seu_username [ADMIN] | 🎒 💾 🔄 🚪  │
└───────────────────────────────────────────────────────────┘
```

### Lado Esquerdo:
- **Vila Abandonada** (texto amarelo/laranja `#f0a500`)
- **|** (separador)
- **👤** (emoji de usuário)
- **seu_username** (texto cinza `#ccc`)
- **[ADMIN]** (badge rosa - só aparece se for admin)

### Lado Direito (Botões):
- **🎒** Inventário (amarelo/laranja)
- **💾** Salvar (amarelo/laranja)
- **🔄** Resetar (amarelo/laranja)
- **🚪** Sair (vermelho `#f44336`)

## Características Visuais:

1. **Fundo**: Gradiente preto transparente
2. **Altura**: 60px
3. **Padding**: 20px nas laterais
4. **Botões**: 40x40px com bordas arredondadas
5. **Hover**: Botões crescem um pouco (scale 1.1)

## Verificar no Console (F12):

Deve aparecer:
```
✓ Logged in as: seu_username
```

E ao inspecionar elementos, deve existir:
- `<div id="top-bar">`
- `<div id="phaser-ui">`

## Se NÃO aparecer:

1. **Ctrl + F5** (hard refresh)
2. **Feche e abra o navegador**
3. **Limpe cache**: Configurações → Privacidade → Limpar dados
4. **Verifique console (F12)** se há erros em vermelho
5. **Confirme que está na versão local atualizada**, não no servidor

## Testando se está funcionando:

Abra o Console (F12) e digite:
```javascript
document.getElementById('top-bar')
```

Se retornar `null` → UI não foi criada
Se retornar `<div id="top-bar">...` → UI foi criada ✓
