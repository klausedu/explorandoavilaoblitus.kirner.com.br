/**
 * Phaser Game Configuration
 * Configuração principal e inicialização do jogo
 */

console.log('🎮 Vila Abandonada - Phaser Edition');
console.log('📦 Carregando dados do jogo...');

// Configuração do Phaser
const config = {
    type: Phaser.AUTO,
    parent: 'game-container',
    width: 1280,
    height: 720,
    backgroundColor: '#000000',
    scale: {
        mode: Phaser.Scale.RESIZE,
        autoCenter: Phaser.Scale.CENTER_BOTH
    },
    dom: {
        createContainer: true
    },
    scene: [BootScene, LocationScene]
};

// Inicializar jogo
let game;

function initGame() {
    console.log('📋 Locações carregadas:', Object.keys(GAME_MAP).length);

    // Inicializar Phaser
    game = new Phaser.Game(config);
    console.log('✓ Jogo inicializado');
}

// Iniciar quando página carregar
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initGame);
} else {
    initGame();
}
