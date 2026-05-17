import { renderizarLayout } from "../components/layoutManager.js";

// ============================================================
// Utilidad: llamada al endpoint de bloqueos
// ============================================================
async function bloqueoRequest(accion, entidad, id, idAdmin, extra = {}) {
    const params = new URLSearchParams({ accion, entidad, id, id_admin: idAdmin, ...extra });
    const res = await fetch(`./../../data/Logic/bloqueoLogic.php?${params.toString()}`);
    return res.json();
}

// ============================================================
// Adquirir bloqueo con retroalimentacion visual
// ============================================================
async function adquirirBloqueo(entidad, id, idAdmin, botonEditar) {
    const textoOriginal = botonEditar.textContent;
    botonEditar.disabled = true;
    botonEditar.textContent = "Verificando…";

    try {
        const json = await bloqueoRequest("adquirir", entidad, id, idAdmin, { minutos: 30 });

        if (json.correcto) {
            botonEditar.disabled = false;
            botonEditar.textContent = textoOriginal;
            return true;
        }

        mostrarBannerBloqueo(json.mensaje);
        botonEditar.textContent = textoOriginal;
        return false;
    } catch (error) {
        console.error("Error al adquirir bloqueo:", error);
        botonEditar.disabled = false;
        botonEditar.textContent = textoOriginal;
        return false;
    }
}

// ============================================================
// Liberar bloqueo
// ============================================================
async function liberarBloqueo(entidad, id, idAdmin) {
    try {
        await bloqueoRequest("liberar", entidad, id, idAdmin);
    } catch (_) {
        // Silencioso: el Event Scheduler lo limpiara al expirar
    }
}

// ============================================================
// Banner de aviso de bloqueo (no usa alert nativo)
// ============================================================
function mostrarBannerBloqueo(mensaje) {
    const viejo = document.getElementById("banner_bloqueo");
    if (viejo) viejo.remove();

    const banner = document.createElement("div");
    banner.id = "banner_bloqueo";
    banner.style.cssText = `
        position: fixed; top: 80px; left: 50%; transform: translateX(-50%);
        background: #fef2f2; border: 1.5px solid #f87171; border-radius: 10px;
        padding: 14px 24px; z-index: 9999; max-width: 480px; width: 90%;
        box-shadow: 0 4px 20px rgba(0,0,0,.15);
        display: flex; align-items: center; gap: 12px;
    `;
    banner.innerHTML = `
        <span style="font-size:1.5rem;">🔒</span>
        <div style="flex:1">
            <strong style="color:#dc2626; display:block; margin-bottom:4px;">Registro en uso</strong>
            <span style="color:#374151; font-size:.9rem;">${mensaje}</span>
        </div>
        <button id="cerrar_banner_bloqueo"
            style="background:none; border:none; font-size:1.2rem; cursor:pointer; color:#6b7280;">✕</button>
    `;
    document.body.appendChild(banner);

    document.getElementById("cerrar_banner_bloqueo")
        .addEventListener("click", () => banner.remove());

    setTimeout(() => { if (banner.isConnected) banner.remove(); }, 8000);
}

// ============================================================
// Indicador de bloqueo activo
// ============================================================
function mostrarIndicadorBloqueoActivo(contenedor) {
    const viejo = document.getElementById("indicador_bloqueo");
    if (viejo) return;

    const badge = document.createElement("div");
    badge.id = "indicador_bloqueo";
    badge.style.cssText = `
        background: #ecfdf5; border: 1.5px solid #34d399; border-radius: 8px;
        padding: 8px 16px; margin-bottom: 12px;
        display: flex; align-items: center; gap: 8px; font-size: .88rem;
        color: #065f46;
    `;
    badge.innerHTML = `<span>🟢</span> <span>Tienes el control de edición. Recuerda guardar o cancelar para liberarlo.</span>`;
    contenedor.insertBefore(badge, contenedor.firstChild);
}

function quitarIndicadorBloqueoActivo() {
    const badge = document.getElementById("indicador_bloqueo");
    if (badge) badge.remove();
}

// ============================================================
// MAIN
// ============================================================
window.addEventListener("load", async function () {

    // ---- Sesion ----
    const administradorSession = sessionStorage.getItem("admin_logeado");
    if (!administradorSession) {
        window.location.href = "./../../../index.html";
        return;
    }
    const administradorObj = JSON.parse(administradorSession);
    const idAdmin          = administradorObj.id_admin;

    // ---- Datos del lugar ----
    const id_lugar   = sessionStorage.getItem("id_lugar_selected");
    const lugar_json = sessionStorage.getItem("lugar_objeto");
    if (!id_lugar || !lugar_json) {
        window.location.href = "./a_gestion_view.html";
        return;
    }
    const lugar = JSON.parse(lugar_json);

    // ---- Layout ----
    const base = "./../../";
    await renderizarLayout({
        header: {
            basePath: base,
            titulo: "Configuración de los lugares del sitio",
            fondo: `${base}media/images/layout/imgLayout18.jpeg`,
            headerScriptPath: `${base}scripts/header_script_a_gestion_moves.js`,
            enlaces: [
                { url: "./a_gestion_view.html",    texto: "Gestion de lugares", icono: `${base}media/images/icons/iconAnav1.png` },
                { url: "./a_info_events.html",     texto: "Estadisticas",       icono: `${base}media/images/icons/iconAnav3.png` },
                { url: "./a_gestion_respaldo.html",texto: "Respaldos",          icono: `${base}media/images/icons/icon_backup.jpg` }
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

    // ---- Poblar formulario ----
    document.getElementById("txt_nombre_lugar").value      = lugar.nombre_lugar;
    document.getElementById("txt_direccion_lugar").value   = lugar.direccion;
    document.getElementById("txt_zona_lugar").value        = lugar.zona;
    document.getElementById("txt_ciudad_lugar").value      = lugar.ciudad;
    document.getElementById("txt_descripcion_lugar").value = lugar.descripcion;
    document.querySelector(".imagen_lugar").src            = lugar.imagen_url || "./../../media/images/layout/imgLayout4.jpg";
    document.querySelector(".modulo_h2").innerText         = `Gestionando: ${lugar.nombre_lugar}`;

    sessionStorage.removeItem("id_lugar_selected");
    sessionStorage.removeItem("lugar_objeto");

    // ---- Preview imagen ----
    const fileInput        = document.getElementById("file_new_img");
    const fileNameDisplay  = document.getElementById("file_name_display");
    const previewImagen    = document.querySelector(".imagen_lugar");
    const originalImageUrl = previewImagen.src;

    fileInput.addEventListener("change", function () {
        if (this.files && this.files.length > 0) {
            fileNameDisplay.textContent = this.files[0].name;
            const reader = new FileReader();
            reader.onload = (e) => { previewImagen.src = e.target.result; };
            reader.readAsDataURL(this.files[0]);
        } else {
            fileNameDisplay.textContent = "Click para cambiar la imagen del lugar";
            previewImagen.src = originalImageUrl;
        }
    });

    // ---- Referencias de modales ----
    const id_lugarAct        = lugar.id_lugar;
    const boton_eliminar     = document.getElementById("boton_eliminar_lugar");
    const boton_modificar    = document.getElementById("boton_modificar_lugar");
    const confirm_delete     = document.getElementById("confirm_delete");
    const button_proceder    = document.getElementById("button_proceder");
    const button_revert      = document.getElementById("button_revert");
    const error_in_delete    = document.getElementById("error_in_delete");
    const delete_success     = document.getElementById("delete_success");
    const button_error_revert  = document.getElementById("button_error_revert");
    const button_succes_revert = document.getElementById("button_succes_revert");

    const contenedorForm = boton_modificar.closest("form") || boton_modificar.parentElement;

    // ============================================================
    // INTENTAR ADQUIRIR EL BLOQUEO AL CARGAR LA PÁGINA
    // ============================================================
    let tengoBloqueo = await adquirirBloqueo("lugar", id_lugarAct, idAdmin, boton_modificar);
    
    if (tengoBloqueo) {
        mostrarIndicadorBloqueoActivo(contenedorForm);
    } else {
        // Bloquear todos los inputs si otro admin está adentro
        const idsInputs = ["txt_nombre_lugar", "txt_direccion_lugar", "txt_zona_lugar", "txt_ciudad_lugar", "txt_descripcion_lugar", "file_new_img"];
        idsInputs.forEach(id => {
            const elemento = document.getElementById(id);
            if(elemento) elemento.disabled = true;
        });
        boton_modificar.disabled = true;
        boton_eliminar.disabled = true;
    }

    // ============================================================
    // MODIFICAR LUGAR — flujo con bloqueo
    // ============================================================
    boton_modificar.addEventListener("click", async (e) => {
        e.preventDefault(); // <-- Esto evita que la página se recargue

        const bloqueado = await adquirirBloqueo("lugar", id_lugarAct, idAdmin, boton_modificar);
        if (!bloqueado) return;

        boton_modificar.textContent = "Guardando…";
        boton_modificar.disabled    = true;

        const nombre      = document.getElementById("txt_nombre_lugar").value;
        const descripcion = document.getElementById("txt_descripcion_lugar").value;
        const direccion   = document.getElementById("txt_direccion_lugar").value;
        const zona        = document.getElementById("txt_zona_lugar").value;
        const ciudad      = document.getElementById("txt_ciudad_lugar").value;
        const imagenFile  = document.getElementById("file_new_img").files[0];

        const formData = new FormData();
        formData.append("nombre",          nombre);
        formData.append("descripcion",     descripcion);
        formData.append("direccion",       direccion);
        formData.append("zona",            zona);
        formData.append("ciudad",          ciudad);
        formData.append("id_admin",        idAdmin);
        formData.append("id_lugar_update", id_lugarAct);
        if (imagenFile) formData.append("imagen", imagenFile);

        try {
            const response = await fetch("./../../data/Logic/lugarLogic.php", {
                method: "POST",
                body: formData
            });
            const data = await response.json();

            if (data.correcto) {
                await liberarBloqueo("lugar", id_lugarAct, idAdmin);
                quitarIndicadorBloqueoActivo();

                delete_success.querySelector(".dialog_h3").innerText = "Lugar modificado exitosamente.";
                delete_success.showModal();
            } else {
                await liberarBloqueo("lugar", id_lugarAct, idAdmin);
                quitarIndicadorBloqueoActivo();

                error_in_delete.querySelector(".dialog_h3").innerText = data.mensaje || "Se produjo un error al modificar el lugar.";
                error_in_delete.showModal();
            }
        } catch (err) {
            console.error("Error en el servidor:", err);
            await liberarBloqueo("lugar", id_lugarAct, idAdmin);
            quitarIndicadorBloqueoActivo();

            error_in_delete.querySelector(".dialog_h3").innerText = "Error de conexión al modificar.";
            error_in_delete.showModal();
        } finally {
            boton_modificar.textContent = "Modificar lugar";
            boton_modificar.disabled    = false;
        }
    });

    // ============================================================
    // ELIMINAR LUGAR
    // ============================================================
    boton_eliminar.addEventListener("click", (e) => {
        e.preventDefault();
        confirm_delete.showModal();
    });

    button_proceder.addEventListener("click", async () => {
        confirm_delete.close();

        const bloqueado = await adquirirBloqueo("lugar", id_lugarAct, idAdmin, boton_eliminar);
        if (!bloqueado) return;

        boton_eliminar.disabled = true;

        try {
            const response = await fetch("./../../data/Logic/lugarLogic.php", {
                method: "DELETE",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id_lugar: id_lugarAct, id_admin: idAdmin })
            });
            const data = await response.json();

            if (data.correcto) {
                delete_success.showModal();
            } else {
                await liberarBloqueo("lugar", id_lugarAct, idAdmin);
                error_in_delete.querySelector(".dialog_h3").innerText = data.mensaje || "Error al eliminar el lugar.";
                error_in_delete.showModal();
            }
        } catch (_) {
            await liberarBloqueo("lugar", id_lugarAct, idAdmin);
            error_in_delete.showModal();
        } finally {
            boton_eliminar.disabled = false;
        }
    });

    // ---- Liberar al salir de la pagina ----
    window.addEventListener("beforeunload", () => {
        const params = new URLSearchParams({ accion: "liberar", entidad: "lugar", id: id_lugarAct, id_admin: idAdmin });
        navigator.sendBeacon(`./../../data/Logic/bloqueoLogic.php?${params.toString()}`);
    });

    // ---- Botones de cierre ----
    button_revert.addEventListener("click", () => { confirm_delete.close(); });
    button_error_revert.addEventListener("click", () => { error_in_delete.close(); });
    button_succes_revert.addEventListener("click", () => {
        delete_success.close();
        window.location.href = "./a_gestion_view.html";
    });
});