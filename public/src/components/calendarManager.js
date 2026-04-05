/**
 * Inicializa el módulo de calendario (modal, eventos, drag & drop).
 * Ahora se encarga de inyectar su propio HTML en el DOM.
 */
export class CalendarManager {
    static _loaded = false;

    /**
     * Inyecta el HTML necesario para los modales del calendario
     */
    static _inyectarHTML() {
        // Verificar si ya existe para no duplicarlo
        if (document.getElementById('calendar-modal')) return;

        const modalHTML = `
            <div class="calendar-modal" id="calendar-modal">
                <div class="calendar-modal__overlay"></div>
                <div class="calendar-modal__content">
                    <button class="calendar-modal__close">×</button>
                    <br><br>
                    <section class="calendar">
                        <section class="calendar__header">
                            <div class="header__container">
                                <button type="button" class="calendar__button calendar__button--previous">Anterior</button>
                                <h3 class="container__heading" id="calendar-date">Mes</h3>
                                <button type="button" class="calendar__button calendar__button--next">Siguiente</button>
                            </div>
                        </section>

                        <section class="calendar__weekdays">
                            <div><h4>Lun</h4></div>
                            <div><h4>Mar</h4></div>
                            <div><h4>Mié</h4></div>
                            <div><h4>Jue</h4></div>
                            <div><h4>Vie</h4></div>
                            <div><h4>Sáb</h4></div>
                            <div><h4>Dom</h4></div>
                        </section>

                        <ol class="calendar__days"></ol>
                    </section>
                </div>
            </div>

            <div class="day-info-modal" id="day-info-modal">
                <div class="day-info-overlay"></div>
                <div class="day-info-content">
                    <button class="day-info-close">×</button>
                    <h2 id="day-info-title">Día</h2>
                    <p id="day-info-description">Descripción del evento</p>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }

    /**
     * Carga el script del calendario y su HTML
     * Es idempotente: solo importa una vez.
     */
    static async init() {
        if (CalendarManager._loaded) return;
        
        // 1. Inyectamos el HTML en el DOM
        this._inyectarHTML();

        // 2. Cargamos la lógica del calendario
        await import("./calendar/calendar.js");
        CalendarManager._loaded = true;
    }
}