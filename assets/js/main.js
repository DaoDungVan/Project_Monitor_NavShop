function confirmDelete() {
    return confirm('Are you sure you want to delete this product?');
}

document.addEventListener('click', function (event) {
    const button = event.target.closest('[data-toggle-password]');
    if (!button) {
        return;
    }

    const input = document.getElementById(button.dataset.togglePassword);
    if (!input) {
        return;
    }

    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    button.textContent = isHidden ? 'Hide' : 'Show';
    button.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
});
