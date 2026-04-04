import { renderizarLayout } from "../components/layoutManager.js";
import { CalendarManager } from "../components/calendarManager.js";

let organizadora = JSON.parse(sessionStorage.getItem("organizador_logeado"));

window.addEventListener("load", async function () {
    await CalendarManager.init();

    console.log(organizadora)
    const idOrg = organizadora.id_organizadora;

    if (!organizadora) {
        window.location.href = "../../../index.html";
    }


    console.log(organizadora.imagen_url);
    // Configuracion inicial
    document.title = organizadora.nombre_agencia;

    const imgAgencia = document.getElementById("imgAgencia");
    if (organizadora.imagen_url) {
        imgAgencia.src = `../../../src/media/images/organizers/${organizadora.imagen_url}`;
        imgAgencia.onerror = function () {
            this.src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120'%3E%3Crect fill='%23e2e8f0' width='120' height='120'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' fill='%2394a3b8' font-size='12'%3EImagen no disponible%3C/text%3E%3C/svg%3E";
            this.alt = "Imagen no disponible";
        };
    } else {
        imgAgencia.alt = "Sin imagen";
        imgAgencia.src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120'%3E%3Crect fill='%23e2e8f0' width='120' height='120'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' fill='%2394a3b8' font-size='12'%3ESin imagen%3C/text%3E%3C/svg%3E";
    }


    // Obtener número eventos mes
    fetch(`../../data/Logic/organizerController.php?accion=numeroEventosMes&id_organizadora=${idOrg}`)
        .then(res => res.json())
        .then(json => {
            if (json.correcto) {
                document.getElementById("numeroEventos")
                    .innerText = `Numero de eventos este mes: ${json.total}`;
            } else {
                console.error(json.mensaje);
            }
        });

    // Cargar eventos
    let eventos = [];
    fetch(`../../data/Logic/organizerController.php?accion=eventos&id_organizadora=${idOrg}`)
        .then(res => res.json())
        .then(json => {
            if (json.correcto) {
                eventos = json.data;
                filtrarEventos();
            } else {
                console.error(json.mensaje);
            }
        });

    // Variables
    let asc = true;

    const tbody = document.getElementById("eventosBody");
    const buscarNombre = document.getElementById("buscarNombre");
    const buscarLugar = document.getElementById("buscarLugar");
    const fechaInicio = document.getElementById("fechaInicio");
    const fechaFin = document.getElementById("fechaFin");
    const btnOrdenar = document.getElementById("btnOrdenar");
    const btnAgregar = document.getElementById("btnAgregar");
    const btnCalendar = document.getElementById("btnCalendar");

    function renderTabla(lista) {
        tbody.innerHTML = "";
        lista.forEach(ev => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td data-label="Fecha">${ev.fecha_evento}</td>
                <td data-label="Nombre">${ev.nombre_evento}</td>
                <td data-label="Lugar">${ev.lugar || "N/A"}</td>
                <td data-label="Precio">$${ev.precio_boleto}</td>
                <td data-label="Acciones">
                    <button class="btn-modificar" data-id="${ev.id_evento}">Modificar</button>
                    <button class="btn-calendario" data-id="${ev.id_evento}">📅</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }


    // Filtrar 
    function filtrarEventos() {
        let filtrados = [...eventos];

        const nombre = buscarNombre.value.toLowerCase();
        const lugar = buscarLugar.value.toLowerCase();
        const inicio = fechaInicio.value ? new Date(fechaInicio.value) : null;
        const fin = fechaFin.value ? new Date(fechaFin.value) : null;

        filtrados = filtrados.filter(e => {
            const fechaEv = new Date(e.fecha_evento);
            const matchNombre = e.nombre_evento.toLowerCase().includes(nombre);
            const matchLugar = (e.lugar || "").toLowerCase().includes(lugar);
            const matchFecha =
                (!inicio || fechaEv >= inicio) &&
                (!fin || fechaEv <= fin);

            return matchNombre && matchLugar && matchFecha;
        });

        // Ordenar por fecha
        filtrados.sort((a, b) =>
            asc
                ? new Date(a.fecha_evento) - new Date(b.fecha_evento)
                : new Date(b.fecha_evento) - new Date(a.fecha_evento)
        );

        renderTabla(filtrados);
    }

    // Eventos input
    [buscarNombre, buscarLugar, fechaInicio, fechaFin].forEach(input => {
        input.addEventListener("input", filtrarEventos);
    });

    // Ordenar
    btnOrdenar.addEventListener("click", () => {
        asc = !asc;
        filtrarEventos();
    });

    btnCalendar.addEventListener("click", () => {

        const fecha = new Date();
        window.dispatchEvent(new CustomEvent("abrirCalendario", {
            detail: {
                month: fecha.getMonth(),
                year: fecha.getFullYear(),
                source: "organizer" //organizer
            }
        }));

    });

    // Botones de la tabla
    tbody.addEventListener("click", (e) => {

        if (e.target.classList.contains("btn-modificar")) {
            this.sessionStorage.setItem("evento_seleccionado", e.target.dataset.id);
            window.location.href = "events.html";

        } else if (e.target.classList.contains("btn-calendario") && e.target.id != "btnCalendar") {

            const evento = eventos.find(ev => ev.id_evento == e.target.dataset.id);
            if (evento) {
                const fecha = new Date(evento.fecha_evento);
                sessionStorage.setItem("evento_seleccionado", evento.id_evento);

                window.dispatchEvent(new CustomEvent("abrirCalendario", {
                    detail: {
                        month: fecha.getMonth(),
                        year: fecha.getFullYear(),
                        source: "event" //organizer,
                    }
                }));
            }
        }

    });

    btnAgregar.addEventListener("click", function () {
        window.location.href = "./add_events.html";
    });

    const base = "./../../../src/";
    await renderizarLayout({
        header: {
            basePath: base,
            titulo: organizadora.nombre_agencia,
            fondo: `${base}media/images/layout/img_background_header.jpg`,
            enlaces: [
                { url: "#", texto: "Pagina Principal", icono: `${base}media/images/icons/icon_home.png` },
                { url: "./add_events.html", texto: "Crear evento", icono: `${base}media/images/icons/icon_event.png` },
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
