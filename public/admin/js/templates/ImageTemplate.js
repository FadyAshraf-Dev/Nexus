import Template from "../core/Template.js";

export default class ImageTemplate extends Template {

    gallery(files) {

        if (!files.length) {
            return this.empty();
        }

        return files
            .map((file, index) => this.card(file, index))
            .join("");

    }

    card(file, index) {

        return `
            <div class="col-md-3">

                <div class="card shadow-sm">

                    <img
                        src="${URL.createObjectURL(file)}"
                        class="card-img-top"
                        style="
                            height:180px;
                            object-fit:cover;
                        "
                    >

                    <div class="card-body text-center">

                        <small
                            class="text-truncate d-block mb-2"
                            title="${this.escape(file.name)}">

                            ${this.escape(file.name)}

                        </small>

                        ${this.button({

                            icon: "trash-2",

                            action: "remove-image",

                            id: index,

                            title: "Remove",

                            className:
                                "btn btn-outline-danger btn-sm"

                        })}

                    </div>

                </div>

            </div>
        `;

    }

    empty() {

        return `
            <div class="col-12 text-center py-5 text-muted">

                <i
                    data-feather="image"
                    style="width:48px;height:48px"
                ></i>

                <p class="mt-3 mb-0">

                    No images selected.

                </p>

            </div>
        `;

    }

}