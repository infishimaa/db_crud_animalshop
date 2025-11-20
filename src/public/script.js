// Функції для управління модальним вікном редагування тварини
function openEditAnimalModal(id, name, type, price) {
    console.log('openEditAnimalModal:', {id, name, type, price});
    document.getElementById('edit_animal_id').value = id;
    document.getElementById('edit_animal_name').value = name;
    document.getElementById('edit_animal_type').value = type;
    document.getElementById('edit_animal_price').value = price;
    document.getElementById('editAnimalModal').classList.add('show');
}

function openEditAnimalModalFromButton(button) {
    const id = button.getAttribute('data-id');
    const name = button.getAttribute('data-name');
    const type = button.getAttribute('data-type');
    const price = button.getAttribute('data-price');
    openEditAnimalModal(id, name, type, price);
}

function closeEditAnimalModal() {
    document.getElementById('editAnimalModal').classList.remove('show');
}

function validateEditAnimal() {
    let form = document.querySelector('#editAnimalModal form');
    let formData = new FormData(form);
    formData.append('edit_animal', '1');
    
    fetch('', {
        method: 'POST',
        body: formData
    }).then(() => {
        window.location.href = 'index.php';
    });
    return false;
}

// Функції для управління модальним вікном редагування користувача
function openEditUserModal(id, name, email, role) {
    console.log('openEditUserModal:', {id, name, email, role});
    document.getElementById('edit_user_id').value = id;
    document.getElementById('edit_user_name').value = name;
    document.getElementById('edit_user_email').value = email;
    document.getElementById('edit_user_role').value = role;
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

function validateEditUser() {
    let form = document.querySelector('#editUserModal form');
    let formData = new FormData(form);
    formData.append('edit_user', '1');
    
    fetch('', {
        method: 'POST',
        body: formData
    }).then(() => {
        window.location.href = 'index.php';
    });
    return false;
}

// Закриття модалей при кліку поза вікном
window.onclick = function(event) {
    let animalModal = document.getElementById('editAnimalModal');
    let userModal = document.getElementById('editUserModal');
    
    if (event.target == animalModal) {
        animalModal.classList.remove('show');
    }
    if (event.target == userModal) {
        userModal.classList.remove('show');
    }
}
