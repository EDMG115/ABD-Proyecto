/**
 * Layout modular: header y footer reutilizables en todo el proyecto.
 * Las rutas basePath son relativas al documento HTML (no al .js).
 */

import { attachCloseSessionMenu } from "./closeSessionManager.js";

/**
 * @param {Object} config
 * @param {string} config.basePath - Ruta hasta la carpeta src/ (ej: "./src/", "./../../")
 * @param {string} [config.titulo] - Texto del h2 del header
 * @param {string} config.fondo - URL de imagen de fondo del header
 * @param {Array} [config.enlaces=[]] - { tipo: "link"|"boton", url, texto?, icono, id?, onClick?, target? }
 * @param {string} [config.htmlPath="components/header.html"]
 * @param {string} [config.headerScriptPath] - Por defecto basePath + scripts/header_script.js
 * @param {function} [config.onHeaderReady] - async (ctx) => {} con { s_header, n_h2, s_icon, bnav }
 * @param {boolean} [config.closeSessionMenu=true] - Si es true, añade #btn_is_r si falta y menú "Cerrar sesión". En index usar false.
 * @param {string} [config.indexHref] - URL al index (opcional; por defecto relativa desde basePath)
 */
export async function renderizarHeader(config) {
    const {
        basePath,
        titulo = "",
        fondo,
        enlaces: enlacesIn = [],
        htmlPath = "components/header.html",
        headerScriptPath,
        onHeaderReady,
        closeSessionMenu = true,
        indexHref
    } = config;

    const enlaces = [...enlacesIn];
    const tieneBtnSesion = enlaces.some(
        (e) => e.tipo === "boton" && e.id === "btn_is_r"
    );
    if (closeSessionMenu && !tieneBtnSesion) {
        enlaces.push({
            tipo: "boton",
            id: "btn_is_r",
            url: "#",
            icono: `${basePath}media/images/icons/icon_user.png`,
            onClick: () => {}
        });
    }

    const scriptPath = headerScriptPath || `${basePath}scripts/header_script.js`;

    try {
        const response = await fetch(`${basePath}${htmlPath}`);
        const data = await response.text();
        document.body.insertAdjacentHTML("afterbegin", data);

        const script = document.createElement("script");
        script.src = scriptPath;
        document.body.appendChild(script);

        const s_header = document.getElementById("s_header");
        if (s_header) s_header.style.backgroundImage = `url(${fondo})`;
        const n_h2 = document.getElementById("n_h2");
        if (n_h2) n_h2.innerText = titulo;
        const s_icon = document.getElementById("s_icon");
        if (s_icon) s_icon.setAttribute("src", `${basePath}media/images/icons/icon_arc.png`);

        const bnav = document.getElementById("underline_nav");
        if (bnav && enlaces.length > 0) {
            enlaces.forEach((enlace, index) => {
                if (enlace.tipo === "boton") {
                    const btnHTML = `
                        <button id="${enlace.id || "btn_is_r"}" type="button">
                            <a href="${enlace.url || "#"}">
                                <img class="icon_user" src="${enlace.icono}" alt="">
                            </a>
                        </button>
                    `;
                    bnav.insertAdjacentHTML("beforeend", btnHTML);
                    const btnEl = document.getElementById(enlace.id || "btn_is_r");
                    if (btnEl && typeof enlace.onClick === "function") {
                        btnEl.addEventListener("click", (e) => {
                            e.preventDefault();
                            enlace.onClick(e);
                        });
                        const innerA = btnEl.querySelector("a");
                        if (innerA) {
                            innerA.addEventListener("click", (e) => e.preventDefault());
                        }
                    }
                } else {
                    const linkHTML = `
                        <a id="a${index + 1}" href="${enlace.url || "#"}">
                            <img class="icon_nav" src="${enlace.icono}" alt="">
                            ${enlace.texto || ""}
                        </a>
                    `;
                    bnav.insertAdjacentHTML("beforeend", linkHTML);
                }
            });
        }

        if (typeof onHeaderReady === "function") {
            await onHeaderReady({ s_header, n_h2, s_icon, bnav: document.getElementById("underline_nav") });
        }

        if (closeSessionMenu && document.getElementById("btn_is_r")) {
            attachCloseSessionMenu({ basePath, indexHref });
        }
    } catch (error) {
        console.error("Error al renderizar el header:", error);
    }
}

/**
 * @param {Object} config
 * @param {string} config.basePath
 * @param {string} config.fondo - URL imagen fondo footer
 * @param {string} [config.htmlPath="components/footer.html"]
 * @param {function} [config.onFooterReady]
 */
export async function renderizarFooter(config) {
    const { basePath, fondo, htmlPath = "components/footer.html", onFooterReady } = config;

    try {
        const response = await fetch(`${basePath}${htmlPath}`);
        const data = await response.text();
        document.body.insertAdjacentHTML("beforeend", data);

        const f_icon = document.getElementById("f_icon");
        if (f_icon) f_icon.setAttribute("src", `${basePath}media/images/icons/icon_arc.png`);
        const f_link = document.querySelector(".f_link");
        if (f_link) f_link.href = "#";

        const f_general = document.getElementById("f_general");
        if (f_general) {
            f_general.style.backgroundImage = `url(${fondo})`;
            f_general.style.backgroundPosition = "50% 80%";
        }

        if (typeof onFooterReady === "function") {
            await onFooterReady({ f_icon, f_general, f_link });
        }
    } catch (error) {
        console.error("Error al renderizar el footer:", error);
    }
}

/**
 * Renderiza header y footer en secuencia.
 * @param {{ header: Object, footer: Object }} layout
 */
export async function renderizarLayout(layout) {
    const { header, footer } = layout;
    if (header) await renderizarHeader(header);
    if (footer) await renderizarFooter(footer);
}
