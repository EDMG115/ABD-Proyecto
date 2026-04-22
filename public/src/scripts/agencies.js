import { renderizarLayout } from "../components/layoutManager.js";

let agencia = JSON.parse(sessionStorage.getItem("agencia_logeado"));

window.addEventListener("load", async function () {

    console.log(agencia)
    const idAgencia = agencia.id_agencia;

    if (!agencia) {
        window.location.href = "../../../index.html";
    }


    console.log(agencia.imagen_url);
    // Configuracion inicial
    document.title = agencia.nombre_agencia;

    const imgAgencia = document.getElementById("imgAgencia");
    if (agencia.imagen_url) {
        imgAgencia.src = `../../../src/media/images/agencias/${agencia.imagen_url}`;
        imgAgencia.onerror = function () {
            this.src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120'%3E%3Crect fill='%23e2e8f0' width='120' height='120'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' fill='%2394a3b8' font-size='12'%3EImagen no disponible%3C/text%3E%3C/svg%3E";
            this.alt = "Imagen no disponible";
        };
    } else {
        imgAgencia.alt = "Sin imagen";
        imgAgencia.src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120'%3E%3Crect fill='%23e2e8f0' width='120' height='120'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' fill='%2394a3b8' font-size='12'%3ESin imagen%3C/text%3E%3C/svg%3E";
    }


    // Obtener número paquetes
    fetch(`../../data/Logic/agencyController.php?accion=numeroPaquetes&id_agencia=${idAgencia}`)
        .then(res => res.json())
        .then(json => {
            if (json.correcto) {
                document.getElementById("numeroPaquetes")
                    .innerText = `Numero de paquetes: ${json.total}`;
            } else {
                console.error(json.mensaje);
            }
        });

    // Cargar paquetes
    let paquetes = [];
    fetch(`../../data/Logic/agencyController.php?accion=paquetes&id_agencia=${idAgencia}`)
        .then(res => res.json())
        .then(json => {
            if (json.correcto) {
                paquetes = json.data;
                filtrarPaquetes();
            } else {
                console.error(json.mensaje);
            }
        });

    // Variables
    let asc = true;

    const tbody = document.getElementById("paquetesBody");
    const buscarNombre = document.getElementById("buscarNombre");
    const buscarLugar = document.getElementById("buscarLugar");
    const btnOrdenar = document.getElementById("btnOrdenar");
    const btnAgregar = document.getElementById("btnAgregar");

    function renderTabla(lista) {
        tbody.innerHTML = "";
        lista.forEach(pkg => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td data-label="Nombre">${pkg.nombre_paquete}</td>
                <td data-label="Lugar">${pkg.lugar || "N/A"}</td>
                <td data-label="Precio">$${pkg.precio}</td>
                <td data-label="Acciones">
                    <button class="btn-modificar" data-id="${pkg.id_paquete}">Modificar</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }


    // Filtrar 
    function filtrarPaquetes() {
        let filtrados = [...paquetes];

        const nombre = buscarNombre.value.toLowerCase();
        const lugar = buscarLugar.value.toLowerCase();

        filtrados = filtrados.filter(p => {
            const matchNombre = p.nombre_paquete.toLowerCase().includes(nombre);
            const matchLugar = (p.lugar || "").toLowerCase().includes(lugar);

            return matchNombre && matchLugar;
        });

        // Ordenar por precio
        filtrados.sort((a, b) =>
            asc
                ? parseFloat(a.precio) - parseFloat(b.precio)
                : parseFloat(b.precio) - parseFloat(a.precio)
        );

        renderTabla(filtrados);
    }

    // Eventos input
    [buscarNombre, buscarLugar].forEach(input => {
        input.addEventListener("input", filtrarPaquetes);
    });

    // Ordenar
    btnOrdenar.addEventListener("click", () => {
        asc = !asc;
        filtrarPaquetes();
    });

    // Botones de la tabla
    tbody.addEventListener("click", (e) => {
        if (e.target.classList.contains("btn-modificar")) {
            sessionStorage.setItem("paquete_seleccionado", e.target.dataset.id);
            window.location.href = "packages.html";
        }
    });

    btnAgregar.addEventListener("click", function () {
        window.location.href = "./add_packages.html";
    });

    const base = "./../../../src/";
    await renderizarLayout({
        header: {
            basePath: base,
            titulo: agencia.nombre_agencia,
            fondo: `${base}media/images/layout/img_background_header.jpg`,
            enlaces: [
                { url: "#", texto: "Pagina Principal", icono: `${base}media/images/icons/icon_home.png` },
                { url: "./add_packages.html", texto: "Crear paquete", icono: `${base}media/images/icons/icon_travel.png` },
                { url: "../backup/backup.html", texto: "Respaldos", icono: `${base}media/images/icons/icon_backup.jpg` },
                {
                    tipo: "boton",
                    id: "btn_is_r",
                    url: "#",
                    icono: `${base}media/images/icons/icon_user.png`,
                    onClick: () => {
                        window.location.href = "../../../index.html";
                    }
                }
            ]
        },
        footer: {
            basePath: base,
            fondo: `${base}media/images/layout/imgLayout20.jpg`
        }
    });
});
