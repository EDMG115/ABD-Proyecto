/**
 * Inicializa el módulo de calendario (modal, eventos, drag & drop).
 * Debe llamarse una vez por página que incluya el markup del calendario en el DOM.
 */
export class CalendarManager {
    static _loaded = false;

    /**
     * Carga el script del calendario (efectos secundarios: registra listeners).
     * Es idempotente: solo importa una vez.
     */
    static async init() {
        if (CalendarManager._loaded) return;
        await import("./calendar/calendar.js");
        CalendarManager._loaded = true;
    }
}
