import { renderizarLayout } from "../components/layoutManager.js";
import { CalendarManager } from "../components/calendarManager.js";

// =====================================================
// 1. VALIDACIÓN DE SESIÓN Y REFERENCIAS
// =====================================================
let idEvento = sessionStorage.getItem("evento_seleccionado");

if (!idEvento) {
    window.location.href = "../../../index.html";
}

// Referencias DOM
const img          = document.getElementById("event-img");
const fileInput    = document.getElementById("file_event_img");
const nameInput    = document.getElementById("event-name");
const descInput    = document.getElementById("event-desc");
const timeInput    = document.getElementById("event-time");
const priceInput   = document.getElementById("event-price");

const placeEl      = document.getElementById("event-place");
const typeEl       = document.getElementById("event-type");
const organizerEl  = document.getElementById("event-organizer");
const organizerImg = document.getElementById("event-organizer-img");
const miniCalendar = document.getElementById("event-calendar");

const btnEdit   = document.getElementById("btn-edit");
const btnSave   = document.getElementById("btn-save");
const btnDelete = document.getElementById("btn-delete");

// Estado Global
let evento           = null;
let lugar            = null;
let organizador      = null;
let tipoActividad    = null;
let nuevaImagenArchivo = null;
let editMode         = false;
let bloqueoActivo    = false;   // true mientras este admin tiene el bloqueo
let currentProfile   = JSON.parse(sessionStorage.getItem("organizador_logeado"));

// id_admin del perfil logueado (organizadoras usan id_organizadora como admin en este contexto)
const idAdmin = currentProfile?.id_admin ?? currentProfile?.id_organizadora ?? null;

// =====================================================
// 2. UTILIDADES DE BLOQUEO
// =====================================================

async function bloqueoRequest(accion, extra = {}) {
    const params = new URLSearchParams({
        accion,
        entidad:  "evento",
        id:       idEvento,
        id_admin: idAdmin,
        ...extra
    });
    const res = await fetch(`../../data/Logic/bloqueoLogic.php?${params.toString()}`);
    return res.json();
}

async function adquirirBloqueo() {
    const json = await bloqueoRequest("adquirir", { minutos: 30 });
    if (json.correcto) {
        bloqueoActivo = true;
        mostrarBadgeBloqueoActivo();
        return true;
    }
    showAlert("Registro en uso 🔒", json.mensaje);
    return false;
}

async function liberarBloqueo() {
    if (!bloqueoActivo) return;
    try {
        await bloqueoRequest("liberar");
    } catch (_) { /* silencioso */ }
    bloqueoActivo = false;
    quitarBadgeBloqueoActivo();
}

function mostrarBadgeBloqueoActivo() {
    if (document.getElementById("badge_bloqueo_activo")) return;
    const badge = document.createElement("div");
    badge.id = "badge_bloqueo_activo";
    badge.style.cssText = `
        background:#ecfdf5; border:1.5px solid #34d399; border-radius:8px;
        padding:8px 16px; margin-bottom:12px;
        display:flex; align-items:center; gap:8px;
        font-size:.88rem; color:#065f46;
    `;
    badge.innerHTML = `<span>🟢</span><span>Tienes el control de edición. Guarda o cancela para liberarlo.</span>`;
    btnSave.closest("div")?.prepend(badge) ?? btnSave.parentElement.prepend(badge);
}

function quitarBadgeBloqueoActivo() {
    document.getElementById("badge_bloqueo_activo")?.remove();
}

// Liberar bloqueo cuando el usuario cierra/sale de la pagina
window.addEventListener("beforeunload", () => {
    if (!bloqueoActivo || !idAdmin) return;
    const params = new URLSearchParams({
        accion: "liberar", entidad: "evento",
        id: idEvento, id_admin: idAdmin
    });
    navigator.sendBeacon(`../../data/Logic/bloqueoLogic.php?${params.toString()}`);
});

// =====================================================
// 3. UTILIDADES DE MODALES
// =====================================================

function ensureModalContainer() {
    if (!document.getElementById('app-modals')) {
        const c = document.createElement('div');
        c.id = 'app-modals';
        document.body.appendChild(c);
    }
}

function showAlert(title = 'Aviso', message = '', okText = 'Entendido') {
    ensureModalContainer();
    return new Promise((resolve) => {
        const container = document.getElementById('app-modals');
        const modal = document.createElement('div');
        modal.className = 'app-modal active';
        modal.innerHTML = `
            <div class="app-modal__overlay"></div>
            <div class="app-modal__content" style="max-width: 450px;">
                <div class="app-modal__header">
                    <h2>${title}</h2>
                    <button class="app-modal__close">×</button>
                </div>
                <div class="app-modal__body">
                    <p style="margin:0; color:#475569; font-size:1rem; line-height:1.6;">${message}</p>
                </div>
                <div class="app-modal__actions">
                    <button class="btn-modal btn-modal--primary app-modal__ok">${okText}</button>
                </div>
            </div>
        `;
        container.appendChild(modal);
        const close = () => { modal.classList.remove('active'); setTimeout(() => modal.remove(), 250); resolve(); };
        modal.querySelector('.app-modal__close').addEventListener('click', close);
        modal.querySelector('.app-modal__ok').addEventListener('click', close);
        modal.querySelector('.app-modal__overlay').addEventListener('click', close);
    });
}

function showConfirm(title = 'Confirmar', message = '') {
    ensureModalContainer();
    return new Promise((resolve) => {
        const container = document.getElementById('app-modals');
        const modal = document.createElement('div');
        modal.className = 'app-modal active';
        modal.innerHTML = `
            <div class="app-modal__overlay"></div>
            <div class="app-modal__content" style="max-width: 480px;">
                <div class="app-modal__header">
                    <h2>${title}</h2>
                    <button class="app-modal__close">×</button>
                </div>
                <div class="app-modal__body">
                    <p style="margin:0; color:#475569; font-size:1rem; line-height:1.6;">${message}</p>
                </div>
                <div class="app-modal__actions">
                    <button class="btn-modal btn-modal--secondary app-modal__cancel">Cancelar</button>
                    <button class="btn-modal btn-modal--primary app-modal__ok">Confirmar</button>
                </div>
            </div>
        `;
        container.appendChild(modal);
        const close = (r) => { modal.classList.remove('active'); setTimeout(() => modal.remove(), 250); resolve(r); };
        modal.querySelector('.app-modal__close').addEventListener('click',  () => close(false));
        modal.querySelector('.app-modal__cancel').addEventListener('click', () => close(false));
        modal.querySelector('.app-modal__overlay').addEventListener('click',() => close(false));
        modal.querySelector('.app-modal__ok').addEventListener('click',     () => close(true));
    });
}

function mostrarModalSeleccion(titulo, lista, config, callback) {
    ensureModalContainer();
    const container = document.getElementById('app-modals');
    const modal = document.createElement('div');
    modal.className = 'app-modal active';
    modal.innerHTML = `
        <div class="app-modal__overlay"></div>
        <div class="app-modal__content">
            <div class="app-modal__header">
                <h2>${titulo}</h2>
                <button class="app-modal__close">×</button>
            </div>
            <div class="app-modal__body">
                <input type="search" class="modal-search" placeholder="Buscar...">
                <div class="selection-grid" id="modal-grid-container"></div>
            </div>
        </div>
    `;
    container.appendChild(modal);

    const grid    = modal.querySelector('#modal-grid-container');
    const search  = modal.querySelector('.modal-search');
    const closeBtn = modal.querySelector('.app-modal__close');
    const overlay  = modal.querySelector('.app-modal__overlay');

    function render(filter = '') {
        grid.innerHTML = '';
        const lowerFilter = filter.toLowerCase();
        const filtrados = lista.filter(item => {
            const nombre = (item[config.keyNombre] || '').toLowerCase();
            const desc   = (item[config.keyDesc]   || '').toLowerCase();
            return nombre.includes(lowerFilter) || desc.includes(lowerFilter);
        });
        if (filtrados.length === 0) {
            grid.innerHTML = `<div style="text-align:center;color:#94a3b8;padding:2rem;">No se encontraron resultados.</div>`;
            return;
        }
        filtrados.forEach(item => {
            const card = document.createElement('div');
            card.className = 'selection-card';
            let imgHTML = '';
            if (config.keyImg && item[config.keyImg]) {
                imgHTML = `<img src="${config.pathImg}/${item[config.keyImg]}" class="selection-card__img" alt="Img" onerror="this.style.display='none'">`;
            }
            card.innerHTML = `
                ${imgHTML}
                <div class="selection-card__info">
                    <h4 class="selection-card__title">${item[config.keyNombre]}</h4>
                    <p class="selection-card__desc">${item[config.keyDesc] || 'Sin descripción'}</p>
                </div>
            `;
            card.addEventListener('click', () => { callback(item); closeModal(); });
            grid.appendChild(card);
        });
    }

    const closeModal = () => { modal.classList.remove('active'); setTimeout(() => modal.remove(), 250); };
    search.addEventListener('input', (e) => render(e.target.value));
    closeBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', closeModal);
    render('');
    setTimeout(() => search.focus(), 100);
}

// =====================================================
// 4. CARGA INICIAL DEL EVENTO
// =====================================================
window.addEventListener("load", async function () {
    try {
        await CalendarManager.init();

        const res  = await fetch(`../../data/Logic/eventController.php?accion=evento&idEvento=${idEvento}`);
        const json = await res.json();
        if (!json.correcto) throw new Error(json.mensaje);
        evento = json.data;

        const resLugar = await fetch(`../../data/Logic/eventController.php?accion=lugar&idLugar=${evento.id_lugar}`);
        const jsonLugar = await resLugar.json();
        if (jsonLugar.correcto) lugar = jsonLugar.data;

        const resOrg = await fetch(`../../data/Logic/eventController.php?accion=organizadora&idOrganizadora=${evento.id_organizadora}`);
        const jsonOrg = await resOrg.json();
        if (jsonOrg.correcto) organizador = jsonOrg.data;

        const resTipoA = await fetch(`../../data/Logic/eventController.php?accion=actividad&idActividad=${evento.id_tipo_actividad}`);
        const jsonTipoA = await resTipoA.json();
        if (jsonTipoA.correcto) tipoActividad = jsonTipoA.data;

        cargarEventoEnUI();
        await footer_header();

    } catch (e) {
        console.error("Error:", e);
        showAlert('Error', 'No se pudieron cargar los datos del evento.');
    }
});

// =====================================================
// 5. RENDERIZADO EN UI
// =====================================================
function cargarEventoEnUI() {
    editMode = false;

    const PLACEHOLDER_SVG = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='150' viewBox='0 0 200 150'%3E%3Crect fill='%23e2e8f0' width='200' height='150'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' fill='%2394a3b8' font-size='14'%3EImagen no disponible%3C/text%3E%3C/svg%3E";

    if (evento.imagen_url) {
        img.src = `../../../src/media/images/events/${evento.imagen_url}`;
        img.onerror = function () { this.src = PLACEHOLDER_SVG; this.alt = "Imagen no disponible"; };
    } else {
        img.src = PLACEHOLDER_SVG;
    }
    img.alt = evento.nombre_evento || 'Evento';

    nameInput.value  = evento.nombre_evento || '';
    descInput.value  = evento.descripcion   || '';
    timeInput.value  = evento.hora_evento   || '';
    priceInput.value = evento.precio_boleto || '';

    actualizarTarjetaLugar();
    actualizarTarjetaTipo();

    organizerEl.textContent = organizador
        ? `Organizado por: ${organizador.nombre_agencia}`
        : "Organizador desconocido";

    const PLACEHOLDER_ORG = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80' viewBox='0 0 80 80'%3E%3Crect fill='%23e2e8f0' width='80' height='80'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' fill='%2394a3b8' font-size='10'%3EImagen no disponible%3C/text%3E%3C/svg%3E";
    if (organizador?.imagen_url) {
        const imgUrl = `../../../src/media/images/organizers/${organizador.imagen_url}`;
        const preload = new Image();
        preload.onload  = () => { organizerImg.style.backgroundImage = `url(${imgUrl})`; };
        preload.onerror = () => { organizerImg.style.backgroundImage = `url(${PLACEHOLDER_ORG})`; };
        preload.src = imgUrl;
    } else {
        organizerImg.style.backgroundImage = `url(${PLACEHOLDER_ORG})`;
    }

    actualizarEstadoUI();

    const fechaEvento = new Date(evento.fecha_evento);
    miniCalendar.addEventListener("click", () => {
        window.eventosCalendario = [evento];
        window.dispatchEvent(new CustomEvent("abrirCalendario", {
            detail: { month: fechaEvento.getMonth(), year: fechaEvento.getFullYear(), source: "event" },
        }));
        document.getElementById("calendar-modal").classList.add("active");
    });
}

function actualizarTarjetaLugar() {
    placeEl.innerHTML = '';
    if (lugar) {
        placeEl.innerHTML = `<div style="display:flex;flex-direction:column;gap:4px;">
            <span>${lugar.nombre_lugar}</span>
            <small style="font-weight:400;font-size:0.8em;opacity:0.8;">${lugar.direccion || ''}</small>
        </div>`;
    } else {
        placeEl.textContent = "Seleccionar Lugar";
    }
}

function actualizarTarjetaTipo() {
    typeEl.innerHTML = '';
    if (tipoActividad) {
        typeEl.innerHTML = `<span>${tipoActividad.nombre_tipo_actividad}</span>`;
    } else {
        typeEl.textContent = "Seleccionar Actividad";
    }
}

function actualizarEstadoUI() {
    [nameInput, descInput, timeInput, priceInput, fileInput].forEach(el => el.disabled = !editMode);

    const cursorStyle  = editMode ? 'pointer' : 'default';
    const borderStyle  = editMode ? '2px dashed #2563eb' : '';

    placeEl.style.cursor        = cursorStyle;
    placeEl.style.pointerEvents = editMode ? 'auto' : 'none';
    placeEl.style.border        = borderStyle;

    typeEl.style.cursor        = cursorStyle;
    typeEl.style.pointerEvents = editMode ? 'auto' : 'none';
    typeEl.style.border        = borderStyle;

    img.style.cursor        = cursorStyle;
    img.style.pointerEvents = editMode ? 'auto' : 'none';

    btnSave.disabled    = !editMode;
    btnEdit.textContent = editMode ? 'Cancelar edición' : '✏ Iniciar edición';

    const puedeEditar = evento.id_organizadora === currentProfile?.id_organizadora;
    btnEdit.style.display   = puedeEditar ? 'inline-block' : 'none';
    btnDelete.style.display = puedeEditar ? 'inline-block' : 'none';
}

// =====================================================
// 6. INTERACCIONES
// =====================================================

// Botón Editar — adquiere el bloqueo al activar modo edicion
btnEdit.addEventListener("click", async () => {
    if (!editMode) {
        // Intentar adquirir bloqueo antes de activar edicion
        const ok = await adquirirBloqueo();
        if (!ok) return;       // Bloqueado por otro admin
        editMode = true;
    } else {
        // Cancelar edicion: liberar bloqueo
        await liberarBloqueo();
        editMode = false;
        fileInput.value = '';
        nuevaImagenArchivo = null;
        eliminarOverlayImagen();
        eliminarTooltips();
        cargarEventoEnUI();
    }

    actualizarEstadoUI();
    if (editMode) {
        configurarInputsAyuda();
        configurarOverlayImagen();
    }
});

function configurarInputsAyuda() {
    const addTooltipWrapper = (element, text) => {
        if (!element) return;
        if (element.parentElement.classList.contains('tooltip-container')) {
            element.parentElement.setAttribute('data-tooltip', text);
            return;
        }
        const wrapper = document.createElement('div');
        wrapper.className = 'tooltip-container';
        wrapper.setAttribute('data-tooltip', text);
        element.parentNode.insertBefore(wrapper, element);
        wrapper.appendChild(element);
        element.removeAttribute("title");
        element.style.width = "100%";
    };
    nameInput.placeholder  = "Ej: Concierto de Rock en la Plaza";
    addTooltipWrapper(nameInput,  "El nombre debe ser corto y llamativo.");
    descInput.placeholder  = "Describe los detalles, artistas invitados...";
    addTooltipWrapper(descInput,  "Incluye detalles clave: artistas, reglas de acceso, etc.");
    priceInput.placeholder = "0 - 3000";
    priceInput.min = "0";
    priceInput.max = "3000";
    addTooltipWrapper(priceInput, "Costo en MXN. Escribe 0 si es entrada libre.");
}

function configurarOverlayImagen() {
    if (img.parentElement.classList.contains('img-wrapper-event')) return;
    const wrapper = document.createElement('div');
    wrapper.className = 'img-wrapper-event';
    img.parentNode.insertBefore(wrapper, img);
    wrapper.appendChild(img);
    const overlay = document.createElement('div');
    overlay.className = 'img-hover-overlay';
    overlay.innerText = "(De click para seleccionar la imagen)";
    wrapper.appendChild(overlay);
    wrapper.addEventListener('click', (e) => { if (e.target !== img) img.click(); });
}

function eliminarTooltips() {
    document.querySelectorAll('.tooltip-container').forEach(wrapper => {
        const input = wrapper.querySelector('input, textarea, select');
        if (input) { wrapper.parentNode.insertBefore(input, wrapper); input.style.width = ''; }
        wrapper.remove();
    });
}

function eliminarOverlayImagen() {
    const wrapper = document.querySelector('.img-wrapper-event');
    if (wrapper) {
        const i = wrapper.querySelector('img');
        if (i) wrapper.parentNode.insertBefore(i, wrapper);
        wrapper.remove();
    }
}

img.addEventListener('click', () => {
    if (!editMode) return;
    fileInput.accept = 'image/png, image/jpeg';
    fileInput.click();
});

fileInput.addEventListener('change', function () {
    const file = fileInput.files[0];
    if (!file) return;
    if (file.type !== 'image/png' && file.type !== 'image/jpeg') {
        showAlert('Formato', 'Por favor usa imágenes PNG o JPEG.');
        fileInput.value = '';
        return;
    }
    nuevaImagenArchivo = file;
    const reader = new FileReader();
    reader.onload = (e) => img.src = e.target.result;
    reader.readAsDataURL(file);
});

placeEl.addEventListener('click', async () => {
    if (!editMode) return;
    try {
        const res  = await fetch("../../data/Logic/eventController.php?accion=lugares");
        const json = await res.json();
        if (!json.correcto) return showAlert('Error', 'Falló la carga de lugares');
        mostrarModalSeleccion('Selecciona un Lugar', json.data, {
            keyNombre: 'nombre_lugar',
            keyDesc:   'descripcion',
            keyImg:    'imagen_url',
            pathImg:   '../../../src/media/images/lugares/'
        }, (sel) => { lugar = sel; actualizarTarjetaLugar(); });
    } catch (err) {
        showAlert('Error', 'No se pudo conectar con el servidor');
    }
});

typeEl.addEventListener('click', async () => {
    if (!editMode) return;
    try {
        const res  = await fetch("../../data/Logic/eventController.php?accion=tipos");
        const json = await res.json();
        if (!json.correcto) return showAlert('Error', 'Falló la carga de tipos');
        mostrarModalSeleccion('Selecciona Tipo de Actividad', json.data, {
            keyNombre: 'nombre_tipo_actividad',
            keyDesc:   'descripcion',
            pathImg:   '../../../src/media/images/activity_types'
        }, (sel) => { tipoActividad = sel; actualizarTarjetaTipo(); });
    } catch (err) {
        showAlert('Error', 'No se pudo conectar con el servidor');
    }
});

// =====================================================
// 7. GUARDAR Y ELIMINAR
// =====================================================

btnSave.addEventListener("click", async () => {
    try {
        if (!nameInput.value.trim()) return showAlert('Faltan datos', 'El nombre es obligatorio.');

        // Verificar que el bloqueo siga activo antes de guardar
        if (!bloqueoActivo) {
            const recheck = await adquirirBloqueo();
            if (!recheck) return;
        }

        btnSave.disabled    = true;
        btnSave.textContent = "Guardando…";

        const formData = new FormData();
        formData.append("fecha_evento",       evento.fecha_evento);
        formData.append("accion",             "actualizar");
        formData.append("idEvento",           idEvento);
        formData.append("id_admin",           idAdmin);
        formData.append("nombre_evento",      nameInput.value);
        formData.append("descripcion",        descInput.value);
        formData.append("hora_evento",        timeInput.value);
        formData.append("precio_boleto",      priceInput.value);
        formData.append("id_lugar",           lugar?.id_lugar           ?? evento.id_lugar);
        formData.append("id_tipo_actividad",  tipoActividad?.id_tipo_actividad ?? evento.id_tipo_actividad);
        formData.append("id_organizadora",    evento.id_organizadora);
        if (nuevaImagenArchivo) formData.append('imagen', nuevaImagenArchivo);

        const res  = await fetch("../../data/Logic/eventController.php", { method: "POST", body: formData });
        const json = await res.json();

        if (!json.correcto) {
            // Si el servidor rechaza (bloqueo expirado u otro error)
            showAlert('Error', json.mensaje || 'Error al guardar');
            return;
        }

        // Actualizar estado local
        if (json.data) {
            evento = json.data;
            if (json.data.lugar)          lugar         = json.data.lugar;
            if (json.data.tipoActividad)  tipoActividad = json.data.tipoActividad;
        } else {
            evento.nombre_evento = nameInput.value;
            evento.descripcion   = descInput.value;
            evento.hora_evento   = timeInput.value;
            evento.precio_boleto = priceInput.value;
            if (json.nueva_imagen_url) evento.imagen_url = json.nueva_imagen_url;
        }

        const PLACEHOLDER_SVG = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='150' viewBox='0 0 200 150'%3E%3Crect fill='%23e2e8f0' width='200' height='150'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' fill='%2394a3b8' font-size='14'%3EImagen no disponible%3C/text%3E%3C/svg%3E";
        if (evento.imagen_url) {
            img.src = `../../../src/media/images/events/${evento.imagen_url}`;
            img.onerror = function () { this.src = PLACEHOLDER_SVG; this.alt = "Imagen no disponible"; };
        } else {
            img.src = PLACEHOLDER_SVG;
        }

        nuevaImagenArchivo = null;

        // Liberar bloqueo tras guardado exitoso
        await liberarBloqueo();
        editMode = false;
        actualizarEstadoUI();
        eliminarOverlayImagen();
        eliminarTooltips();

        await showAlert('Éxito', 'Evento actualizado correctamente.');

    } catch (err) {
        console.error(err);
        showAlert('Error', 'Ocurrió un error inesperado al guardar.');
    } finally {
        btnSave.disabled    = false;
        btnSave.textContent = "Guardar";
    }
});

btnDelete.addEventListener("click", async () => {
    try {
        const confirmar = await showConfirm('Eliminar', '¿Realmente deseas eliminar este evento? No se puede deshacer.');
        if (!confirmar) return;

        // Adquirir bloqueo antes de eliminar
        const ok = bloqueoActivo ? true : await adquirirBloqueo();
        if (!ok) return;

        btnDelete.disabled    = true;
        btnDelete.textContent = "Eliminando…";

        const formData = new FormData();
        formData.append("idEvento", idEvento);
        formData.append("id_admin", idAdmin);
        formData.append("accion",   "eliminar");

        const res  = await fetch("../../data/Logic/eventController.php", { method: "POST", body: formData });
        const text = await res.text();
        let json;
        try { json = JSON.parse(text); }
        catch (e) { throw new Error("El servidor devolvió una respuesta inválida."); }

        if (!json.correcto) {
            await liberarBloqueo();
            return showAlert('Error', json.mensaje);
        }

        // sp_cancelar_evento_y_reservaciones ya libero el bloqueo en el servidor
        bloqueoActivo = false;
        quitarBadgeBloqueoActivo();

        await showAlert('Eliminado', `Evento eliminado. ${json.reservaciones_canceladas ?? 0} reservación(es) cancelada(s).`);
        sessionStorage.removeItem("evento_seleccionado");
        window.location.href = "./organizers.html";

    } catch (err) {
        console.error(err);
        await liberarBloqueo();
        showAlert('Error', 'No se pudo eliminar el evento.');
    } finally {
        btnDelete.disabled    = false;
        btnDelete.textContent = "🗑 Eliminar";
    }
});

// =====================================================
// 8. HEADER / FOOTER
// =====================================================
async function footer_header() {
    const base = "./../../../src/";
    await renderizarLayout({
        header: {
            basePath: base,
            titulo: evento.nombre_evento,
            fondo:  `${base}media/images/layout/img_background_header.jpg`,
            enlaces: [
                { url: "organizer.html", texto: "Pagina Principal", icono: `${base}media/images/icons/icon_home.png` },
                {
                    tipo: "boton", id: "btn_is_r", url: "#",
                    icono: `${base}media/images/icons/icon_user.png`,
                    onClick: () => { window.location.href = "../../../index.html"; }
                }
            ]
        },
        footer: {
            basePath: base,
            fondo: `${base}media/images/layout/imgLayout20.jpg`
        }
    });
}