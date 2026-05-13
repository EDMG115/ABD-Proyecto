console.log("JS cargado");

import { renderizarLayout } from "../components/layoutManager.js";


function obtenerDatosUsuario() {
    const tipo = sessionStorage.getItem("tipo_usuario");

    let id_agencia = null;
    let id_organizadora = null;

    if (tipo == 4) {
        const agencia = JSON.parse(sessionStorage.getItem("agencia_logeado"));
        id_agencia = agencia?.id_agencia || null;
    }

    if (tipo == 3) {
        const org = JSON.parse(sessionStorage.getItem("organizador_logeado"));
        id_organizadora = org?.id_organizadora || null;
    }

    return { tipo, id_agencia, id_organizadora };
}

/*

document.getElementById("btn_usuario").addEventListener("click", function () {
        sessionStorage.setItem("tipo_usuario", "1");
        window.location.href = "./src/html/ingreso.html";
    });

    document.getElementById("btn_admin").addEventListener("click", function () {
        sessionStorage.setItem("tipo_usuario", "2");
        window.location.href = "./src/html/ingreso.html";
    });

    document.getElementById("btn_organizador").addEventListener("click", function () {
        sessionStorage.setItem("tipo_usuario", "3");
        window.location.href = "./src/html/ingreso.html";
    });

    document.getElementById("btn_agencia").addEventListener("click", function () {
        sessionStorage.setItem("tipo_usuario", "4");
        window.location.href = "./src/html/ingreso.html";
    });

*/

// ============================================
// CONFIGURACIÓN POR ROL
// ============================================
function obtenerConfigPorRol(base) {
    const tipo_usuario = sessionStorage.getItem("tipo_usuario");

    if (tipo_usuario == 2) {
        return {
            titulo: "Panel de Administración",
            enlaces: [
                { url: "../admin/a_gestion_view.html", texto: "Gestion de lugares", icono: `${base}media/images/icons/iconAnav1.png` },
                { url: "../admin/a_info_events.html", texto: "Estadisticas", icono: `${base}media/images/icons/iconAnav3.png` },
                { url: "../backup/backup.html", texto: "Respaldos", icono: `${base}media/images/icons/icon_backup.jpg` }
            ],
            permisos: {
                backupCompleto: true,
                restaurar: true
            }
        };
    }

    if (tipo_usuario == 4) {
        return {
            titulo: "Panel Agencia de Viajes",
            enlaces: [
                { url: "../agencies/agency.html", texto: "Pagina Principal", icono: `${base}media/images/icons/icon_home.png` },
                { url: "../agencies/add_packages.html", texto: "Crear paquete", icono: `${base}media/images/icons/icon_travel.png` },
                { url: "../backup/backup.html", texto: "Respaldos", icono: `${base}media/images/icons/icon_backup.jpg` }
            ],
            permisos: {
                backupCompleto: false,
                restaurar: false
            }
        };
    }

    if (tipo_usuario == 3) {
        return {
            titulo: "Panel Organizadora de Eventos",
            enlaces: [
                { url: "../organizers/organizer.html", texto: "Pagina Principal", icono: `${base}media/images/icons/icon_home.png` },
                { url: "../organizers/add_events.html", texto: "Crear evento", icono: `${base}media/images/icons/icon_event.png` },
                { url: "../backup/backup.html", texto: "Respaldos", icono: `${base}media/images/icons/icon_backup.jpg` }
            ],
            permisos: {
                backupCompleto: false,
                restaurar: false
            }
        };
    }

    return null;
}

// ============================================
// INICIO
// ============================================
window.addEventListener("load", async function () {
    try {
        const base = "./../../../src/";
        const config = obtenerConfigPorRol(base);

        await renderizarLayout({
            header: {
                basePath: base,
                titulo: "Gestión de respaldos",
                fondo: `${base}media/images/layout/img_background_header.jpg`,
                enlaces: [
                    ...config.enlaces,
                    {
                        tipo: "boton",
                        id: "btn_is_r",
                        url: "#",
                        icono: `${base}media/images/icons/icon_user.png`,
                        onClick: () => window.location.href = "../../../index.html"
                    }
                ]
            },
            footer: {
                basePath: base,
                fondo: `${base}media/images/layout/imgLayout20.jpg`
            }
        });

        aplicarPermisos(config);


    } catch (e) {
        window.location.href = "./../../../index.html";

    }
});


function descargarArchivo(nombreArchivo) {
    const link = document.createElement("a");
    link.href = `backups/${nombreArchivo}`;
    link.download = nombreArchivo;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// ============================================
// HISTORIAL
// ============================================
async function cargarHistorial() {

    try {

        const res = await fetch("../../data/DAO/backupAdminDAO.php", {
    method: "POST",
    headers: {
        "Content-Type":
        "application/x-www-form-urlencoded"
    },
    body: "accion=historial"
})

        const data = await res.json();

        const tbody =
        document.querySelector(".hist-table tbody");

        tbody.innerHTML = "";

        data.forEach(item => {

            const tr = document.createElement("tr");

            tr.innerHTML = `
                <td>${item.fecha}</td>

                <td>
                    <span class="tag-backup">
                        ${item.tipo}
                    </span>
                </td>

                <td>${item.archivo}</td>

                <td class="acciones">

                    <a
                        href="../../data/DB/backups/${item.archivo}"
                        class="btn-descargar"
                        download
                    >
                        Descargar
                    </a>

                    <button
                        class="btn-restaurar"
                    >
                        Restaurar
                    </button>

                </td>
            `;

            tbody.appendChild(tr);

            // EVENTO
            const btnRestaurar =
            tr.querySelector(".btn-restaurar");

            btnRestaurar.addEventListener(
                "click",
                () => {
                    restaurarBackup(item.archivo);
                }
            );

        });

    } catch (e) {

        console.error(e);

    }

}

// ============================================
// PERMISOS VISUALES
// ============================================
function aplicarPermisos(config) {
    document.querySelector(".page-title").textContent = config.titulo;

    if (!config.permisos.backupCompleto) {
        ocultarSeccion("Respaldo completo de la base de datos");
    }

    if (!config.permisos.restaurar) {
        ocultarSeccion("Restaurar base de datos");
    }
}

function ocultarSeccion(texto) {
    document.querySelectorAll(".card").forEach(sec => {
        const h2 = sec.querySelector("h2");
        if (h2 && h2.textContent.includes(texto)) {
            sec.style.display = "none";
        }
    });
}

// ============================================
// MODAL 
// ============================================
function ensureModalContainer() {
    if (!document.getElementById('app-modals')) {
        const c = document.createElement('div');
        c.id = 'app-modals';
        document.body.appendChild(c);
    }
}

function showAlert(title, message) {
    ensureModalContainer();
    const container = document.getElementById('app-modals');

    const modal = document.createElement('div');
    modal.className = 'app-modal active';
    modal.innerHTML = `
        <div class="app-modal__overlay"></div>
        <div class="app-modal__content">
            <h2>${title}</h2>
            <p>${message}</p>
            <button id="okBtn">OK</button>
        </div>
    `;

    container.appendChild(modal);

    modal.querySelector("#okBtn").onclick = () => modal.remove();
}
//================================
// RESTAURACION
// ===============================
async function restaurarBackup(archivo) {

    const confirmacion =
    await Swal.fire({

        title: '¿Restaurar respaldo?',

        text:
        `Se restaurará: ${archivo}`,

        icon: 'warning',

        showCancelButton: true,

        confirmButtonText:
        'Sí, restaurar',

        cancelButtonText:
        'Cancelar'
    });

    if (!confirmacion.isConfirmed)
        return;

    Swal.fire({

        title: 'Restaurando...',

        allowOutsideClick: false,

        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {

        const respuesta =
        await fetch(

            './../../data/DAO/backupRestoreDAO.php',

            {

                method: 'POST',

                headers: {

                    'Content-Type':
                    'application/x-www-form-urlencoded'
                },

                body:
                `backup_file=${encodeURIComponent(archivo)}`
            }
        );

        const texto =
        await respuesta.text();

        if (texto.includes("✅")) {

            Swal.fire({

                icon: 'success',

                title:
                'Restauración exitosa',

                text: texto,
                 confirmButtonColor: '#023E8A',
                cancelButtonColor: '#dc2626'
            });

        } else {

            Swal.fire({

                icon: 'error',

                title: 'Error',

                text: texto
            });

        }

    } catch (error) {

        Swal.fire({

            icon: 'error',

            title:
            'Error de conexión',

            text: error
        });

    }
}
   // ============================
   // POPUP de confirmacion
   // ===============================
document.addEventListener("DOMContentLoaded", () => {
    
    cargarHistorial();

    // =============================
    // Generar Respaldo
    // =============================
    const form = document.getElementById("backup");

    form.addEventListener("submit", async (e) => {

        e.preventDefault();

     
        Swal.fire({
            title: 'Generando respaldo...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {

            const respuesta = await fetch("./../../data/DAO/backupAdminDAO.php", {
                method: "POST"
            });

            const texto = await respuesta.text();

            // ✅ ÉXITO
            if (texto.includes("✅")) {

                Swal.fire({
                    icon: 'success',
                    title: 'Respaldo generado',
                    text: texto,
                    confirmButtonText: 'Aceptar',

                confirmButtonColor: '#023E8A',
                cancelButtonColor: '#dc2626'
                });
                cargarHistorial();
            } 
            
            // ❌ ERROR
            else {

                Swal.fire({
                    icon: 'error',
                    title: 'Error al generar respaldo',
                    text: texto,
                    confirmButtonText: 'Cerrar'
                });

            }

        } catch (error) {

            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: error,
                confirmButtonText: 'Cerrar'



            });

        }

    });

    // ==================================
    // Restaurar Respaldo
    // ==================================
    


});

