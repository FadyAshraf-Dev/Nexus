export default class Template {
    constructor() {
        if (new.target === Template) {
            throw new Error(
                "Template is abstract and cannot be instantiated directly."
            );
        }
    }

    escape(value) {
        const div = document.createElement("div");

        div.textContent = value ?? "";

        return div.innerHTML;
    }

    badge(text, classes = "bg-secondary") {
        return `
            <div class="badge ${classes} rounded-pill">
                ${this.escape(text)}
            </div>
        `;
    }

    button({
        icon,
        action,
        id,
        title,
        className = ""
    }) {
        return `
            <button
                class="btn ${className}"
                data-action="${action}"
                data-id="${id}"
                title="${title}"
            >
                <i data-feather="${icon}"></i>
            </button>
        `;
    }

    image(src, alt = "", width = 60, height = 60) {
        if (!src) {
            return `
                <div
                    class="bg-light rounded d-flex align-items-center justify-content-center"
                    style="width:${width}px;height:${height}px;"
                >
                    <i data-feather="image"></i>
                </div>
            `;
        }

        return `
            <img
                src="${src}"
                alt="${this.escape(alt)}"
                width="${width}"
                height="${height}"
                class="rounded"
                style="object-fit:cover"
            >
        `;
    }

    currency(value) {
        return Number(value).toFixed(2);
    }

    truncate(text, length = 50) {
        if (!text) {
            return "";
        }

        return text.length <= length
            ? this.escape(text)
            : this.escape(text.substring(0, length)) + "...";
    }
}