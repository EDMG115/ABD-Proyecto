import { renderizarLayout } from "../components/layoutManager.js";

window.addEventListener("load", async function () {
    const usuarioSession = sessionStorage.getItem("usuario_logeado");
    if (usuarioSession == null) {
        window.location.href = "./../../../index.html";
        return;
    }

    const base = "./../../";
    await renderizarLayout({
        header: {
            basePath: base,
            titulo: "PAGINA PRINC",
            fondo: `${base}media/images/layout/img_background_header.jpg`,
            headerScriptPath: `${base}scripts/header_script.js`,
            enlaces: [
                { url: "./usuario_principal.html", texto: "Pagina Principal", icono: `${base}media/images/icons/icon_home.png` },
                { url: "./usuarioviajes.html", texto: "Paquetes de viaje", icono: `${base}media/images/icons/icon_travel.png` },
                { url: "./usuarioEventos.html", texto: "Actividades", icono: `${base}media/images/icons/icon_event.png` },
                { url: "./usuario_historial.html", texto: "Historial", icono: `${base}media/images/icons/icon_event.png` },
                {
                    tipo: "boton",
                    id: "btn_is_r",
                    url: "#",
                    icono: `${base}media/images/icons/icon_user.png`,
                    onClick: () => {}
                }
            ]
        },
        footer: {
            basePath: base,
            fondo: `${base}media/images/layout/imgLayout20.jpg`
        }
    });

    const contenedorLugar = document.getElementById("selecciones");
    const contenedorPaquete = document.getElementById("paquete");
    const modal = document.getElementById("fecha_select");
    const btn_aceptar = document.getElementById("button_continuar");
    const btn_noAceptar = document.getElementById("button_noContinuar");
    const descripcion = document.getElementById("descripcion");
    const tit = document.getElementById("titulo");
    const desc = document.getElementById("desc");
    const ol = document.createElement("ol");

    fetch("./../../data/logic/lugarLogic.php")
        .then((response) => response.json())
        .then((respuesta) => {
            if (!respuesta.correcto || !respuesta.lugares) {
                console.log("Error: no se pudieron obtener los lugares");
                return;
            }

            const lugares = respuesta.lugares;

            lugares.forEach((lugar) => {
                const li = document.createElement("li");
                li.textContent = lugar.nombre_lugar;
                li.setAttribute("class", "minicard");

                li.addEventListener("click", function () {
                    const id_lugar = lugar.id_lugar;

                    fetch(`./../../data/logic/PaquetesLogic.php?id_lugar=${id_lugar}`)
                        .then((response) => response.json())
                        .then((respuestaPaquetes) => {
                            const paquetes = respuestaPaquetes.paquetes;
                            contenedorPaquete.innerHTML = "";

                            paquetes.forEach((paquete) => {
                                tit.innerText = lugar.nombre_lugar;
                                desc.innerText = lugar.descripcion;

                                const div = document.createElement("div");
                                div.innerHTML = `
                            <div class="minicard">
                                <p style="font-size: 20px">${paquete.nombre_paquete}</p>
                                <br>
                                <p style="font-style: italic">Precio: $${paquete.precio}</p>
                            </div>
                        `;
                                contenedorPaquete.appendChild(div);

                                const imgMapa = document.getElementById("imgMapa");
                                imgMapa.src = `./../../media/images/lugares/limg${paquete.id_lugar}.jpg`;

                                let con = 0;

                                div.addEventListener("click", function () {
                                    descripcion.innerText = paquete.descripcion_paquete;
                                    modal.showModal();

                                    btn_aceptar.onclick = () => {
                                        if (con === 0) {
                                            const fecha = document.getElementById("fecha").value;
                                            const hora = document.getElementById("hora").value;
                                            const usuario = JSON.parse(usuarioSession);

                                            const formData = new FormData();
                                            formData.append("id_cliente", usuario.id_cliente);
                                            formData.append("id_paquete", paquete.id_paquete);
                                            formData.append("estado", "pendiente");
                                            formData.append("fecha_viaje", fecha);
                                            formData.append("hora_viaje", hora);

                                            fetch(`./../../data/logic/CrearViajeLogic.php`, {
                                                method: "POST",
                                                body: formData
                                            })
                                                .then((response) => response.json())
                                                .then((respuestaViaje) => {
                                                    console.log("Respuesta viaje:", respuestaViaje.mensaje);
                                                });

                                            con++;
                                            modal.close();
                                        }
                                    };
                                });
                            });

                            if (contenedorPaquete.innerHTML === "") {
                                alert("No tiene paquetes disponibles");
                            }
                        });
                });

                ol.appendChild(li);
            });

            contenedorLugar.appendChild(ol);
        })
        .catch((error) => {
            console.log("Error en fetch:", error);
        });

    btn_noAceptar.addEventListener("click", () => {
        modal.close();
    });
});
