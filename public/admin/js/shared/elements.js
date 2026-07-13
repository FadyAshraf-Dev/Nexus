/**
 * Creates an object containing references to DOM elements.
 *
 * Example:
 * const dom = createDom({
 *     form: "loginForm",
 *     email: "inputEmail",
 *     password: "inputPassword"
 * });
 */

function createDom(elements) {

    return Object.fromEntries(

        Object.entries(elements).map(function ([key, id]) {

            return [key, document.getElementById(id)];

        })

    );

}