/**
 * Punto único de entrada para carruseles; delega en crearCarrusel con rutas configurables.
 */
import { crearCarrusel } from "../scripts/carousel.js";

export class CarouselManager {
    /**
     * Monta un carrusel en el contenedor indicado.
     * @param {Object} opts - Mismas opciones que crearCarrusel, más:
     * @param {string} [opts.mediaBase] - Ruta base para imágenes (relativa al HTML), ej: "./src/media/images/" o "./../../media/images/"
     * @param {string} [opts.dataBasePath] - Ruta base para cargar JSON por dataFile, ej: "./src/data/"
     */
    static async mount(opts) {
        const { mediaBase, dataBasePath, ...rest } = opts;
        return crearCarrusel({
            mediaBase,
            dataBasePath,
            ...rest
        });
    }
}
