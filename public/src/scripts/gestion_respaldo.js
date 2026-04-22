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
        conectarEventos();

    } catch (e) {
        console.error(e);
        showAlert("Error", "No se pudo iniciar.");
    }
});

// ============================================
// CONECTAR EVENTOS A FORMULARIOS
// ============================================
function conectarEventos() {

    const formFull = document.querySelector("form[action='backup_full.php']");
    if (formFull) {
        formFull.addEventListener("submit", async (e) => {
            e.preventDefault();

            const res = await fetch("../../data/Logic/backupLogic.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "accion=backup_full"
            });

            const data = await res.json();

            if (data.archivo) {
                descargarArchivo(data.archivo);
            }

            showAlert("Backup", data.msg);
        });
    }

    const formFecha = document.querySelector("form[action='backup_by_date.php']");
    if (formFecha) {
        formFecha.addEventListener("submit", async (e) => {
            e.preventDefault();

            const formData = new FormData(formFecha);
            const user = obtenerDatosUsuario();

            // Se pasan los id para poder trabajar
            const body = new URLSearchParams({
                accion: "backup_fecha",
                fecha_inicio: formData.get("fecha_inicio"),
                fecha_fin: formData.get("fecha_fin"),
                id_agencia: user.id_agencia,
                id_organizadora: user.id_organizadora
            });

            const res = await fetch("../../data/Logic/backupLogic.php", {
                method: "POST",
                body
            });

            const data = await res.json();

            if (data.archivo) {
                descargarArchivo(data.archivo);
            }

            showAlert("Resultado", data.msg);
        });
    }

    const formRestore = document.querySelector("form[action='restore.php']");
    if (formRestore) {
        formRestore.addEventListener("submit", async (e) => {
            e.preventDefault();

            const archivo = document.getElementById("backup_file").value;

            if (!archivo) {
                showAlert("Error", "Selecciona un archivo");
                return;
            }

            const body = new URLSearchParams({
                accion: "restore",
                archivo: archivo
            });

            const res = await fetch("../../data/Logic/backupLogic.php", {
                method: "POST",
                body
            });

            const data = await res.json();

            showAlert("Restauración", data.msg);
        });
    }

    cargarHistorial();
}


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
        const res = await fetch("../../data/Logic/backupLogic.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "accion=historial"
        });

        const data = await res.json();

        const tbody = document.querySelector(".hist-table tbody");
        tbody.innerHTML = "";

        data.forEach(item => {
            const tr = document.createElement("tr");

            tr.innerHTML = `
                <td>${item.fecha}</td>
                <td>${item.tipo}</td>
                <td>${item.archivo}</td>
                <td>
                    <a href="backups/${item.archivo}" download>Descargar</a>
                </td>
            `;

            tbody.appendChild(tr);
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