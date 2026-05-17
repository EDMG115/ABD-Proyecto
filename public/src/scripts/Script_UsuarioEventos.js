import { renderizarLayout } from "../components/layoutManager.js";

// =====================================================
// UTILIDADES DE FORMATO
// =====================================================
function toDateInputValue(fecha) {
    if (!fecha) return "";
    const raw = String(fecha).trim();
    const d = raw.split(/[\sT]/)[0];
    return d.length >= 10 ? d.slice(0, 10) : "";
}

function formatHoraLegible(horaVal) {
    if (!horaVal) return "—";
    const s = String(horaVal).trim();
    if (s.length >= 5 && /^\d{1,2}:\d{2}/.test(s)) return s.slice(0, 5);
    return s;
}

function formatFechaLegible(fecha) {
    const raw = toDateInputValue(fecha);
    if (!raw) return "—";
    const [y, m, d] = raw.split("-").map(Number);
    const dt = new Date(y, m - 1, d);
    if (Number.isNaN(dt.getTime())) return raw;
    return dt.toLocaleDateString("es-MX", {
        weekday: "long", year: "numeric", month: "long", day: "numeric"
    });
}

// =====================================================
// VERIFICAR DISPONIBILIDAD DEL EVENTO (bloqueo admin)
// =====================================================
async function verificarDisponibilidadEvento(id_evento) {
    const params = new URLSearchParams({
        accion: "estado",
        entidad: "evento",
        id: id_evento,
        id_admin: 0          // 0 = consulta publica, no interesa quien tiene el bloqueo
    });
    try {
        const res  = await fetch(`./../../data/Logic/bloqueoLogic.php?${params.toString()}`);
        const json = await res.json();
        // bloqueado:true significa que hay un admin editando ahora mismo
        return json;
    } catch (_) {
        // Si falla la consulta de disponibilidad dejamos pasar (fail-open)
        return { bloqueado: false };
    }
}

// =====================================================
// BANNER DE EVENTO NO DISPONIBLE
// =====================================================
function mostrarBannerNoDisponible(contenedor, mensaje) {
    // Eliminar banner anterior si existe
    contenedor.querySelector(".banner-no-disponible")?.remove();

    const banner = document.createElement("div");
    banner.className = "banner-no-disponible";
    banner.style.cssText = `
        background:#fef2f2; border:1.5px solid #f87171; border-radius:10px;
        padding:14px 18px; margin-bottom:12px;
        display:flex; align-items:center; gap:10px; font-size:.9rem; color:#991b1b;
    `;
    banner.innerHTML = `<span style="font-size:1.4rem;">🔒</span><span>${mensaje}</span>`;
    contenedor.prepend(banner);
}

function quitarBannerNoDisponible(contenedor) {
    contenedor.querySelector(".banner-no-disponible")?.remove();
}

// =====================================================
// MAIN
// =====================================================
window.addEventListener("load", async function () {

    const usuarioSession = sessionStorage.getItem("usuario_logeado");
    if (!usuarioSession) {
        window.location.href = "./../../../index.html";
        return;
    }

    const base = "./../../";
    await renderizarLayout({
        header: {
            basePath: base,
            titulo: "PAGINA PRINC",
            fondo: `${base}media/images/layout/img_background_header.jpg`,
            headerScriptPath: `${base}scripts/header_script.js`,
            enlaces: [
                { url: "./usuario_principal.html", texto: "Pagina Principal",  icono: `${base}media/images/icons/icon_home.png` },
                { url: "./usuarioviajes.html",     texto: "Paquetes de viaje", icono: `${base}media/images/icons/icon_travel.png` },
                { url: "./usuarioEventos.html",    texto: "Actividades",       icono: `${base}media/images/icons/icon_event.png` },
                { url: "./usuario_historial.html", texto: "Historial",         icono: `${base}media/images/icons/icon_event.png` },
                {
                    tipo: "boton", id: "btn_is_r", url: "#",
                    icono: `${base}media/images/icons/icon_user.png`,
                    onClick: () => {}
                }
            ]
        },
        footer: {
            basePath: base,
            fondo: `${base}media/images/layout/imgLayout20.jpg`
        }
    });

    const contenedorLugar   = document.getElementById("selecciones");
    const contenedorPaquete = document.getElementById("paquete");
    const modal             = document.getElementById("fecha_select");
    const btn_aceptar       = document.getElementById("button_continuar");
    const btn_noAceptar     = document.getElementById("button_noContinuar");
    const descripcion       = document.getElementById("descripcion");
    const infoFecha         = document.getElementById("info_fecha");
    const infoHora          = document.getElementById("info_hora");
    const tit               = document.getElementById("titulo");
    const desc              = document.getElementById("desc");
    const ol                = document.createElement("ol");

    // =====================================================
    // CARGA Y RENDERIZADO DE EVENTOS
    // =====================================================
    fetch("./../../data/Logic/eventoLogic.php")
        .then((response) => response.json())
        .then((data) => {
            if (!data.correcto || !data.eventos) {
                console.error("Error: no se pudieron obtener los eventos");
                return;
            }

            data.eventos.forEach((ev) => {
                const li = document.createElement("li");
                li.textContent = ev.nombre_evento;
                li.setAttribute("class", "minicard");

                li.addEventListener("click", async function () {
                    tit.innerText  = ev.nombre_evento;
                    desc.innerText = ev.descripcion || "Sin descripción.";
                    contenedorPaquete.innerHTML = "";

                    const div = document.createElement("div");
                    div.innerHTML = `
                        <div class="minicard" style="height: 400px">
                            <p style="font-size: 20px">Reservar entrada</p>
                            <img style="height: 150px; width: 100%; border-radius: 10px"
                                 src="./../../media/images/events/eimg${ev.id_evento}.png"
                                 alt="${ev.nombre_evento}">
                            <p style="font-style: italic">Precio: $${ev.precio_boleto}</p>
                        </div>
                    `;
                    contenedorPaquete.appendChild(div);

                    const imgMapa = document.getElementById("imgMapa");
                    imgMapa.src = `./../../media/images/events/eimg${ev.id_evento}.png`;
                    imgMapa.alt  = ev.nombre_evento;

                    // ---- Verificar disponibilidad ANTES de mostrar tarjeta ----
                    const disponibilidad = await verificarDisponibilidadEvento(ev.id_evento);
                    if (disponibilidad.bloqueado && !disponibilidad.es_tuyo) {
                        // El evento esta siendo editado por un admin: mostrar aviso
                        mostrarBannerNoDisponible(
                            contenedorPaquete,
                            `Este evento no está disponible temporalmente. ` +
                            `<strong>${disponibilidad.nombre_admin ?? 'Un administrador'}</strong> ` +
                            `lo está actualizando. Intenta de nuevo después de las ` +
                            `<strong>${new Date(disponibilidad.expira_en).toLocaleTimeString('es-MX', {hour:'2-digit', minute:'2-digit'})}</strong>.`
                        );
                        // Deshabilitar la tarjeta visualmente
                        div.style.opacity  = "0.45";
                        div.style.cursor   = "not-allowed";
                        div.style.pointerEvents = "none";
                        return;   // No registrar click de reservacion
                    }

                    // Evento disponible: quitar banner previo si habia
                    quitarBannerNoDisponible(contenedorPaquete);
                    let con = 0;

                    div.addEventListener("click", async function () {
                        // Re-verificar disponibilidad justo antes de abrir el modal
                        const disp2 = await verificarDisponibilidadEvento(ev.id_evento);
                        if (disp2.bloqueado && !disp2.es_tuyo) {
                            showAlert(
                                "Evento no disponible 🔒",
                                `${disp2.nombre_admin ?? 'Un administrador'} está actualizando este evento. ` +
                                `Intenta de nuevo en unos minutos.`
                            );
                            return;
                        }

                        descripcion.innerText  = ev.descripcion || "Confirma los datos para completar tu reservación.";
                        if (infoFecha) infoFecha.textContent = formatFechaLegible(ev.fecha_evento);
                        if (infoHora)  infoHora.textContent  = formatHoraLegible(ev.hora_evento);
                        modal.show();

                        btn_aceptar.onclick = async () => {
                            if (con > 0) return;
                            con++;
                            modal.close();

                            const usuario = JSON.parse(usuarioSession);
                            const formData = new FormData();
                            formData.append("id_evento",  ev.id_evento);
                            formData.append("id_cliente", usuario.id_cliente);
                            // El estado lo maneja el SP (siempre 'pendiente' en creacion)

                            try {
                                const response = await fetch("./../../data/Logic/CrearReservacionLogic.php", {
                                    method: "POST",
                                    body: formData
                                });
                                const res = await response.json();

                                if (res.correcto) {
                                    showAlert("✅ Reservación exitosa", "Tu reservación se registró correctamente.");
                                } else {
                                    // El mensaje ya viene listo del backend (bloqueo, duplicado, etc.)
                                    showAlert("No se pudo reservar 🔒", res.mensaje || "Intenta de nuevo más tarde.");
                                    con = 0; // Permitir reintentar
                                }
                            } catch (_) {
                                showAlert("Error de conexión", "No se pudo completar la reservación. Intenta de nuevo.");
                                con = 0;
                            }
                        };
                    });
                });

                ol.appendChild(li);
            });

            contenedorLugar.appendChild(ol);

            // Busqueda en tiempo real
            const buscarEventos = document.getElementById("buscar_eventos");
            if (buscarEventos) {
                buscarEventos.addEventListener("input", (e) => {
                    const q = e.target.value.trim().toLowerCase();
                    ol.querySelectorAll("li").forEach((li) => {
                        const t = (li.textContent || "").toLowerCase();
                        li.style.display = q === "" || t.includes(q) ? "" : "none";
                    });
                });
            }
        })
        .catch(() => {
            showAlert("Error", "No se pudieron obtener los eventos. Prueba a recargar la página.");
        });

    btn_noAceptar.addEventListener("click", () => { modal.close(); });
});

// =====================================================
// UTILIDADES DE MODALES
// =====================================================
function ensureModalContainer() {
    if (!document.getElementById("app-modals")) {
        const c = document.createElement("div");
        c.id = "app-modals";
        document.body.appendChild(c);
    }
}

function showAlert(title = "Aviso", message = "", okText = "Entendido") {
    ensureModalContainer();
    return new Promise((resolve) => {
        const container = document.getElementById("app-modals");
        const modalEl   = document.createElement("div");
        modalEl.className = "app-modal active";
        modalEl.innerHTML = `
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
        container.appendChild(modalEl);
        const close = () => { modalEl.classList.remove("active"); setTimeout(() => modalEl.remove(), 250); resolve(); };
        modalEl.querySelector(".app-modal__close").addEventListener("click", close);
        modalEl.querySelector(".app-modal__ok").addEventListener("click", close);
        modalEl.querySelector(".app-modal__overlay").addEventListener("click", close);
    });
}