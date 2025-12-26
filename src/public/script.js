function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}


// Редагування тварини — оновлено для description та photo
function openEditAnimalModal(id, name, type, price, description = '', photo = '') {
    document.getElementById('edit_animal_id').value = id;
    document.getElementById('edit_animal_name').value = escapeHtml(name);
    document.getElementById('edit_animal_type').value = escapeHtml(type);
    document.getElementById('edit_animal_price').value = price;
    document.getElementById('edit_animal_description').value = description || '';

    const currentPhotoImg = document.getElementById('edit_current_photo');
    const noPhotoText = document.getElementById('no_photo_text');

    if (photo && photo.trim() !== '') {
        currentPhotoImg.src = 'uploads/animals/' + photo;
        currentPhotoImg.style.display = 'block';
        noPhotoText.style.display = 'none';
    } else {
        currentPhotoImg.style.display = 'none';
        noPhotoText.style.display = 'block';
    }

    document.getElementById('editAnimalModal').classList.add('show');
}

function openEditAnimalModalFromButton(button) {
    const id = button.getAttribute('data-id');
    const name = button.getAttribute('data-name');
    const type = button.getAttribute('data-type');
    const price = button.getAttribute('data-price');
    const description = button.getAttribute('data-description') || '';
    const photo = button.getAttribute('data-photo') || '';
    openEditAnimalModal(id, name, type, price, description, photo);
}

function closeEditAnimalModal() {
    document.getElementById('editAnimalModal').classList.remove('show');
}

// Редагування користувача (без змін)
function openEditUserModal(id, name, email, role) {
    document.getElementById('edit_user_id').value = id;
    document.getElementById('edit_user_name').value = escapeHtml(name);
    document.getElementById('edit_user_email').value = escapeHtml(email);
    document.getElementById('edit_user_role').value = escapeHtml(role);
    document.getElementById('editUserModal').classList.add('show');
}

function openEditUserModalFromButton(button) {
    const id = button.getAttribute('data-id');
    const name = button.getAttribute('data-name');
    const email = button.getAttribute('data-email');
    const role = button.getAttribute('data-role');
    openEditUserModal(id, name, email, role);
}

function closeEditUserModal() {
    document.getElementById('editUserModal').classList.remove('show');
}

// Закриття всіх модалок при кліку поза вікном
window.onclick = function(event) {
    const detailsModal = document.getElementById('animalDetailsModal');
    const animalModal = document.getElementById('editAnimalModal');
    const userModal = document.getElementById('editUserModal');

    if (event.target === detailsModal) closeAnimalDetails();
    if (event.target === animalModal) closeEditAnimalModal();
    if (event.target === userModal) closeEditUserModal();
}

// Детальна інформація про тварину
function openAnimalDetails(id, name, type, price, description, photo) {
    document.getElementById('detail_name').textContent = name;
    document.getElementById('detail_type').textContent = type;
    // Додаємо Number()
    document.getElementById('detail_price').textContent = Number(price).toFixed(2);
    document.getElementById('detail_description').textContent = description || 'Опис відсутній';

    const photoEl = document.getElementById('detail_photo');
    if (photo && photo.trim() !== '') {
        photoEl.src = 'uploads/animals/' + photo;
        photoEl.style.display = 'block';
    } else {
        photoEl.style.display = 'none';
    }

    document.getElementById('animalDetailsModal').classList.add('show');
}

function closeAnimalDetails() {
    document.getElementById('animalDetailsModal').classList.remove('show');
}

function openAddOrderModal() {
    document.getElementById('addOrderModal').classList.add('show');
}
function closeAddOrderModal() {
    document.getElementById('addOrderModal').classList.remove('show');
}

function openStatusModal(id, current) {
    document.getElementById('status_id').value = id;
    document.getElementById('new_status').value = current;
    document.getElementById('statusModal').classList.add('show');
}
function closeStatusModal() {
    document.getElementById('statusModal').classList.remove('show');
}

function openEditOrderModal(id, order_date = '', status = '', transfer_date = '', payment_date = '', amount = '') {
    const modal = document.getElementById('editOrderModal');
    if (!modal) {
        console.error('Модалка editOrderModal не знайдена');
        return;
    }

    document.getElementById('edit_order_id').value = id;
    document.getElementById('edit_order_date').value = order_date;
    document.getElementById('edit_order_status').value = status;
    document.getElementById('edit_amount').value = amount;

    modal.classList.add('show');
}

function closeEditOrderModal() {
    document.getElementById('editOrderModal').classList.remove('show');
}

function openAnimalDetailsFromButton(button) {
    const id = button.getAttribute('data-id');
    const name = button.getAttribute('data-name');
    const type = button.getAttribute('data-type');
    const price = parseFloat(button.getAttribute('data-price')) || 0;
    const description = button.getAttribute('data-description');
    const photo = button.getAttribute('data-photo');
    
    // Викликаємо вже існуючу функцію
    openAnimalDetails(id, name, type, price, description, photo);
}
