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
    const fecha = document.getElementById("Fecha");
    const hora = document.getElementById("Hora");
    const tit = document.getElementById("titulo");
    const desc = document.getElementById("desc");
    const ol = document.createElement("ol");

    fetch("./../../data/logic/eventoLogic.php")
        .then((response) => response.json())
        .then((data) => {
            if (!data.correcto || !data.eventos) {
                console.log("Hubo error en el if de correcto y lugares");
                return;
            }

            const eventos = data.eventos;

            eventos.forEach((ev) => {
                const li = document.createElement("li");
                const liTxt = document.createTextNode(ev.nombre_evento);
                li.appendChild(liTxt);
                li.setAttribute("class", "minicard");
                li.addEventListener("click", function () {
                    tit.innerText = ev.nombre_evento;
                    desc.innerText = ev.descripcion;
                    contenedorPaquete.innerHTML = "";

                    const div = document.createElement("div");
                    div.innerHTML = `<div class="minicard"> <p style="font-size: 20px">Reservar</p>
                            <br> <p style="font-style: italic">Precio: $${ev.precio_boleto}</p> </div>`;
                    contenedorPaquete.appendChild(div);
                    const imgMapa = document.getElementById("imgMapa");
                    imgMapa.src = `./../../media/images/events/eimg${ev.id_evento}.png`;
                    let con = 0;

                    div.addEventListener("click", function () {
                        fecha.innerText = "Fecha del evento: " + ev.fecha_evento;
                        hora.innerText = "Hora del evento: " + ev.hora_evento;
                        modal.showModal();

                        btn_aceptar.onclick = () => {
                            if (con === 0) {
                                const miArray = JSON.parse(usuarioSession);
                                const id_evento = ev.id_evento;
                                const id_cliente = miArray.id_cliente;
                                const estado = "pendiente";

                                const formData = new FormData();
                                formData.append("id_evento", id_evento);
                                formData.append("id_cliente", id_cliente);
                                formData.append("estado", estado);

                                fetch(`./../../data/logic/CrearReservacionLogic.php`, {
                                    method: "POST",
                                    body: formData
                                })
                                    .then((response) => response.json())
                                    .then((res) => {
                                        console.log("funcionó??" + res.mensaje);
                                    });
                                con++;
                                modal.close();
                            }
                        };

                        if (contenedorPaquete.innerHTML === "") {
                            alert("No tiene paquetes disponibles");
                        }
                    });
                });
                ol.appendChild(li);
            });

            contenedorLugar.appendChild(ol);
        })
        .catch(() => {
            alert("Error de conexión al servidor. No se pudieron obtener los lugares.");
        });

    btn_noAceptar.addEventListener("click", () => {
        modal.close();
    });
});
