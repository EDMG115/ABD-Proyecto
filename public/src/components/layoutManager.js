
export async function renderizarHeader(config) {
    const { basePath, titulo, fondo, enlaces, htmlPath = "components/header.html" } = config;

    try {
        const response = await fetch(`${basePath}${htmlPath}`);
        const data = await response.text();
        document.body.insertAdjacentHTML("afterbegin", data);

        // Añadir script del header
        const script = document.createElement("script");
        script.src = `${basePath}scripts/header_script.js`;
        document.body.appendChild(script);

        // Configurar estilos base
        document.getElementById("s_header").style.backgroundImage = `url(${fondo})`;
        document.getElementById("n_h2").innerText = titulo;
        document.getElementById("s_icon").setAttribute("src", `${basePath}media/images/icons/icon_arc.png`);

        // Construir Nav Dinámico usando Template Strings (mucho más limpio que createElement repetitivo)
        const bnav = document.getElementById("underline_nav");
        
        enlaces.forEach((enlace, index) => {
            if (enlace.tipo === "boton") {
                const btnHTML = `
                    <button id="${enlace.id || 'btn_is_r'}">
                        <a href="${enlace.url}" target="${enlace.target || '_self'}">
                            <img class="icon_user" src="${enlace.icono}">
                        </a>
                    </button>
                `;
                bnav.insertAdjacentHTML("beforeend", btnHTML);
                
                // Si el botón requiere un evento (como en el index), lo retornamos o lo manejamos después
                if (enlace.onClick) {
                    document.getElementById(enlace.id || 'btn_is_r').addEventListener("click", enlace.onClick);
                }
            } else {
                const linkHTML = `
                    <a id="a${index + 1}" href="${enlace.url}">
                        <img class="icon_nav" src="${enlace.icono}">
                        ${enlace.texto}
                    </a>
                `;
                bnav.insertAdjacentHTML("beforeend", linkHTML);
            }
        });

    } catch (error) {
        console.error("Error al renderizar el header:", error);
    }
}

export async function renderizarFooter(config) {
    const { basePath, fondo, htmlPath = "components/footer.html" } = config;

    try {
        const response = await fetch(`${basePath}${htmlPath}`);
        const data = await response.text();
        document.body.insertAdjacentHTML("beforeend", data);

        document.getElementById("f_icon").setAttribute("src", `${basePath}media/images/icons/icon_arc.png`);
        document.querySelector(".f_link").href = "#";

        const f_general = document.getElementById("f_general");
        f_general.style.backgroundImage = `url(${fondo})`;
        f_general.style.backgroundPosition = "50% 80%";
    } catch (error) {
        console.error("Error al renderizar el footer:", error);
    }
}