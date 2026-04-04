import { renderizarLayout } from "../components/layoutManager.js";
import { CarouselManager } from "../components/carouselManager.js";

window.addEventListener("load", async function () {
    const base = "./../";

    await renderizarLayout({
        header: {
            basePath: base,
            titulo: "PAGINA PRINCIPAL FALSA",
            fondo: `${base}media/images/layout/img_background_header.jpg`,
            headerScriptPath: `${base}scripts/header_script.js`,
            enlaces: [
                { url: "#", texto: "Pagina Principal", icono: `${base}media/images/icons/icon_home.png` },
                { url: "#", texto: "Lugares Populares", icono: `${base}media/images/icons/icon_travel.png` },
                { url: "#", texto: "Eventos Recientes", icono: `${base}media/images/icons/icon_event.png` },
                {
                    tipo: "boton",
                    id: "btn_is_r",
                    url: "#",
                    icono: `${base}media/images/icons/icon_user.png`,
                    onClick: () => {
                        document.getElementById("select_login").showModal();
                    }
                }
            ]
        },
        footer: {
            basePath: base,
            fondo: `${base}media/images/layout/imgLayout20.jpg`
        }
    });

    const select_login = document.getElementById("select_login");
    const btn_is_r = document.getElementById("btn_is_r");
    if (btn_is_r && select_login) {
        btn_is_r.addEventListener("click", function () {
            select_login.showModal();
        });
    }

    fetch(`${base}data/Logic/IndexCarruselLogic.php`)
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
            }
        })
        .catch(() => {});
});
