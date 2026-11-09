// assets/js/producto.js - Validación en el navegador del formulario de producto
(function () {
  const form = document.getElementById('formProducto');
  if (!form) return;

  function entero(v, etiqueta) {
    if (!/^-?\d+$/.test(v.trim())) return etiqueta + ' debe ser un número entero.';
    if (Number(v) < 0) return etiqueta + ' no puede ser negativo.';
    if (Number(v) > 1000000) return etiqueta + ' no puede superar 1.000.000.';
    return '';
  }

  // Cada validador devuelve '' si está bien, o el mensaje de error
  const validadores = {
    codigo: function (v) {
      if (form.elements.codigo.readOnly) return '';
      if (v.trim() === '') return 'El código es obligatorio.';
      if (!/^[A-Za-z0-9-]{1,30}$/.test(v.trim()))
        return 'Usa solo letras, números y guiones (máximo 30).';
      return '';
    },
    nombre: function (v) {
      if (v.trim() === '') return 'El nombre es obligatorio.';
      if (v.trim().length > 120) return 'Máximo 120 caracteres.';
      return '';
    },
    id_categoria: function (v) {
      return v === '' ? 'Selecciona una categoría.' : '';
    },
    precio: function (v) {
      if (v.trim() === '' || isNaN(Number(v))) return 'El precio debe ser un número.';
      if (Number(v) < 0) return 'El precio no puede ser negativo.';
      if (Number(v) > 99999999.99) return 'El precio es demasiado alto.';
      return '';
    },
    stock_actual: function (v) { return entero(v, 'El stock actual'); },
    stock_minimo: function (v) { return entero(v, 'El stock mínimo'); },
    descripcion: function (v) {
      return v.length > 500 ? 'Máximo 500 caracteres.' : '';
    }
  };

  function validarCampo(nombre) {
    const campo = form.elements[nombre];
    const mensaje = validadores[nombre](campo.value);
    campo.classList.toggle('is-invalid', mensaje !== '');
    const feedback = campo.parentElement.querySelector('.invalid-feedback');
    if (feedback && mensaje !== '') feedback.textContent = mensaje;
    return mensaje === '';
  }

  // Valida al salir de cada campo
  Object.keys(validadores).forEach(function (nombre) {
    form.elements[nombre].addEventListener('blur', function () {
      validarCampo(nombre);
    });
  });

  // Valida todo al enviar; si hay errores, no se envía
  form.addEventListener('submit', function (e) {
    let ok = true;
    Object.keys(validadores).forEach(function (nombre) {
      if (!validarCampo(nombre)) ok = false;
    });
    if (!ok) {
      e.preventDefault();
      const primero = form.querySelector('.is-invalid');
      if (primero) primero.focus();
    }
  });
})();