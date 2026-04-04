import { renderizarLayout } from "../components/layoutManager.js";

window.addEventListener("load", async function () {
    const administradorSession = sessionStorage.getItem("admin_logeado");
    if (administradorSession == null) {
        window.location.href = "./../../../index.html";
        return;
    }
    const administradorObj = JSON.parse(administradorSession);
    const idAdmin = administradorObj.id_admin;
    const errorDialog = document.getElementById("errorDialog");
    const errorTitulo = document.getElementById("errorTitulo");
    const errorMensaje = document.getElementById("errorMensaje");
    const closeErrorButton = document.getElementById("close_errorDialog");

    function mostrarError(titulo, mensaje) {
        errorTitulo.textContent = titulo;
        errorMensaje.textContent = mensaje;
        errorDialog.showModal();
    }

    if (closeErrorButton) {
        closeErrorButton.addEventListener("click", function () {
            errorDialog.close();
        });
    }

    const base = "./../../";
    await renderizarLayout({
        header: {
            basePath: base,
            titulo: "Configuración de los lugares del sitio",
            fondo: `${base}media/images/layout/imgLayout5.jpg`,
            headerScriptPath: `${base}scripts/header_script_a_crear_lugar.js`,
            enlaces: [
                { url: "./a_gestion_view.html", texto: "Gestion de lugares", icono: `${base}media/images/icons/iconAnav1.png` },
                { url: "./a_info_events.html", texto: "Estadisticas", icono: `${base}media/images/icons/iconAnav3.png` }
            ],
            onHeaderReady: ({ s_header }) => {
                if (s_header) s_header.style.backgroundPosition = "50% 50%";
            }
        },
        footer: {
            basePath: base,
            fondo: `${base}media/images/layout/imgLayout19.jpg`,
            onFooterReady: ({ f_general }) => {
                if (f_general) f_general.style.backgroundPosition = "50% 80%";
            }
        }
    });

    const form_crear_lugar = document.getElementById("form_crear_lugar");
    const file_new_img = document.getElementById("file_new_img");
    const file_name_display = document.getElementById("file_name_display");
    const preview_imagen = document.getElementById("preview_imagen");

    file_new_img.addEventListener("change", function () {
        if (this.files && this.files.length > 0) {
            file_name_display.textContent = this.files[0].name;
            const reader = new FileReader();
            reader.onload = function (e) {
                preview_imagen.src = e.target.result;
            };
            reader.readAsDataURL(this.files[0]);
        } else {
            file_name_display.textContent = "Selecciona la imagen para el nuevo lugar";
            preview_imagen.src = "./../../media/images/lugares/default.jpg";
        }
    });
    form_crear_lugar.addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(form_crear_lugar);
        formData.append("id_admin", idAdmin);
        fetch("./../../data/logic/lugarLogic.php", {
            method: "POST",
            body: formData
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Error en la respuesta de la peticion.");
                }
                return response.json();
            })
            .then((data) => {
                if (data.correcto === true) {
                    window.location.href = "./a_gestion_view.html";
                } else {
                    mostrarError("Error al Crear Lugar", data.mensaje || "Error desconocido del servidor.");
                }
            })
            .catch((error) => {
                mostrarError("Error de Conexión", `No se pudo conectar al servidor: ${error.message}`);
            });
    });
});
