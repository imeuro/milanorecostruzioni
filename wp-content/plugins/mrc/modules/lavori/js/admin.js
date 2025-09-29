/**
 * MRC Lavori Admin Script
 * Gestisce l'interfaccia admin per la galleria immagini dei lavori
 * Vanilla JavaScript - No jQuery dependency
 */

(function() {
    'use strict';
    
    let mediaUploader;
    let galleryData = [];
    
    /**
     * Inizializza il modulo quando il documento è pronto
     */
    document.addEventListener('DOMContentLoaded', function() {
        // Verifica che siamo nella pagina corretta
        const galleryContainer = document.getElementById('mrc-gallery-container');
        if (!galleryContainer) {
            return;
        }
        
        initGalleryUpload();
        initGallerySortable();
        initGalleryActions();
        loadExistingGallery();
    });
    
    /**
     * Inizializza l'upload della galleria
     */
    function initGalleryUpload() {
        const uploadBtn = document.getElementById('mrc-gallery-upload-btn');
        const uploadArea = document.getElementById('mrc-gallery-upload-area');
        
        if (!uploadBtn || !uploadArea) return;
        
        // Click sul pulsante di upload
        uploadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openMediaUploader();
        });
        
        // Drag & drop area
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                uploadFiles(files);
            }
        });
        
        // Click sull'area di upload
        uploadArea.addEventListener('click', function(e) {
            if (e.target === this || e.target.classList.contains('mrc-upload-area')) {
                openMediaUploader();
            }
        });
    }
    
    /**
     * Apre il media uploader di WordPress
     */
    function openMediaUploader() {
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        
        mediaUploader = wp.media({
            title: mrcLavori.strings.selectImages,
            button: {
                text: mrcLavori.strings.selectImages
            },
            multiple: true,
            library: {
                type: 'image'
            }
        });
        
        mediaUploader.on('select', function() {
            const selection = mediaUploader.state().get('selection');
            const selectedImages = [];
            
            selection.map(function(attachment) {
                selectedImages.push(attachment.toJSON());
            });
            
            addImagesToGallery(selectedImages);
        });
        
        mediaUploader.open();
    }
    
    /**
     * Upload files via drag & drop
     */
    function uploadFiles(files) {
        const maxFiles = mrcLavori.maxImages || 20;
        
        if (files.length > maxFiles) {
            alert(mrcLavori.strings.maxImages);
            return;
        }
        
        // Mostra indicatore di caricamento
        showUploadProgress();
        
        // Upload ogni file singolarmente
        const uploadPromises = Array.from(files).map((file, index) => {
            if (!file.type.startsWith('image/')) {
                return Promise.resolve(null);
            }
            
            return uploadSingleFile(file, index);
        });
        
        // Attendi che tutti gli upload siano completati
        Promise.all(uploadPromises)
            .then(results => {
                hideUploadProgress();
                const validResults = results.filter(result => result !== null);
                if (validResults.length > 0) {
                    addImagesToGallery(validResults);
                }
            })
            .catch(error => {
                hideUploadProgress();
                showError(mrcLavori.strings.uploadError);
                console.error('Upload error:', error);
            });
    }
    
    /**
     * Upload di un singolo file
     */
    function uploadSingleFile(file, index) {
        const formData = new FormData();
        
        // Aggiungi il file
        formData.append('async-upload', file);
        formData.append('action', 'mrc_upload_image');
        
        // Aggiungi nonce per l'upload
        const nonceInput = document.getElementById('mrc-lavori-nonce');
        if (nonceInput) {
            formData.append('_wpnonce', nonceInput.value);
        }
        
        return fetch(mrcLavori.ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                return {
                    id: data.data.id,
                    url: data.data.url,
                    alt: data.data.alt || file.name,
                    sizes: {
                        thumbnail: { url: data.data.thumbnail }
                    }
                };
            } else {
                throw new Error(data.data.message || 'Upload failed');
            }
        })
        .catch(error => {
            console.error('Single file upload error:', error);
            return null;
        });
    }
    
    
    /**
     * Aggiunge immagini alla galleria
     */
    function addImagesToGallery(images) {
        const maxImages = mrcLavori.maxImages || 20;
        
        if (galleryData.length + images.length > maxImages) {
            alert(mrcLavori.strings.maxImages);
            return;
        }
        
        images.forEach(function(image) {
            if (galleryData.indexOf(image.id) === -1) {
                galleryData.push(image.id);
                renderGalleryItem(image);
            }
        });
        
        // Aggiorna gli indici dopo aver aggiunto le immagini
        updateGalleryIndices();
        updateGalleryData();
        updateUploadAreaVisibility();
    }
    
    /**
     * Renderizza un elemento della galleria
     */
    function renderGalleryItem(image) {
        const index = galleryData.indexOf(image.id);
        const itemHtml = `
            <div class="mrc-gallery-item" data-image-id="${image.id}" data-index="${index}">
                <div class="mrc-gallery-item-handle">
                    <span class="dashicons dashicons-menu"></span>
                </div>
                <div class="mrc-gallery-item-image">
                    <img src="${image.sizes.thumbnail ? image.sizes.thumbnail.url : image.url}" alt="${image.alt || ''}">
                </div>
                <div class="mrc-gallery-item-actions">
                    <button type="button" class="button button-small mrc-remove-image" title="${mrcLavori.strings.removeImage}">
                        <span class="dashicons dashicons-trash"></span>
                    </button>
                </div>
            </div>
        `;
        
        const galleryPreview = document.getElementById('mrc-gallery-preview');
        if (galleryPreview) {
            galleryPreview.insertAdjacentHTML('beforeend', itemHtml);
        }
    }
    
    /**
     * Inizializza il drag & drop per riordinamento
     */
    function initGallerySortable() {
        const galleryPreview = document.getElementById('mrc-gallery-preview');
        if (!galleryPreview) {
            console.log('Gallery preview not found - drag & drop not initialized');
            return;
        }
        
        
        // Implementazione drag & drop nativa
        let draggedElement = null;
        
        galleryPreview.addEventListener('dragstart', function(e) {
            const handle = e.target.closest('.mrc-gallery-item-handle');
            const galleryItem = e.target.closest('.mrc-gallery-item');
            
            // Permetti il drag sia dall'handle che dall'elemento stesso
            if (handle || galleryItem) {
                draggedElement = galleryItem;
                
                if (draggedElement) {
                    draggedElement.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/html', draggedElement.outerHTML);
                }
            }
        });
        
        galleryPreview.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            
            if (!draggedElement) return;
            
            const afterElement = getDragAfterElement(galleryPreview, e.clientY);
            
            // Rimuovi la classe drag-over da tutti gli elementi
            galleryPreview.querySelectorAll('.mrc-gallery-item').forEach(item => {
                item.classList.remove('drag-over');
            });
            
            // Aggiungi la classe drag-over all'elemento target
            if (afterElement) {
                afterElement.classList.add('drag-over');
            }
            
            if (afterElement && afterElement !== draggedElement) {
                try {
                    galleryPreview.insertBefore(draggedElement, afterElement);
                } catch (error) {
                    console.error('insertBefore error:', error);
                }
            } else if (!afterElement) {
                try {
                    galleryPreview.appendChild(draggedElement);
                } catch (error) {
                    console.error('appendChild error:', error);
                }
            }
        });
        
        galleryPreview.addEventListener('dragend', function(e) {
            if (draggedElement) {
                draggedElement.classList.remove('dragging');
                
                // Rimuovi tutte le classi drag-over
                galleryPreview.querySelectorAll('.mrc-gallery-item').forEach(item => {
                    item.classList.remove('drag-over');
                });
                
                updateGalleryOrder();
                updateGalleryData();
                draggedElement = null;
            }
        });
        
        // Rendi gli elementi draggable
        function makeItemsDraggable() {
            const items = galleryPreview.querySelectorAll('.mrc-gallery-item');
            items.forEach((item, index) => {
                if (item && item.nodeType === Node.ELEMENT_NODE) {
                    item.draggable = true;
                }
            });
        }
        
        // Osserva i cambiamenti nel DOM per rendere draggable i nuovi elementi
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    makeItemsDraggable();
                }
            });
        });
        
        observer.observe(galleryPreview, { childList: true });
        makeItemsDraggable();
        
        // Debug function (rimuovere in produzione)
        window.debugGallery = function() {
            console.log('Gallery Preview:', galleryPreview);
            console.log('Dragged Element:', draggedElement);
            const items = galleryPreview.querySelectorAll('.mrc-gallery-item');
            console.log('Gallery Items:', items);
            items.forEach((item, index) => {
                console.log(`Item ${index}:`, {
                    id: item.dataset.imageId,
                    draggable: item.draggable,
                    index: item.dataset.index
                });
            });
        };
        
        // Test function per verificare il drag & drop
        window.testDragDrop = function() {
            const items = galleryPreview.querySelectorAll('.mrc-gallery-item');
            console.log('Testing drag & drop...');
            console.log('Items found:', items.length);
            
            items.forEach((item, index) => {
                const handle = item.querySelector('.mrc-gallery-item-handle');
                console.log(`Item ${index}:`, {
                    id: item.dataset.imageId,
                    draggable: item.draggable,
                    hasHandle: !!handle,
                    handleVisible: handle ? getComputedStyle(handle).display !== 'none' : false,
                    handleClickable: handle ? getComputedStyle(handle).pointerEvents !== 'none' : false
                });
            });
            
            // Test eventi
            console.log('Testing event listeners...');
            if (items.length > 0) {
                const firstItem = items[0];
                const handle = firstItem.querySelector('.mrc-gallery-item-handle');
                if (handle) {
                    console.log('Testing handle click...');
                    
                    // Test click event
                    handle.addEventListener('click', function() {
                        console.log('Handle clicked successfully!');
                    }, { once: true });
                    
                    // Test mousedown event
                    handle.addEventListener('mousedown', function() {
                        console.log('Handle mousedown triggered!');
                    }, { once: true });
                    
                    console.log('Event listeners added. Try clicking the first handle.');
                } else {
                    console.log('No handle found for first item');
                }
            }
        };
        
        // Test function per verificare gli handle
        window.testHandles = function() {
            const items = galleryPreview.querySelectorAll('.mrc-gallery-item');
            console.log('Testing handles...');
            
            items.forEach((item, index) => {
                const handle = item.querySelector('.mrc-gallery-item-handle');
                if (handle) {
                    const computedStyle = getComputedStyle(handle);
                    console.log(`Handle ${index}:`, {
                        exists: true,
                        display: computedStyle.display,
                        visibility: computedStyle.visibility,
                        opacity: computedStyle.opacity,
                        pointerEvents: computedStyle.pointerEvents,
                        cursor: computedStyle.cursor,
                        position: computedStyle.position,
                        zIndex: computedStyle.zIndex
                    });
                } else {
                    console.log(`Handle ${index}: MISSING!`);
                }
            });
        };
    }
    
    /**
     * Determina dove inserire l'elemento trascinato
     */
    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.mrc-gallery-item:not(.dragging)')];
        
        if (draggableElements.length === 0) {
            return null;
        }
        
        // Ottimizzazione: usa requestAnimationFrame per performance migliori
        const result = draggableElements.reduce((closest, child) => {
            if (!child || !child.getBoundingClientRect) {
                return closest;
            }
            
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY });
        
        return result && result.element ? result.element : null;
    }
    
    /**
     * Aggiorna l'ordine della galleria
     */
    function updateGalleryOrder() {
        galleryData = [];
        
        const galleryPreview = document.getElementById('mrc-gallery-preview');
        if (!galleryPreview) return;
        
        const items = galleryPreview.querySelectorAll('.mrc-gallery-item');
        items.forEach((item, index) => {
            const imageId = item.dataset.imageId;
            if (imageId) {
                galleryData.push(imageId);
                item.setAttribute('data-index', index);
            }
        });
        
        updateGalleryData();
    }
    
    /**
     * Inizializza le azioni della galleria
     */
    function initGalleryActions() {
        // Rimozione immagine - usa event delegation
        document.addEventListener('click', function(e) {
            if (e.target.closest('.mrc-remove-image')) {
                const removeBtn = e.target.closest('.mrc-remove-image');
                const item = removeBtn.closest('.mrc-gallery-item');
                const imageId = item.dataset.imageId;
                
                if (confirm('Sei sicuro di voler rimuovere questa immagine?')) {
                    // Rimuovi dall'array
                    const index = galleryData.indexOf(imageId);
                    if (index > -1) {
                        galleryData.splice(index, 1);
                    }
                    
                    // Rimuovi dal DOM
                    item.remove();
                    
                    // Aggiorna indici
                    updateGalleryIndices();
                    updateGalleryData();
                    updateUploadAreaVisibility();
                }
            }
        });
    }
    
    /**
     * Aggiorna gli indici degli elementi della galleria
     */
    function updateGalleryIndices() {
        const galleryPreview = document.getElementById('mrc-gallery-preview');
        if (!galleryPreview) return;
        
        const items = galleryPreview.querySelectorAll('.mrc-gallery-item');
        items.forEach((item, index) => {
            item.setAttribute('data-index', index);
        });
    }
    
    /**
     * Carica la galleria esistente
     */
    function loadExistingGallery() {
        const galleryDataInput = document.getElementById('mrc-gallery-data');
        if (galleryDataInput && galleryDataInput.value) {
            try {
                galleryData = JSON.parse(galleryDataInput.value);
                updateUploadAreaVisibility();
            } catch (e) {
                console.error('Errore nel parsing dei dati galleria:', e);
            }
        }
    }
    
    /**
     * Aggiorna i dati della galleria nel campo nascosto
     */
    function updateGalleryData() {
        const galleryDataInput = document.getElementById('mrc-gallery-data');
        if (galleryDataInput) {
            galleryDataInput.value = JSON.stringify(galleryData);
        }
    }
    
    /**
     * Aggiorna la visibilità dell'area di upload
     */
    function updateUploadAreaVisibility() {
        const uploadArea = document.getElementById('mrc-gallery-upload-area');
        if (!uploadArea) return;
        
        const maxImages = mrcLavori.maxImages || 20;
        if (galleryData.length >= maxImages) {
            uploadArea.style.display = 'none';
        } else {
            uploadArea.style.display = 'block';
        }
    }
    
    /**
     * Mostra il progresso di upload
     */
    function showUploadProgress() {
        const uploadArea = document.getElementById('mrc-gallery-upload-area');
        if (!uploadArea) return;
        
        const progressHtml = '<div class="mrc-upload-progress"><p>Caricamento in corso...</p></div>';
        uploadArea.insertAdjacentHTML('beforeend', progressHtml);
    }
    
    /**
     * Nasconde il progresso di upload
     */
    function hideUploadProgress() {
        const progressElements = document.querySelectorAll('.mrc-upload-progress');
        progressElements.forEach(element => element.remove());
    }
    
    /**
     * Mostra un messaggio di errore
     */
    function showError(message) {
        const galleryContainer = document.getElementById('mrc-gallery-container');
        if (!galleryContainer) return;
        
        const errorHtml = `<div class="notice notice-error"><p>${message}</p></div>`;
        galleryContainer.insertAdjacentHTML('afterbegin', errorHtml);
        
        setTimeout(function() {
            const errorElements = document.querySelectorAll('.notice-error');
            errorElements.forEach(element => {
                element.style.opacity = '0';
                element.style.transition = 'opacity 0.3s ease';
                setTimeout(() => element.remove(), 300);
            });
        }, 5000);
    }
    
    /**
     * Validazione del form prima del salvataggio
     */
    document.addEventListener('DOMContentLoaded', function() {
        const postForm = document.getElementById('post');
        if (postForm) {
            postForm.addEventListener('submit', function(e) {
                // Verifica che ci sia almeno una descrizione o una galleria
                const descriptionInput = document.getElementById('mrc_lavori_description');
                const description = descriptionInput ? descriptionInput.value.trim() : '';
                
                if (!description && galleryData.length === 0) {
                    e.preventDefault();
                    alert('Inserisci almeno una descrizione o una galleria di immagini.');
                    return false;
                }
                
                return true;
            });
        }
    });
    
})();
