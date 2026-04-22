import { renderizarLayout } from "../components/layoutManager.js";

window.addEventListener("load", async function () {
    const administradorSession = sessionStorage.getItem("admin_logeado");
    if (administradorSession == null) {
        window.location.href = "./../../../index.html";
        return;
    }
    const administradorObj = JSON.parse(administradorSession);
    const idAdmin = administradorObj.id_admin;
    const id_lugar = sessionStorage.getItem("id_lugar_selected");
    const lugar_json = sessionStorage.getItem("lugar_objeto");
    if (!id_lugar || !lugar_json) {
        window.location.href = "./a_gestion_view.html";
        return;
    }
    const lugar = JSON.parse(lugar_json);

    const base = "./../../";
    await renderizarLayout({
        header: {
            basePath: base,
            titulo: "Configuración de los lugares del sitio",
            fondo: `${base}media/images/layout/imgLayout18.jpeg`,
            headerScriptPath: `${base}scripts/header_script_a_gestion_moves.js`,
            enlaces: [
                { url: "./a_gestion_view.html", texto: "Gestion de lugares", icono: `${base}media/images/icons/iconAnav1.png` },
                { url: "./a_info_events.html", texto: "Estadisticas", icono: `${base}media/images/icons/iconAnav3.png` },
                { url: "./a_gestion_respaldo.html", texto: "Respaldos", icono: `${base}media/images/icons/icon_backup.jpg` }
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

    document.getElementById("txt_nombre_lugar").value = lugar.nombre_lugar;
    document.getElementById("txt_direccion_lugar").value = lugar.direccion;
    document.getElementById("txt_zona_lugar").value = lugar.zona;
    document.getElementById("txt_ciudad_lugar").value = lugar.ciudad;
    document.getElementById("txt_descripcion_lugar").value = lugar.descripcion;
    document.querySelector(".imagen_lugar").src = lugar.imagen_url || "./../../media/images/layout/imgLayout4.jpg";
    document.querySelector(".modulo_h2").innerText = `Gestionando: ${lugar.nombre_lugar}`;
    sessionStorage.removeItem("id_lugar_selected");
    sessionStorage.removeItem("lugar_objeto");

    const fileInput = document.getElementById("file_new_img");
    const fileNameDisplay = document.getElementById("file_name_display");
    const previewImagen = document.querySelector(".imagen_lugar");
    const originalImageUrl = previewImagen.src;

    fileInput.addEventListener("change", function () {
        if (this.files && this.files.length > 0) {
            fileNameDisplay.textContent = this.files[0].name;
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImagen.src = e.target.result;
            };
            reader.readAsDataURL(this.files[0]);
        } else {
            fileNameDisplay.textContent = "Click para cambiar la imagen del lugar";
            previewImagen.src = originalImageUrl;
        }
    });

    const boton_eliminar_lugar = document.getElementById("boton_eliminar_lugar");
    const boton_modificar_lugar = document.getElementById("boton_modificar_lugar");
    const id_lugarAct = lugar.id_lugar;
    const confirm_delete = document.getElementById("confirm_delete");
    const button_proceder = document.getElementById("button_proceder");
    const button_revert = document.getElementById("button_revert");
    const error_in_delete = document.getElementById("error_in_delete");
    const delete_success = document.getElementById("delete_success");
    const button_error_revert = document.getElementById("button_error_revert");
    const button_succes_revert = document.getElementById("button_succes_revert");

    boton_eliminar_lugar.addEventListener("click", (e) => {
        e.preventDefault();
        confirm_delete.showModal();
    });
    boton_modificar_lugar.addEventListener("click", (e) => {
        e.preventDefault();
        const nombre = document.getElementById("txt_nombre_lugar").value;
        const descripcion = document.getElementById("txt_descripcion_lugar").value;
        const direccion = document.getElementById("txt_direccion_lugar").value;
        const zona = document.getElementById("txt_zona_lugar").value;
        const ciudad = document.getElementById("txt_ciudad_lugar").value;
        const imagenFile = document.getElementById("file_new_img").files[0];

        const formData = new FormData();
        formData.append("nombre", nombre);
        formData.append("descripcion", descripcion);
        formData.append("direccion", direccion);
        formData.append("zona", zona);
        formData.append("ciudad", ciudad);
        formData.append("id_admin", idAdmin);
        formData.append("id_lugar_update", id_lugarAct);
        if (imagenFile) {
            formData.append("imagen", imagenFile);
        }
        fetch("./../../data/Logic/lugarLogic.php", {
            method: "POST",
            body: formData
        })
            .then((response) => {
                if (!response.ok) {
                    return response.json().catch(() => {
                        throw new Error("Error en la respuesta de la solicitud.");
                    });
                }
                return response.json();
            })
            .then((data) => {
                if (data.correcto) {
                    delete_success.querySelector(".dialog_h3").innerText = "Lugar modificado exitosamente.";
                    delete_success.showModal();
                } else {
                    error_in_delete.querySelector(".dialog_h3").innerText =
                        data.mensaje || "Se produjo un error al modificar el lugar.";
                    error_in_delete.showModal();
                }
            })
            .catch(() => {
                error_in_delete.querySelector(".dialog_h3").innerText = "Error de conexión o fallo interno al modificar.";
                error_in_delete.showModal();
            });
    });
    button_proceder.addEventListener("click", () => {
        confirm_delete.close();
        fetch("./../../data/Logic/lugarLogic.php", {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id_lugar: id_lugarAct })
        })
            .then((response) => {
                if (!response.ok) {
                    return response.json().catch(() => {
                        throw new Error("Error en la respuesta de la solicitud.");
                    });
                }
                return response.json();
            })
            .then((data) => {
                if (data.correcto) {
                    delete_success.showModal();
                } else {
                    error_in_delete.showModal();
                }
            })
            .catch(() => {
                error_in_delete.showModal();
            });
    });
    button_revert.addEventListener("click", () => {
        confirm_delete.close();
    });
    button_error_revert.addEventListener("click", () => {
        error_in_delete.close();
    });
    button_succes_revert.addEventListener("click", () => {
        delete_success.close();
        window.location.href = "./a_gestion_view.html";
    });
});
