// assets/js/login.js - Validación en el navegador del formulario de login
document.getElementById('formLogin').addEventListener('submit', function (e) {
  const correo = document.getElementById('correo');
  const clave = document.getElementById('clave');
  let valido = true;

  const correoOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo.value.trim());
  correo.classList.toggle('is-invalid', !correoOk);
  if (!correoOk) valido = false;

  const claveOk = clave.value.length > 0;
  clave.classList.toggle('is-invalid', !claveOk);
  if (!claveOk) valido = false;

  if (!valido) e.preventDefault();   // no envía el formulario si hay errores
});