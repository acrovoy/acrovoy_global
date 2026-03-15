// Получаем элементы
const mainImage = document.getElementById('mainImage');
const viewer = document.getElementById('productViewer');

// Функция для привязки клика к превью
function attachViewer(thumbsSelector, gallery) {
    document.querySelectorAll(thumbsSelector).forEach((thumb, index) => {
        thumb.addEventListener('click', () => {
            viewer.open(gallery, index);
        });
    });
}

// === 1. Продуктовая галерея ===
let currentIndex = 0;
const thumbnails = document.querySelectorAll('.thumbnail');

thumbnails.forEach((thumb, index) => {
    thumb.addEventListener('click', () => {
        mainImage.src = thumb.dataset.src; // обновляем главную картинку
        currentIndex = index;               // сохраняем текущий индекс
    });
});

if (mainImage) {
    mainImage.addEventListener('click', () => {
        viewer.open(window.productGallery, currentIndex);
    });
}

// === 2. Сертификаты ===
attachViewer('.certificate-thumb', window.certificatesGallery);

// === 3. Фотографии фабрики ===
attachViewer('.factory-thumb', window.factoryGallery);

// === 4. Легко расширять для новых разделов ===
// attachViewer('.document-thumb', window.documentsGallery);
// attachViewer('.manual-thumb', window.manualsGallery);