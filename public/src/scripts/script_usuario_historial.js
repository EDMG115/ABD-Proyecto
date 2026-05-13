import { renderizarLayout } from "../components/layoutManager.js";

window.addEventListener("load", async function () {
    const usuarioSession = sessionStorage.getItem("usuario_logeado");
    if (usuarioSession == null) {
        window.location.href = "./../../../index.html";
        return;
    }
    const usuario = JSON.parse(usuarioSession);

    const base = "./../../";
    await renderizarLayout({
        header: {
            basePath: base,
            titulo: "HISTORIAL USUARIO",
            fondo: `${base}media/images/layout/background-img-historial.jpeg`,
            headerScriptPath: `${base}scripts/header_script.js`,
            enlaces: [
                { url: "./usuario_principal.html", texto: "Pagina Principal", icono: `${base}media/images/icons/icon_home.png` },
                { url: "./usuarioviajes.html", texto: "Paquetes de viaje", icono: `${base}media/images/icons/icon_home.png` },
                { url: "./usuarioEventos.html", texto: "Actividades", icono: `${base}media/images/icons/icon_home.png` },
                { url: "./usuario_historial.html", texto: "Historial", icono: `${base}media/images/icons/icon_home.png` },
                {
                    tipo: "boton",
                    id: "btn_is_r",
                    url: "#",
                    icono: `${base}media/images/icons/icon_user.png`,
                    onClick: () => {}
                }
            ],
            onHeaderReady: ({ s_header }) => {
                if (s_header) s_header.style.backgroundPosition = "50% 50%";
            }
        },
        footer: {
            basePath: base,
            fondo: `${base}media/images/layout/imgLayout20.jpg`
        }
    });

    // Codigo ---------

    fetch("../../data/Logic/HistorialLogic.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id_cliente: usuario.id_cliente })
    })
        .then((r) => r.json())
        .then((data) => {
            if (!data.correcto) {
                console.error("Error:", data.mensaje);
                return;
            }

            const scriptTabla = document.createElement("script");
            scriptTabla.src = "./../../scripts/historial_table.js";

            scriptTabla.onload = () => {
                if (typeof window.initHistorialTable === "function") {
                    window.initHistorialTable(data.viajes);
                } else {
                    console.error("No se encontró initHistorialTable");
                }
            };

            document.body.appendChild(scriptTabla);
        })
        .catch((err) => {
            console.error("Error cargando historial:", err);
        });

    fetch("../../data/Logic/ReservacionLogic.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id_cliente: usuario.id_cliente })
    })
        .then((r) => r.json())
        .then((data) => {
            console.log("Respuesta de reservaciones:", data);
            if (!data.correcto) {
                console.error("Error:", data.mensaje);
                return;
            }

            if (!data.reservaciones || !Array.isArray(data.reservaciones)) {
                console.warn("No se recibieron reservaciones o el formato es incorrecto");
                return;
            }

            const scriptReservaciones = document.createElement("script");
            scriptReservaciones.src = "./../../scripts/reservations_table.js";

            scriptReservaciones.onload = () => {
                setTimeout(() => {
                    if (typeof window.initReservacionesTable === "function") {
                        window.initReservacionesTable(data.reservaciones);
                    } else {
                        console.error("No se encontró initReservacionesTable en window");
                    }
                }, 100);
            };

            scriptReservaciones.onerror = () => {
                console.error("Error al cargar el script reservations_table.js");
            };

            document.body.appendChild(scriptReservaciones);
        })
        .catch((err) => {
            console.error("Error cargando historial de reservaciones:", err);
        });
});
