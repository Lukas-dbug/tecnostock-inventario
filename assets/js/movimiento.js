// assets/js/movimiento.js - Validación en el navegador del formulario de movimientos
(function () {
  const form = document.getElementById('formMovimiento');
  if (!form) return;

  const selProducto = form.elements.id_producto;
  const infoStock = document.getElementById('infoStock');

  function stockSeleccionado() {
    const op = selProducto.options[selProducto.selectedIndex];
    return op && op.value !== '' ? Number(op.dataset.stock) : null;
  }

  function mostrarStock() {
    const s = stockSeleccionado();
    infoStock.textContent = s === null ? '' : 'Stock actual: ' + s + ' unidad(es).';
  }

  const validadores = {
    id_producto: function (v) {
      return v === '' ? 'Selecciona un producto.' : '';
    },
    tipo: function (v) {
      return v !== 'ENTRADA' && v !== 'SALIDA' ? 'Selecciona el tipo de movimiento.' : '';
    },
    cantidad: function (v) {
      if (!/^-?\d+$/.test(v.trim())) return 'La cantidad debe ser un número entero.';
      const n = Number(v);
      if (n <= 0) return 'La cantidad debe ser mayor a cero.';
      if (n > 1000000) return 'La cantidad no puede superar 1.000.000.';
      const stock = stockSeleccionado();
      if (form.elements.tipo.value === 'SALIDA' && stock !== null && n > stock)
        return 'La salida supera el stock disponible (' + stock + ').';
      return '';
    },
    observacion: function (v) {
      return v.length > 255 ? 'Máximo 255 caracteres.' : '';
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

  Object.keys(validadores).forEach(function (nombre) {
    form.elements[nombre].addEventListener('blur', function () {
      validarCampo(nombre);
    });
  });

  // Al cambiar producto o tipo, se actualiza el aviso y se revisa la cantidad
  [selProducto, form.elements.tipo].forEach(function (el) {
    el.addEventListener('change', function () {
      mostrarStock();
      if (form.elements.cantidad.value !== '') validarCampo('cantidad');
    });
  });

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

  mostrarStock();
})();