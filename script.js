const loginBtn = document.getElementById('login-btn');
const loginCard = document.getElementById('login-card');

loginBtn.addEventListener('click', () => {
    loginCard.classList.toggle('active');
});
