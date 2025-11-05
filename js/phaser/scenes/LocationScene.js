/**
 * LocationScene
 * Cena principal onde o jogo acontece
 * Cada local é renderizado aqui dinamicamente
 */
class LocationScene extends Phaser.Scene {
    constructor() {
        super({ key: 'LocationScene' });
        this.currentLocation = null;
        this.locationData = null;
        this.hotspots = [];
        this.items = [];
    }

    init(data) {
        // Recebe locationId de outras cenas
        if (data && data.locationId) {
            this.currentLocation = data.locationId;
        }
    }

    create() {
        // Obter dados da location
        this.locationData = databaseLoader.getLocation(this.currentLocation);

        if (!this.locationData) {
            console.error('Location not found:', this.currentLocation);
            return;
        }
        // Renderizar background
        this.renderBackground();

        // Renderizar hotspots
        this.renderHotspots();

        // Renderizar itens
        this.renderItems();

        // Atualizar UI
        uiManager.updateLocationInfo(this.locationData);

        // Fade in
        this.cameras.main.fadeIn(300, 0, 0, 0);

        // Listener para redimensionamento (Scale.RESIZE muda dimensões do game)
        this.scale.on('resize', this.handleResize, this);
    }

    handleResize(gameSize) {
        const width = gameSize.width;
        const height = gameSize.height;

        // Reposicionar/reescalar background
        if (this.background) {
            this.background.setPosition(width / 2, height / 2);
            const scaleX = width / this.background.texture.source[0].width;
            const scaleY = height / this.background.texture.source[0].height;
            const scale = Math.min(scaleX, scaleY);
            this.background.setScale(scale);
        }

        // Recalcular posições de hotspots e itens
        this.repositionAll(width, height);
    }

    repositionAll(width, height) {
        // Limpar e recriar hotspots e itens
        this.hotspots.forEach(h => {
            h.zone.destroy();
            h.label.destroy();
        });
        this.items.forEach(i => {
            if (i.sprite) i.sprite.destroy();
        });

        this.hotspots = [];
        this.items = [];

        this.renderHotspots();
        this.renderItems();
    }

    // Helper: calcula dimensões e posição do background
    getBackgroundBounds() {
        const width = this.cameras.main.width;
        const height = this.cameras.main.height;

        // Handle "fake" background object when image is missing
        if (!this.background || !this.background.texture) {
            return {
                width: width,
                height: height,
                bgWidth: width,
                bgHeight: height,
                bgX: 0,
                bgY: 0
            };
        }

        const bg = this.background;
        const bgWidth = bg.displayWidth;
        const bgHeight = bg.displayHeight;
        const bgX = bg.x - (bgWidth / 2);
        const bgY = bg.y - (bgHeight / 2);

        return { width, height, bgWidth, bgHeight, bgX, bgY };
    }

    renderBackground() {
        const width = this.cameras.main.width;
        const height = this.cameras.main.height;

        // Verificar se a textura foi carregada
        const hasTexture = this.textures.exists(this.currentLocation);

        if (hasTexture) {
            // Adicionar imagem de fundo
            const bg = this.add.image(width / 2, height / 2, this.currentLocation);

            // Ajustar escala para preencher tela (contain)
            const scaleX = width / bg.width;
            const scaleY = height / bg.height;
            const scale = Math.min(scaleX, scaleY);
            bg.setScale(scale);

            // Salvar referência
            this.background = bg;
        } else {
            // Fallback: criar retângulo colorido se imagem não carregou
            console.warn('⚠️ Imagem não encontrada para:', this.currentLocation);

            const bg = this.add.rectangle(width / 2, height / 2, width, height, 0x2a2a2a);

            // Adicionar texto indicando que a imagem está faltando
            const errorText = this.add.text(width / 2, height / 2,
                `❌ Imagem não encontrada\n\n${this.locationData.name}\n\nID: ${this.currentLocation}\nPath esperado: ${this.locationData.image}`, {
                fontSize: '24px',
                color: '#f0a500',
                align: 'center',
                backgroundColor: '#000000',
                padding: { x: 20, y: 20 }
            });
            errorText.setOrigin(0.5);

            // Criar objeto "fake" que funciona como imagem
            this.background = {
                width: width,
                height: height,
                scale: 1,
                x: width / 2,
                y: height / 2,
                texture: null, // Add texture property to avoid errors
                setOrigin: () => { }, // Add a dummy setOrigin method
                setPosition: (x, y) => {
                    this.background.x = x;
                    this.background.y = y;
                },
                setScale: (scale) => {
                    this.background.scale = scale;
                }
            };
        }
    }

    renderHotspots() {
        if (!this.locationData.hotspots) return;

        const { bgWidth, bgHeight, bgX, bgY } = this.getBackgroundBounds();

        this.locationData.hotspots.forEach(hotspot => {
            if (!hotspot.position) {
                console.warn('Hotspot sem dados de posição:', hotspot);
                return; // Pula este hotspot se não tiver posição
            }

            const x = bgX + (hotspot.position.x / 100) * bgWidth;
            const y = bgY + (hotspot.position.y / 100) * bgHeight;
            const w = (hotspot.position.width / 100) * bgWidth;
            const h = (hotspot.position.height / 100) * bgHeight;

            // Criar área interativa (invisível)
            const zone = this.add.zone(x + w / 2, y + h / 2, w, h);
            zone.setInteractive({ useHandCursor: true });
            zone.setOrigin(0.5);

            // Label (nome do destino)
            const label = this.add.text(x + w / 2, y + h + 10, hotspot.label, {
                fontSize: '18px',
                color: '#f0a500',
                backgroundColor: '#000000',
                padding: { x: 10, y: 6 },
                fontStyle: 'bold'
            });
            label.setOrigin(0.5, 0);
            label.setAlpha(0); // Invisível por padrão

            // Hover effects - apenas label
            zone.on('pointerover', () => {
                label.setAlpha(1);
                // Animação suave de fade in
                this.tweens.add({
                    targets: label,
                    alpha: 1,
                    y: label.y - 5,
                    duration: 200,
                    ease: 'Power2'
                });
            });

            zone.on('pointerout', () => {
                this.tweens.killTweensOf(label);
                label.setAlpha(0);
                label.setY(y + h + 10); // Reset posição
            });

            // Click handler
            zone.on('pointerdown', () => {
                this.handleHotspotClick(hotspot);
            });

            // Salvar referências
            this.hotspots.push({
                zone,
                label,
                data: hotspot
            });
        });
    }

    renderItems() {
        if (!this.locationData.items) return;

        const { bgWidth, bgHeight, bgX, bgY } = this.getBackgroundBounds();

        this.locationData.items.forEach(item => {
            if (gameStateManager.isItemCollected(item.id)) return;
            if (!item.image || !item.position) {
                console.warn('Item sem dados de imagem ou posição:', item);
                return;
            }

            const x = bgX + (item.position.x / 100) * bgWidth;
            const y = bgY + (item.position.y / 100) * bgHeight;

            const transform = item.transform || {};
            let element;

            // Se tem qualquer transformação, usar DOMElement para ter mais controle
            if (item.transform) {
                // Criar elemento HTML <img>
                const img = document.createElement('img');
                img.src = item.image;
                img.style.width = (item.size?.width || 80) + 'px';
                img.style.height = (item.size?.height || 80) + 'px';
                img.style.pointerEvents = 'auto';

                // Criar DOMElement
                element = this.add.dom(x, y, img);
                element.setOrigin(0.5);

                // Aplicar perspectiva via Phaser
                element.setPerspective(800);

                // Aplicar rotações 3D via Phaser API (no container)
                const rotX = transform.rotateX || 0;
                const rotY = transform.rotateY || 0;

                if (rotX !== 0 && rotY !== 0) {
                    element.rotate3d.set(1, 0, 0, rotX);
                } else if (rotX !== 0) {
                    element.rotate3d.set(1, 0, 0, rotX);
                } else if (rotY !== 0) {
                    element.rotate3d.set(0, 1, 0, rotY);
                }

                // Construir transformações 2D via CSS (na imagem, não no container!)
                const transforms = [];

                // Rotação 2D (Z axis)
                const rotation = transform.rotation || 0;
                if (rotation !== 0) transforms.push(`rotate(${rotation}deg)`);

                // Escala (com flip integrado) - PRIORIDADE!
                const baseScaleX = transform.scaleX || 1;
                const baseScaleY = transform.scaleY || 1;
                const flipX = transform.flipX ? -1 : 1;
                const flipY = transform.flipY ? -1 : 1;
                const finalScaleX = baseScaleX * flipX;
                const finalScaleY = baseScaleY * flipY;
                transforms.push(`scale(${finalScaleX}, ${finalScaleY})`);

                // Skew (distorção) - pode ver depois
                const skewX = transform.skewX || 0;
                const skewY = transform.skewY || 0;
                if (skewX !== 0) transforms.push(`skewX(${skewX}deg)`);
                if (skewY !== 0) transforms.push(`skewY(${skewY}deg)`);

                // Aplicar na IMAGEM (não no container com rotate3d)
                const transformString = transforms.join(' ');
                img.style.transform = transformString;

                // DEBUG
                console.log('🎨 Item:', item.id);
                console.log('  Transform object:', transform);
                console.log('  Scale values:', {baseScaleX, baseScaleY, flipX, flipY, finalScaleX, finalScaleY});
                console.log('  CSS 2D transforms:', transformString);
                console.log('  Applied to img:', img.style.transform);

                // Aplicar opacidade via CSS
                img.style.opacity = transform.opacity ?? 1;

                // Aplicar sombra via CSS filter (drop-shadow)
                const shadowBlur = transform.shadowBlur || 0;
                const shadowX = transform.shadowOffsetX || 0;
                const shadowY = transform.shadowOffsetY || 0;
                if (shadowBlur > 0) {
                    img.style.filter = `drop-shadow(${shadowX}px ${shadowY}px ${shadowBlur}px rgba(0,0,0,0.5))`;
                } else {
                    img.style.filter = 'none';
                }

                // Salvar transform original para hover
                const originalTransform = img.style.transform;

                // Eventos de hover/click via DOMElement
                element.addListener('pointerover');
                element.on('pointerover', () => {
                    img.style.filter = 'brightness(1.2)';
                    img.style.transition = 'filter 0.2s ease';
                });

                element.addListener('pointerout');
                element.on('pointerout', () => {
                    img.style.filter = '';
                });

                element.addListener('pointerdown');
                element.on('pointerdown', () => {
                    this.collectItem(item, element);
                });

            } else {
                // Sem rotação 3D - usar Phaser Image normal
                const textureKey = 'item_' + item.id;
                const hasTexture = this.textures.exists(textureKey);

                if (hasTexture) {
                    element = this.add.image(x, y, textureKey);

                    // Set size
                    if (item.size) {
                        element.setDisplaySize(item.size.width, item.size.height);
                    } else {
                        element.setDisplaySize(80, 80);
                    }

                    // Apply transformations
                    element.setAngle(transform.rotation || 0);
                    if (transform.flipX) element.flipX = true;
                    if (transform.flipY) element.flipY = true;
                    if (transform.opacity !== undefined) {
                        element.setAlpha(transform.opacity);
                    }
                    element.setOrigin(0.5);

                    // Make it interactive
                    element.setInteractive({ useHandCursor: true });

                    // Store original scale
                    const originalScaleX = element.scaleX;
                    const originalScaleY = element.scaleY;

                    // Hover effect
                    element.on('pointerover', () => {
                        this.tweens.add({
                            targets: element,
                            scaleX: originalScaleX * 1.15,
                            scaleY: originalScaleY * 1.15,
                            duration: 200,
                            ease: 'Power2'
                        });
                    });

                    element.on('pointerout', () => {
                        this.tweens.killTweensOf(element);
                        element.setScale(originalScaleX, originalScaleY);
                    });

                    // Click handler
                    element.on('pointerdown', () => {
                        this.collectItem(item, element);
                    });
                } else {
                    // Fallback: create rectangle with emoji
                    console.warn('⚠️ Item image not found:', item.id, item.image);
                    element = this.add.text(x, y, '📦', {
                        fontSize: '48px'
                    });
                    element.setOrigin(0.5);
                }
            }

            this.items.push({ sprite: element, data: item });
        });
    }

    handleHotspotClick(hotspot) {
        console.log('🖱️ Clique no hotspot:', hotspot);

        switch (hotspot.action) {
            case 'navigate':
            case 'navigation':
                // Aceitar ambos target e targetLocation para compatibilidade
                const targetId = hotspot.targetLocation || hotspot.target;
                console.log('➡️ Navegando para:', targetId);
                this.navigateToLocation(targetId, hotspot);
                break;

            case 'puzzle':
                this.openPuzzle(hotspot.puzzleId);
                break;

            case 'collect':
                const item = this.locationData.items?.find(i => i.id === hotspot.itemId);
                if (item) {
                    this.collectItem(item);
                }
                break;

            case 'interact':
                uiManager.showNotification('Nada de interessante aqui...');
                break;
        }
    }

    navigateToLocation(targetLocationId, hotspot) {
        console.log('🚀 navigateToLocation chamada:', targetLocationId);

        if (!targetLocationId) {
            console.error('❌ targetLocationId está vazio ou undefined!');
            return;
        }

        // Verificar se o destino existe
        const targetExists = databaseLoader.getLocation(targetLocationId);
        if (!targetExists) {
            console.error('❌ Localização de destino não encontrada:', targetLocationId);
            uiManager.showNotification('Localização não encontrada: ' + targetLocationId);
            return;
        }

        console.log('✅ Destino encontrado:', targetExists.name);

        const { bgWidth, bgHeight, bgX, bgY } = this.getBackgroundBounds();

        const centerX = bgX + ((hotspot.position.x + hotspot.position.width / 2) / 100) * bgWidth;
        const centerY = bgY + ((hotspot.position.y + hotspot.position.height / 2) / 100) * bgHeight;

        // Determinar tipo de zoom baseado no hotspot
        const zoomDirection = hotspot.zoomDirection || 'in'; // Default: zoom in
        let zoomLevel = 1;

        if (zoomDirection === 'in') {
            zoomLevel = 2.5; // Zoom in (aproximar)
        } else if (zoomDirection === 'out') {
            zoomLevel = 0.5; // Zoom out (afastar)
        } else {
            zoomLevel = 1; // Sem zoom
        }

        console.log('🎬 Iniciando animação de zoom...');

        // Animação de pan e zoom
        this.cameras.main.pan(centerX, centerY, 700, 'Cubic.easeInOut');
        this.cameras.main.zoomTo(zoomLevel, 700, 'Cubic.easeInOut');

        // Fade out e trocar cena
        this.time.delayedCall(500, () => {
            console.log('🎬 Fade out iniciado...');
            this.cameras.main.fadeOut(200, 0, 0, 0);
        });

        this.cameras.main.once('camerafadeoutcomplete', () => {
            console.log('🎬 Fade out completo! Trocando para:', targetLocationId);

            // Atualizar estado
            gameStateManager.navigateToLocation(targetLocationId);

            // Reiniciar cena com nova location
            console.log('🔄 Reiniciando cena...');
            this.scene.restart({ locationId: targetLocationId });
        });
    }

    collectItem(item, element) {
        const collected = gameStateManager.collectItem(item);

        if (collected) {
            uiManager.showNotification(`✓ Você pegou: ${item.name}`);

            // Animação de coleta
            if (element) {
                this.tweens.add({
                    targets: element,
                    y: element.y - 100,
                    alpha: 0,
                    duration: 500,
                    ease: 'Power2',
                    onComplete: () => {
                        element.destroy();
                    }
                });
            }
        } else {
            uiManager.showNotification('Você já pegou este item');
        }
    }

    openPuzzle(puzzleId) {
        // TODO: Implementar sistema de puzzles
        uiManager.showNotification('Sistema de puzzles em desenvolvimento');
    }

    shutdown() {
        // Cleanup
        this.hotspots = [];
        this.items = [];

        // Remover listener de resize
        this.scale.off('resize', this.handleResize, this);
    }
}
