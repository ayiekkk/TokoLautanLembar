
// Bagian script hanya menangani tampilan visual drag & drop gambar saja, tidak mengganggu jalannya simpan data
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');
const filePreview = document.getElementById('file-preview');

dropZone.addEventListener('click', () => fileInput.click());
fileInput.addEventListener('click', (e) => e.stopPropagation());

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, e => e.preventDefault());
    document.body.addEventListener(eventName, e => e.preventDefault());
});

['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, () => dropZone.classList.add('active'));
});
['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, () => dropZone.classList.remove('active'));
});

dropZone.addEventListener('drop', (e) => {
    if (e.dataTransfer.files.length > 0) {
        const file = e.dataTransfer.files[0];
        if (file.type.startsWith('image/')) {
            const dataTransferContainer = new DataTransfer();
            dataTransferContainer.items.add(file);
            fileInput.files = dataTransferContainer.files;
            showPreview(file);
        }
    }
});

fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) showPreview(e.target.files[0]);
});

function showPreview(file) {
    dropZone.style.borderColor = "#22c55e";
    dropZone.style.backgroundColor = "#f0fdf4";
    const dropText = dropZone.querySelector('.drop-text');
    if (dropText) dropText.innerHTML = `File terpilih: <strong>${file.name}</strong>`;
}