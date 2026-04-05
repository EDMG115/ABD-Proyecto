import { renderizarLayout } from "../components/layoutManager.js";
import { CalendarManager } from "../components/calendarManager.js"; // IMPORTAMOS EL MANAGER

window.addEventListener("load", async function () {
    // 1. INICIALIZAR EL CALENDARIO (Inyecta el HTML en el DOM)
    await CalendarManager.init();

    const usuarioSession = sessionStorage.getItem("usuario_logeado");
    if (usuarioSession == null) {
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
                { url: "./usuario_principal.html", texto: "Pagina Principal", icono: `${base}media/images/icons/icon_home.png` },
                { url: "./usuarioviajes.html", texto: "Paquetes de viaje", icono: `${base}media/images/icons/icon_travel.png` },
                { url: "./usuarioEventos.html", texto: "Actividades", icono: `${base}media/images/icons/icon_event.png` },
                { url: "./usuario_historial.html", texto: "Historial", icono: `${base}media/images/icons/icon_event.png` },
                {
                    tipo: "boton",
                    id: "btn_is_r",
                    url: "#",
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

    const contenedorLugar = document.getElementById("selecciones");
    const contenedorPaquete = document.getElementById("paquete");
    const modal = document.getElementById("fecha_select");
    const btn_aceptar = document.getElementById("button_continuar");
    const btn_noAceptar = document.getElementById("button_noContinuar");
    const descripcion = document.getElementById("descripcion");
    const tit = document.getElementById("titulo");
    const desc = document.getElementById("desc");
    const inputFecha = document.getElementById("fecha"); // OBTENEMOS EL INPUT
    const ol = document.createElement("ol");

    // =====================================================
    // LÓGICA DEL CALENDARIO (Apertura y Selección)
    // =====================================================
    
    // Al hacer clic en el input, abrimos el calendario en modo "selection"
    inputFecha.addEventListener("click", () => {
        modal.close();

        const fechaActual = new Date();
        window.dispatchEvent(new CustomEvent("abrirCalendario", {
            detail: {
                month: fechaActual.getMonth(),
                year: fechaActual.getFullYear(),
                source: "selection" 
            }
        }));
    });

    // Interceptamos clics globales para detectar cuándo se elige un día en el calendario
    window.addEventListener("fechaSeleccionada", (e) => {
    const { year, month, day } = e.detail;

    inputFecha.value = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

    const calModal = document.getElementById("calendar-modal");

    // 1. Cerrar calendario primero
    if (calModal) {
        calModal.classList.add("closing");

        setTimeout(() => {
            calModal.classList.remove("active", "closing");

            // 2. Mostrar mensaje ENCIMA de todo
            showAlert("Fecha seleccionada", "Se seleccionó el " + inputFecha.value)
            .then(() => {
                // 3. Reabrir selector DESPUÉS del mensaje
                modal.show();
            });

        }, 250);
    }
});

    // =====================================================
    // CARGA DE DATOS Y EVENTOS
    // =====================================================

    fetch("./../../data/logic/lugarLogic.php")
        .then((response) => response.json())
        .then((respuesta) => {
            if (!respuesta.correcto || !respuesta.lugares) {
                console.log("Error: no se pudieron obtener los lugares");
                return;
            }

            const lugares = respuesta.lugares;

            lugares.forEach((lugar) => {
                const li = document.createElement("li");
                li.textContent = lugar.nombre_lugar;
                li.setAttribute("class", "minicard");

                li.addEventListener("click", function () {
                    const id_lugar = lugar.id_lugar;

                    fetch(`./../../data/logic/PaquetesLogic.php?id_lugar=${id_lugar}`)
                        .then((response) => response.json())
                        .then((respuestaPaquetes) => {
                            const paquetes = respuestaPaquetes.paquetes;
                            contenedorPaquete.innerHTML = "";

                            paquetes.forEach((paquete) => {
                                tit.innerText = lugar.nombre_lugar;
                                desc.innerText = lugar.descripcion;

                                const div = document.createElement("div");
                                div.innerHTML = `
                                <div class="minicard" style="height: 400px">
                                    <p style="font-size: 20px">${paquete.nombre_paquete}</p>
                                    <img style="height: 150px; width: 100%; border-radius: 10px" src="./../../media/images/paquetes/${paquete.imagen_url}" id="${paquete.nombre_paquete}" alt="${paquete.descripcion_paquete}">
                                    <p >${paquete.descripcion_paquete}</p>
                                    <p style="font-style: italic">Precio: $${paquete.precio}</p>
                                </div>
                        `       ;
                                contenedorPaquete.appendChild(div);

                                const imgMapa = document.getElementById("imgMapa");
                                imgMapa.src = `./../../media/images/lugares/limg${paquete.id_lugar}.jpg`;

                                let con = 0;

                                div.addEventListener("click", function () {
                                    descripcion.innerText = paquete.descripcion_paquete;
                                    inputFecha.value = ""; // Reseteamos la fecha al abrir un paquete nuevo
                                    modal.show();

                                    btn_aceptar.onclick = () => {
                                        if (con === 0) {
                                            const fecha = document.getElementById("fecha").value;
                                            const hora = document.getElementById("hora").value;
                                            const usuario = JSON.parse(usuarioSession);

                                            // Validación extra: no dejar continuar si no eligió fecha
                                            if (!fecha) {
                                                showAlert("Información incompleta", "Por favor, selecciona una fecha en el calendario.");
                                                return;
                                            }

                                            const formData = new FormData();
                                            formData.append("id_cliente", usuario.id_cliente);
                                            formData.append("id_paquete", paquete.id_paquete);
                                            formData.append("estado", "pendiente");
                                            formData.append("fecha_viaje", fecha);
                                            formData.append("hora_viaje", hora);

                                            fetch(`./../../data/logic/CrearViajeLogic.php`, {
                                                method: "POST",
                                                body: formData
                                            })
                                                .then((response) => response.json())
                                                .then((respuestaViaje) => {
                                                    showAlert("Éxito", "El paquete ha sido registrado con éxito.");                                                    
                                                });

                                            con++;
                                            modal.close();
                                        }
                                    };
                                });
                            });

                            if (contenedorPaquete.innerHTML === "") {
                                showAlert("Sin paquetes", "El lugar seleccionado, aún no tiene paquetes disponibles.");
                            }
                        });
                });

                ol.appendChild(li);
            });

            contenedorLugar.appendChild(ol);
        })
        .catch((error) => {
            console.log("Error en fetch:", error);
        });

    btn_noAceptar.addEventListener("click", () => {
        modal.close();
    });
});

// =====================================================
// UTILIDADES DE MODALES (Alerta, Confirm)
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