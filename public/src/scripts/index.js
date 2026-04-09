import { renderizarLayout } from "../components/layoutManager.js";
import { CarouselManager } from "../components/carouselManager.js";

const base = "./src/";
const API_CARRUSEL = "./src/data/Logic/IndexCarruselLogic.php";

let lugaresPopularesRaw = [];
let eventosCache = [];

function normalizeHash() {
    const h = (location.hash || "#inicio").replace(/^#/, "").toLowerCase();
    if (h === "lugares" || h === "eventos") return h;
    return "inicio";
}

function showView(name) {
    const views = {
        inicio: document.getElementById("view-inicio"),
        lugares: document.getElementById("view-lugares"),
        eventos: document.getElementById("view-eventos")
    };
    Object.entries(views).forEach(([key, el]) => {
        if (!el) return;
        if (key === name) {
            el.hidden = false;
            el.classList.add("index-view--active");
        } else {
            el.hidden = true;
            el.classList.remove("index-view--active");
        }
    });
    window.scrollTo({ top: 0, behavior: "smooth" });
}

async function mountCarousel(containerSelector, dataArray, type, title) {
    const container = document.querySelector(containerSelector);
    if (!container) return;
    container.innerHTML = "";
    if (!dataArray || dataArray.length === 0) {
        container.innerHTML =
            '<p class="index-empty">No hay elementos para mostrar en este momento.</p>';
        return;
    }
    await CarouselManager.mount({
        containerSelector,
        dataArray,
        type,
        title,
        mediaBase: `${base}media/images/`,
        dataBasePath: `${base}data/`
    });
}

function mapLugaresToPaqueteCards(lugares) {
    return lugares.map((lugar) => ({
        nombre_paquete: lugar.nombre_lugar,
        descripcion_paquete:
            lugar.descripcion ||
            (lugar.ciudad ? `${lugar.ciudad} · Lugar destacado` : "Lugar destacado"),
        imagen_url: lugar.imagen_url
    }));
}

window.addEventListener("load", async function () {
    await renderizarLayout({
        header: {
            basePath: base,
            titulo: "PAGINA PRINCIPAL",
            fondo: `${base}media/images/layout/img_background_header.jpg`,
            closeSessionMenu: false,
            enlaces: [
                { url: "#inicio", texto: "Pagina Principal", icono: `${base}media/images/icons/icon_home.png` },
                { url: "#lugares", texto: "Lugares Populares", icono: `${base}media/images/icons/icon_travel.png` },
                { url: "#eventos", texto: "Eventos Recientes", icono: `${base}media/images/icons/icon_event.png` },
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

    async function loadHomeCarousels() {
        const [resL, resE] = await Promise.all([
            fetch(`${API_CARRUSEL}?tipo=lugares`),
            fetch(`${API_CARRUSEL}?tipo=eventos`)
        ]);
        const jsonL = await resL.json();
        const jsonE = await resE.json();

        if (jsonL.correcto && jsonL.data && jsonL.data.length > 0) {
            lugaresPopularesRaw = jsonL.data;
            await mountCarousel(
                "#carrusel-home-lugares",
                mapLugaresToPaqueteCards(jsonL.data),
                "paquete",
                "Lugares más populares"
            );
        } else {
            document.getElementById("carrusel-home-lugares").innerHTML =
                '<p class="index-empty">No hay lugares populares disponibles en este momento.</p>';
        }

        if (jsonE.correcto && jsonE.data && jsonE.data.length > 0) {
            await mountCarousel(
                "#carrusel-home-eventos",
                jsonE.data,
                "evento",
                "Eventos disponibles próximamente"
            );
        } else {
            document.getElementById("carrusel-home-eventos").innerHTML =
                '<p class="index-empty">No hay eventos disponibles en este momento.</p>';
        }
    }

    async function loadLugaresView() {
        let res = await fetch(`${API_CARRUSEL}?tipo=lugares`);
        let json = await res.json();
        if (!json.correcto || !json.data || json.data.length === 0) {
            document.getElementById("carrusel-viajes-lugares").innerHTML =
                '<p class="index-empty">No se pudieron cargar los lugares populares.</p>';
        } else {
            lugaresPopularesRaw = json.data;
            await mountCarousel(
                "#carrusel-viajes-lugares",
                mapLugaresToPaqueteCards(json.data),
                "paquete",
                "Lugares más populares"
            );
        }

        const sel = document.getElementById("filter-paquete-lugar");
        sel.innerHTML = '<option value="">Todos los lugares destacados</option>';
        lugaresPopularesRaw.forEach((l) => {
            const id = l.id_lugar;
            const nombre = l.nombre_lugar || "Lugar";
            if (id == null) return;
            const opt = document.createElement("option");
            opt.value = String(id);
            opt.textContent = nombre;
            sel.appendChild(opt);
        });

        await refreshPaquetesCarrusel();
    }

    async function refreshPaquetesCarrusel() {
        const idLugar = document.getElementById("filter-paquete-lugar").value;
        const orden = document.getElementById("filter-paquete-orden").value;
        let url = `${API_CARRUSEL}?tipo=paquetes`;
        if (idLugar) url += `&id_lugar=${encodeURIComponent(idLugar)}`;
        if (orden === "asc" || orden === "desc") url += `&orden=${orden}`;

        const res = await fetch(url);
        const json = await res.json();
        if (!json.correcto || !json.data || json.data.length === 0) {
            await mountCarousel("#carrusel-viajes-paquetes", [], "paquete", "Paquetes disponibles");
            return;
        }
        await mountCarousel(
            "#carrusel-viajes-paquetes",
            json.data,
            "paquete",
            "Paquetes disponibles"
        );
    }

    function fillCiudadSelectDesdeEventos(eventos) {
        const sel = document.getElementById("filter-evento-ciudad");
        const prev = sel.value;
        const ciudades = [...new Set(eventos.map((e) => (e.ciudad || "").trim()).filter(Boolean))].sort(
            (a, b) => a.localeCompare(b, "es")
        );
        sel.innerHTML = '<option value="">Todas</option>';
        ciudades.forEach((c) => {
            const opt = document.createElement("option");
            opt.value = c;
            opt.textContent = c;
            sel.appendChild(opt);
        });
        if (ciudades.includes(prev)) sel.value = prev;
    }

    function aplicarFiltrosEventosLocal() {
        const q = (document.getElementById("filter-evento-q").value || "").trim().toLowerCase();
        const ciudad = (document.getElementById("filter-evento-ciudad").value || "").trim();

        let lista = eventosCache.slice();
        if (ciudad) {
            lista = lista.filter((e) => (e.ciudad || "").trim() === ciudad);
        }
        if (q) {
            lista = lista.filter((e) => {
                const n = (e.nombre_evento || "").toLowerCase();
                const d = (e.descripcion || "").toLowerCase();
                return n.includes(q) || d.includes(q);
            });
        }
        return lista;
    }

    async function loadEventosView() {
        const res = await fetch(`${API_CARRUSEL}?tipo=eventos`);
        const json = await res.json();
        if (!json.correcto || !json.data) {
            eventosCache = [];
            await mountCarousel("#carrusel-page-eventos", [], "evento", "Eventos disponibles");
            return;
        }
        eventosCache = json.data;
        fillCiudadSelectDesdeEventos(eventosCache);
        const filtrados = aplicarFiltrosEventosLocal();
        await mountCarousel(
            "#carrusel-page-eventos",
            filtrados,
            "evento",
            "Eventos disponibles"
        );
    }

    let homeLoaded = false;
    let lugaresLoaded = false;
    let eventosLoaded = false;

    async function route() {
        const name = normalizeHash();
        showView(name);

        if (name === "inicio") {
            if (!homeLoaded) {
                homeLoaded = true;
                await loadHomeCarousels();
            }
        } else if (name === "lugares") {
            if (!lugaresLoaded) {
                lugaresLoaded = true;
                await loadLugaresView();
            }
        } else if (name === "eventos") {
            if (!eventosLoaded) {
                eventosLoaded = true;
                await loadEventosView();
            }
        }
    }

    document.getElementById("btn-aplicar-paquetes").addEventListener("click", () => {
        refreshPaquetesCarrusel();
    });

    document.getElementById("btn-aplicar-eventos").addEventListener("click", async () => {
        const filtrados = aplicarFiltrosEventosLocal();
        await mountCarousel(
            "#carrusel-page-eventos",
            filtrados,
            "evento",
            "Eventos disponibles"
        );
    });

    window.addEventListener("hashchange", () => route());

    const initial = normalizeHash();
    if (!location.hash || location.hash === "#") {
        history.replaceState(null, "", "#inicio");
    }
    await route();
});
