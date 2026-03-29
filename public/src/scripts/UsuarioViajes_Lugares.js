window.addEventListener("load", function () {
    const usuarioSession = sessionStorage.getItem("usuario_logeado");
    if (usuarioSession == null) {
        window.location.href = "./../../../index.html";
        return;
    }
    let contenedorLugar = document.getElementById("selecciones");
    let contenedorPaquete = document.getElementById("paquete");
    let paqcont = 1;
    let ol = document.createElement("ol");
    let modal = document.getElementById("fecha_select");
    let btn_aceptar = document.getElementById("button_continuar");
    let btn_noAceptar = document.getElementById("button_noContinuar");
    let descripcion = document.getElementById("descripcion");
    let tit = document.getElementById("titulo");
    let desc = document.getElementById("desc");

    fetch("./../../data/logic/lugarLogic.php")
        .then(response => response.json())
        .then(respuesta => {

            console.log("Respuesta completa:", respuesta);

            if (respuesta.correcto && respuesta.lugares) {

                const lugares = respuesta.lugares;

                lugares.forEach(lugar => {

                    let li = document.createElement("li");
                    li.textContent = lugar.nombre_lugar;
                    li.setAttribute("class", "minicard");

                    li.addEventListener("click", function () {

                        let id_lugar = lugar.id_lugar;
                        console.log("ID lugar:", id_lugar);

                        fetch(`./../../data/logic/PaquetesLogic.php?id_lugar=${id_lugar}`)
                            .then(response => response.json())
                            .then(respuestaPaquetes => {

                                console.log("Paquetes:", respuestaPaquetes);

                                const paquetes = respuestaPaquetes.paquetes;
                                contenedorPaquete.innerHTML = "";

                                paquetes.forEach(paquete => {

                                    tit.innerText = lugar.nombre_lugar;
                                    desc.innerText = lugar.descripcion;

                                    let div = document.createElement("div");

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

                                        // ⚠️ Usar onclick en lugar de addEventListener
                                        btn_aceptar.onclick = () => {

                                            if (con === 0) {

                                                let fecha = document.getElementById("fecha").value;
                                                let hora = document.getElementById("hora").value;

                                                const usuario = JSON.parse(usuarioSession);

                                                const formData = new FormData();
                                                formData.append("id_cliente", usuario.id_cliente);
                                                formData.append("id_paquete", paquete.id_paquete);
                                                formData.append("estado", "pendiente");
                                                formData.append("fecha_viaje", fecha);
                                                formData.append("hora_viaje", hora);

                                                fetch(`./../../data/logic/CrearViajeLogic.php`, {
                                                    method: 'POST',
                                                    body: formData
                                                })
                                                    .then(response => response.json())
                                                    .then(respuestaViaje => {
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

            } else {
                console.log("Error: no se pudieron obtener los lugares");
            }

        })
        .catch(error => {
            console.log("Error en fetch:", error);
        });
    btn_noAceptar.addEventListener("click", () => {
        modal.close();
    });

}

)