import { renderizarLayout } from "../components/layoutManager.js";
import { CalendarManager } from "../components/calendarManager.js";

// =====================================================
// VERIFICAR DISPONIBILIDAD DEL PAQUETE (bloqueo admin)
// =====================================================
async function verificarDisponibilidadPaquete(id_paquete) {
    const params = new URLSearchParams({
        accion: "estado",
        entidad: "paquete",
        id: id_paquete,
        id_admin: 0       // 0 = consulta publica
    });
    try {
        const res  = await fetch(`./../../data/Logic/bloqueoLogic.php?${params.toString()}`);
        const json = await res.json();
        return json;
    } catch (_) {
        return { bloqueado: false };
    }
}

// =====================================================
// BANNER DE PAQUETE NO DISPONIBLE
// =====================================================
function mostrarBannerNoDisponible(contenedor, mensaje) {
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

    await CalendarManager.init();

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
    const tit               = document.getElementById("titulo");
    const desc              = document.getElementById("desc");
    const inputFecha        = document.getElementById("fecha");
    const ol                = document.createElement("ol");

    // =====================================================
    // CALENDARIO — apertura y seleccion de fecha
    // =====================================================
    inputFecha.addEventListener("click", () => {
        modal.close();
        const fechaActual = new Date();
        window.dispatchEvent(new CustomEvent("abrirCalendario", {
            detail: { month: fechaActual.getMonth(), year: fechaActual.getFullYear(), source: "selection" }
        }));
    });

    window.addEventListener("fechaSeleccionada", (e) => {
        const { year, month, day } = e.detail;
        inputFecha.value = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

        const calModal = document.getElementById("calendar-modal");
        if (calModal) {
            calModal.classList.add("closing");
            setTimeout(() => {
                calModal.classList.remove("active", "closing");
                showAlert("Fecha seleccionada", "Se seleccionó el " + inputFecha.value)
                    .then(() => modal.show());
            }, 250);
        }
    });

    // =====================================================
    // CARGA Y RENDERIZADO DE LUGARES Y PAQUETES
    // =====================================================
    fetch("./../../data/Logic/lugarLogic.php")
        .then((response) => response.json())
        .then((respuesta) => {
            if (!respuesta.correcto || !respuesta.lugares) {
                console.error("Error: no se pudieron obtener los lugares");
                return;
            }

            respuesta.lugares.forEach((lugar) => {
                const li = document.createElement("li");
                li.textContent = lugar.nombre_lugar;
                li.setAttribute("class", "minicard");

                li.addEventListener("click", function () {
                    const id_lugar = lugar.id_lugar;

                    fetch(`./../../data/Logic/PaquetesLogic.php?id_lugar=${id_lugar}`)
                        .then((response) => response.json())
                        .then(async (respuestaPaquetes) => {
                            const paquetes = respuestaPaquetes.paquetes;
                            contenedorPaquete.innerHTML = "";

                            if (!paquetes || paquetes.length === 0) {
                                showAlert("Sin paquetes", "El lugar seleccionado aún no tiene paquetes disponibles.");
                                return;
                            }

                            tit.innerText  = lugar.nombre_lugar;
                            desc.innerText = lugar.descripcion;

                            for (const paquete of paquetes) {
                                const div = document.createElement("div");
                                div.innerHTML = `
                                    <div class="minicard" style="height: 400px">
                                        <p style="font-size: 20px">${paquete.nombre_paquete}</p>
                                        <img style="height: 150px; width: 100%; border-radius: 10px"
                                             src="./../../media/images/paquetes/${paquete.imagen_url}"
                                             alt="${paquete.descripcion_paquete}">
                                        <p>${paquete.descripcion_paquete}</p>
                                        <p style="font-style: italic">Precio: $${paquete.precio}</p>
                                    </div>
                                `;
                                contenedorPaquete.appendChild(div);

                                const imgMapa = document.getElementById("imgMapa");
                                imgMapa.src = `./../../media/images/lugares/limg${paquete.id_lugar}.jpg`;

                                // ---- Verificar disponibilidad del paquete al renderizarlo ----
                                const disponibilidad = await verificarDisponibilidadPaquete(paquete.id_paquete);

                                if (disponibilidad.bloqueado && !disponibilidad.es_tuyo) {
                                    // Paquete siendo editado: mostrar aviso y deshabilitar
                                    const hora = disponibilidad.expira_en
                                        ? new Date(disponibilidad.expira_en).toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' })
                                        : 'unos minutos';
                                    mostrarBannerNoDisponible(
                                        contenedorPaquete,
                                        `El paquete <strong>${paquete.nombre_paquete}</strong> no está disponible temporalmente. ` +
                                        `<strong>${disponibilidad.nombre_admin ?? 'Un administrador'}</strong> ` +
                                        `lo está actualizando. Intenta después de las <strong>${hora}</strong>.`
                                    );
                                    div.style.opacity       = "0.45";
                                    div.style.cursor        = "not-allowed";
                                    div.style.pointerEvents = "none";
                                    continue;  // No registrar el listener de click
                                }

                                quitarBannerNoDisponible(contenedorPaquete);
                                let con = 0;

                                div.addEventListener("click", async function () {
                                    // Re-verificar justo antes de abrir el modal de fecha
                                    const disp2 = await verificarDisponibilidadPaquete(paquete.id_paquete);
                                    if (disp2.bloqueado && !disp2.es_tuyo) {
                                        showAlert(
                                            "Paquete no disponible 🔒",
                                            `${disp2.nombre_admin ?? 'Un administrador'} está actualizando este paquete. ` +
                                            `Intenta de nuevo en unos minutos.`
                                        );
                                        return;
                                    }

                                    descripcion.innerText = paquete.descripcion_paquete;
                                    inputFecha.value = "";   // Resetear fecha al abrir paquete nuevo
                                    modal.show();

                                    btn_aceptar.onclick = async () => {
                                        if (con > 0) return;

                                        const fecha = document.getElementById("fecha").value;
                                        const hora  = document.getElementById("hora").value;

                                        if (!fecha) {
                                            showAlert("Información incompleta", "Por favor, selecciona una fecha en el calendario.");
                                            return;
                                        }

                                        con++;
                                        modal.close();

                                        const usuario = JSON.parse(usuarioSession);
                                        const formData = new FormData();
                                        formData.append("id_cliente",  usuario.id_cliente);
                                        formData.append("id_paquete",  paquete.id_paquete);
                                        formData.append("fecha_viaje", fecha);
                                        formData.append("hora_viaje",  hora || "08:00:00");
                                        // El estado lo maneja el SP (siempre 'pendiente')

                                        try {
                                            const response = await fetch("./../../data/Logic/CrearViajeLogic.php", {
                                                method: "POST",
                                                body: formData
                                            });
                                            const res = await response.json();

                                            if (res.correcto) {
                                                showAlert("✅ Viaje registrado", "El paquete ha sido registrado con éxito.");
                                            } else {
                                                // El mensaje ya viene listo: bloqueo, duplicado, fecha invalida, etc.
                                                showAlert("No se pudo registrar 🔒", res.mensaje || "Intenta de nuevo más tarde.");
                                                con = 0; // Permitir reintentar
                                            }
                                        } catch (_) {
                                            showAlert("Error de conexión", "No se pudo registrar el viaje. Intenta de nuevo.");
                                            con = 0;
                                        }
                                    };
                                });
                            }
                        });
                });

                ol.appendChild(li);
            });

            contenedorLugar.appendChild(ol);

            // Busqueda en tiempo real
            const buscarLugares = document.getElementById("buscar_lugares");
            if (buscarLugares) {
                buscarLugares.addEventListener("input", (e) => {
                    const q = e.target.value.trim().toLowerCase();
                    ol.querySelectorAll("li").forEach((li) => {
                        const t = (li.textContent || "").toLowerCase();
                        li.style.display = q === "" || t.includes(q) ? "" : "none";
                    });
                });
            }
        })
        .catch((error) => {
            console.error("Error en fetch:", error);
        });

    btn_noAceptar.addEventListener("click", () => { modal.close(); });
});

// =====================================================
// UTILIDADES DE MODALES
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
        const modalEl   = document.createElement('div');
        modalEl.className = 'app-modal active';
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
        const close = () => { modalEl.classList.remove('active'); setTimeout(() => modalEl.remove(), 250); resolve(); };
        modalEl.querySelector('.app-modal__close').addEventListener('click', close);
        modalEl.querySelector('.app-modal__ok').addEventListener('click', close);
        modalEl.querySelector('.app-modal__overlay').addEventListener('click', close);
    });
}