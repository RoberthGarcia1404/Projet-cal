// js/login.js

document.addEventListener('DOMContentLoaded', () => {
    const loginToggle = document.getElementById('login-toggle');
    const registerToggle = document.getElementById('register-toggle');
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const regPassword = document.getElementById('reg_password');
    const regConfirmPassword = document.getElementById('reg_confirm_password');
  
    loginToggle.addEventListener('click', () => {
      loginToggle.classList.add('active');
      registerToggle.classList.remove('active');
      loginForm.classList.remove('hidden');
      registerForm.classList.add('hidden');
    });
  
    registerToggle.addEventListener('click', () => {
      registerToggle.classList.add('active');
      loginToggle.classList.remove('active');
      registerForm.classList.remove('hidden');
      loginForm.classList.add('hidden');
    });
  
    // Validar que las contraseñas coincidan en el registro
    document.getElementById('registerForm').addEventListener('submit', (e) => {
      if (regPassword.value !== regConfirmPassword.value) {
        e.preventDefault();
        alert('Las contraseñas no coinciden.');
      }
    });
  });
  