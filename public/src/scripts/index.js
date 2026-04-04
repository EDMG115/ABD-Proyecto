import { renderizarLayout } from "../components/layoutManager.js";
import { CarouselManager } from "../components/carouselManager.js";

window.addEventListener("load", async function () {
    const base = "./src/";

    await renderizarLayout({
        header: {
            basePath: base,
            titulo: "PAGINA PRINCIPAL",
            fondo: `${base}media/images/layout/img_background_header.jpg`,
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
    document.getElementById("close_select_login").addEventListener("click", function () {
        select_login.close();
    });

    const btnUsuario = document.getElementById("btn_usuario");
    const btnAdmin = document.getElementById("btn_admin");
    const btnOrganizador = document.getElementById("btn_organizador");
    const btnAgencia = document.getElementById("btn_agencia");

    btnUsuario.addEventListener("click", function () {
        sessionStorage.setItem("tipo_usuario", "1");
        window.location.href = "./src/html/ingreso.html";
    });

    btnAdmin.addEventListener("click", function () {
        sessionStorage.setItem("tipo_usuario", "2");
        window.location.href = "./src/html/ingreso.html";
    });

    btnOrganizador.addEventListener("click", function () {
        sessionStorage.setItem("tipo_usuario", "3");
        window.location.href = "./src/html/ingreso.html";
    });

    btnAgencia.addEventListener("click", function () {
        sessionStorage.setItem("tipo_usuario", "4");
        window.location.href = "./src/html/ingreso.html";
    });

    fetch("./src/data/Logic/IndexCarruselLogic.php")
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
