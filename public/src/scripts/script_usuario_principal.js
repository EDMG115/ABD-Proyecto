import { renderizarLayout } from "../components/layoutManager.js";
import { CarouselManager } from "../components/carouselManager.js";

window.addEventListener("load", async function () {
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

    fetch("./../../data/Logic/CarruselLogic.php")
        .then((response) => response.json())
        .then(async (data) => {
            if (data.correcto && data.lugares && data.lugares.length > 0) {
                const lugaresFormateados = data.lugares.map((lugar) => ({
                    nombre_paquete: lugar.nombre_lugar,
                    descripcion_paquete: lugar.descripcion || "Lugar destacado",
                    imagen_url: lugar.imagen_url
                }));

                await CarouselManager.mount({
                    containerSelector: "#carrusel-paquetes",
                    dataArray: lugaresFormateados,
                    type: "paquete",
                    title: "Lugares más populares",
                    mediaBase: `${base}media/images/`,
                    dataBasePath: `${base}data/`
                });
            } else {
                console.error("Error al cargar lugares populares:", data.mensaje || "No se encontraron lugares");
                document.getElementById("carrusel-paquetes").innerHTML =
                    "<p style='text-align: center; padding: 20px;'>No hay lugares populares disponibles en este momento.</p>";
            }
        })
        .catch((error) => {
            console.error("Error al cargar el carrusel:", error);
            document.getElementById("carrusel-paquetes").innerHTML =
                "<p style='text-align: center; padding: 20px;'>Error al cargar los lugares populares.</p>";
        });
});
