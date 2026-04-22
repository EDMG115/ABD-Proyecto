import { renderizarLayout } from "../components/layoutManager.js";

window.addEventListener("load", async function () {
    const administradorSession = sessionStorage.getItem("admin_logeado");
    if (administradorSession == null) {
        window.location.href = "./../../../index.html";
        return;
    }

    const base = "./../../";
    await renderizarLayout({
        header: {
            basePath: base,
            titulo: "Gestion de los lugares del sitio",
            fondo: `${base}media/images/layout/imgLayout20.jpg`,
            headerScriptPath: `${base}scripts/header_script.js`,
            enlaces: [
                { url: "./a_gestion_view.html", texto: "Gestion de lugares", icono: `${base}media/images/icons/iconAnav1.png` },
                { url: "./a_info_events.html", texto: "Estadisticas", icono: `${base}media/images/icons/iconAnav3.png` },
                { url: "../backup/backup.html", texto: "Respaldos", icono: `${base}media/images/icons/icon_backup.jpg` },
            ],
            onHeaderReady: ({ s_header }) => {
                if (s_header) s_header.style.backgroundPosition = "50% 50%";
            }
        },
        footer: {
            basePath: base,
            fondo: `${base}media/images/layout/imgLayout15.jpg`,
            onFooterReady: ({ f_general }) => {
                if (f_general) f_general.style.backgroundPosition = "50% 80%";
            }
        }
    });

    const btnCrear = document.getElementById("btn_crear_lugar");
    if (btnCrear) {
        btnCrear.addEventListener("click", () => {
            window.location.href = "./a_crear_lugar.html";
        });
    }

    fetch("./../../data/logic/lugarLogic.php")
        .then((response) => response.json())
        .then((data) => {
            if (data.correcto && data.lugares) {
                const lugares = data.lugares;
                const contenedor_lugares = document.getElementById("contenedor_lugares");
                const zonas = {};

                lugares.forEach((lugar) => {
                    let contenedorZona;

                    if (!zonas[lugar.zona]) {
                        const box_zone = document.createElement("div");
                        box_zone.classList.add("box-zone");
                        box_zone.id = `zona-${lugar.zona}`;

                        const div_zona = document.createElement("div");
                        div_zona.classList.add("div-zone");
                        const h2 = document.createElement("h2");
                        h2.innerText = lugar.zona;
                        div_zona.appendChild(h2);

                        const div_card = document.createElement("div");
                        div_card.classList.add("div-card");
                        div_card.id = `cards-${lugar.zona}`;

                        box_zone.appendChild(div_zona);
                        box_zone.appendChild(div_card);
                        contenedor_lugares.appendChild(box_zone);

                        zonas[lugar.zona] = div_card;
                    }

                    contenedorZona = zonas[lugar.zona];

                    const card = document.createElement("div");
                    card.classList.add("cards");
                    
                    const imgSrc = lugar.imagen_url;

                    console.log(lugar.imagen_url);

                    card.innerHTML = `
                <div class="div-info-card">
                    <h2>${lugar.nombre_lugar}</h2>
                    <span class="info-card-ciudad">${lugar.ciudad || ""}</span>
                    <span class="info-card-direccion">${lugar.direccion || ""}</span>
                    <button type="button" class="button btn-gestionar-lugar">Gestionar lugar</button>
                </div>
                <div class="div-image-card">
                    <img class="image-card" src="${imgSrc}" alt="${lugar.nombre_lugar}">
                </div>
            `;

                    contenedorZona.appendChild(card);

                    card.querySelector(".btn-gestionar-lugar").addEventListener("click", function () {
                        sessionStorage.setItem("id_lugar_selected", lugar.id_lugar);
                        sessionStorage.setItem("lugar_objeto", JSON.stringify(lugar));
                        window.location.href = "./a_gestion_moves.html";
                    });
                });
            }
        });
});
