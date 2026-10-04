/**
 * ═══════════════════════════════════════════════════════════════
 * HUMAS UPLOAD IMAGE SLIDER CONTROLLER
 * Drag-and-drop dropzone, file validation & real-time toast feedback
 * ═══════════════════════════════════════════════════════════════
 */

document.addEventListener('DOMContentLoaded', () => {
    const humasDropzone = document.getElementById('humasDropzone');
    const humasFileInput = document.getElementById('humasFileInput');
    const btnBrowseFile = document.getElementById('btnBrowseFile');
    const humasToast = document.getElementById('humasToast');
    const humasToastMsg = document.getElementById('humasToastMsg');
    let toastTimeout = null;

    function showToast(message) {
        if (humasToastMsg) humasToastMsg.textContent = message;
        if (humasToast) {
            humasToast.classList.add('show');
            clearTimeout(toastTimeout);
            toastTimeout = setTimeout(() => {
                humasToast.classList.remove('show');
            }, 4000);
        }
    }

    function handleNewUploadedFiles(files) {
        if (!files || files.length === 0) return;

        let processedCount = 0;
        Array.from(files).forEach((file) => {
            if (!file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                const newSrc = e.target.result;
                const newAlt = file.name.replace(/\.[^/.]+$/, "") || 'Foto Penghargaan Kerjasama';

                const track = document.getElementById('stepTrack');
                const sliderTotalNum = document.getElementById('sliderTotalNum');

                if (track) {
                    // 1. Buat elemen card baru
                    const newCard = document.createElement('div');
                    newCard.className = 'gallery-card-frame';
                    newCard.innerHTML = `<img src="${newSrc}" alt="${newAlt}" class="gallery-card-img" loading="lazy">`;

                    // 2. Masukkan ke slider track
                    track.appendChild(newCard);

                    // 3. Masukkan ke itemsData lightbox jika ada
                    if (window.ShowcaseSlider && window.ShowcaseSlider.itemsData) {
                        window.ShowcaseSlider.itemsData.push({ src: newSrc, alt: newAlt });
                        if (sliderTotalNum) {
                            sliderTotalNum.textContent = String(window.ShowcaseSlider.itemsData.length).padStart(2, '0');
                        }
                    }

                    if (window.ShowcaseSlider && window.ShowcaseSlider.calculateCardWidth) {
                        window.ShowcaseSlider.calculateCardWidth();
                    }
                }

                processedCount++;
                showToast(`${processedCount} foto penghargaan kerjasama berhasil diunggah!`);
            };
            reader.readAsDataURL(file);
        });
    }

    if (btnBrowseFile && humasFileInput) {
        btnBrowseFile.addEventListener('click', (e) => {
            e.stopPropagation();
            humasFileInput.click();
        });
    }

    if (humasDropzone && humasFileInput) {
        humasDropzone.addEventListener('click', () => {
            humasFileInput.click();
        });

        humasFileInput.addEventListener('change', (e) => {
            handleNewUploadedFiles(e.target.files);
            humasFileInput.value = '';
        });

        // Drag & Drop event listeners
        ['dragenter', 'dragover'].forEach(eventName => {
            humasDropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                humasDropzone.classList.add('drag-over');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            humasDropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                humasDropzone.classList.remove('drag-over');
            });
        });

        humasDropzone.addEventListener('drop', (e) => {
            if (e.dataTransfer && e.dataTransfer.files) {
                handleNewUploadedFiles(e.dataTransfer.files);
            }
        });
    }

    window.HumasUpload = {
        showToast,
        handleNewUploadedFiles
    };
});
