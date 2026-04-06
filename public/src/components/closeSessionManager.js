/**
 * Menú del botón de usuario (#btn_is_r): abre un desplegable con "Cerrar sesión"
 * y redirige al index limpiando sessionStorage.
 */

/**
 * @param {string} basePath - Ruta base hasta src/ (como en layoutManager)
 * @param {string} [explicitHref] - Si se pasa, se usa tal cual
 * @returns {string}
 */
export function resolveIndexHref(basePath, explicitHref) {
    if (explicitHref) return explicitHref;
    const b = basePath.endsWith("/") ? basePath : `${basePath}/`;
    return `${b}../index.html`;
}

/**
 * @param {object} options
 * @param {string} options.basePath
 * @param {string} [options.indexHref]
 */
export function attachCloseSessionMenu({ basePath, indexHref }) {
    const btn = document.getElementById("btn_is_r");
    if (!btn) return;

    const targetIndex = resolveIndexHref(basePath, indexHref);

    const fresh = btn.cloneNode(true);
    btn.parentNode.replaceChild(fresh, btn);

    const wrap = document.createElement("div");
    wrap.className = "session-menu-wrap";

    const parent = fresh.parentNode;
    parent.insertBefore(wrap, fresh);
    wrap.appendChild(fresh);

    const dropdown = document.createElement("div");
    dropdown.className = "session-menu-dropdown";
    dropdown.setAttribute("role", "menu");
    dropdown.hidden = true;

    const logoutBtn = document.createElement("button");
    logoutBtn.type = "button";
    logoutBtn.className = "session-menu__logout";
    logoutBtn.setAttribute("role", "menuitem");
    logoutBtn.textContent = "Cerrar sesión";

    dropdown.appendChild(logoutBtn);
    wrap.appendChild(dropdown);

    const innerA = fresh.querySelector("a");
    if (innerA) {
        innerA.href = "#";
        innerA.addEventListener("click", (e) => e.preventDefault());
    }

    function closeMenu() {
        dropdown.hidden = true;
        fresh.setAttribute("aria-expanded", "false");
    }

    function openMenu() {
        dropdown.hidden = false;
        fresh.setAttribute("aria-expanded", "true");
    }

    function toggleMenu(e) {
        e.preventDefault();
        e.stopPropagation();
        if (dropdown.hidden) openMenu();
        else closeMenu();
    }

    fresh.setAttribute("aria-haspopup", "true");
    fresh.setAttribute("aria-expanded", "false");
    fresh.setAttribute("type", "button");
    fresh.addEventListener("click", toggleMenu);

    logoutBtn.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        try {
            sessionStorage.clear();
        } catch (_) {
            /* ignore */
        }
        window.location.href = targetIndex;
    });

    document.addEventListener("click", (e) => {
        if (!wrap.contains(e.target)) closeMenu();
    });

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeMenu();
    });
}
