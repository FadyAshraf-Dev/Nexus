export default class Dom {

    constructor(selectors = {}) {

        this.load(selectors);

    }

    load(selectors) {

        for (const [key, id] of Object.entries(selectors)) {

            this[key] =
                document.getElementById(id);

        }

    }

    get(selector) {

        return document.querySelector(selector);

    }

    getAll(selector) {

        return [...document.querySelectorAll(selector)];

    }

    exists(name) {

        return this[name] instanceof Element;

    }

}