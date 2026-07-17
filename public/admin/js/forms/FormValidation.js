import Feedback from "../shared/Feedback.js";
import DynamicConstraints from "../products/DynamicConstraints.js";

export default class FormValidation {
  constructor(dom) {
    this.dom = dom;

    this.started = false;
  }
  start() {
    this.started = true;
  }
  initialize() {
    this.markRequiredFields();

    this.dom.form
      .querySelectorAll("input, select, textarea")
      .forEach((field) => {
        field.addEventListener("input", () => this.handleInput(field));

        field.addEventListener("change", () => this.handleChange(field));
      });
  }

  validate(container) {
    const fields = this.getFields(container);

    let valid = true;

    let firstInvalid = null;

    fields.forEach((field) => {
      if (field.disabled) {
        return;
      }

      if (field.type === "file") {
        if (!field.checkValidity()) {
          Feedback.show(field, field.validationMessage);

          valid = false;

          firstInvalid ??= field;
        } else if (!this.validateFileCount(field)) {
          valid = false;

          firstInvalid ??= field;
        }

        return;
      }

      if (!field.checkValidity()) {
        Feedback.show(field, field.validationMessage);

        valid = false;

        firstInvalid ??= field;
      } else {
        Feedback.clear(field);
      }
    });

    firstInvalid?.focus();

    return valid;
  }
  getFields(container) {
    return [...container.querySelectorAll("input, select, textarea")];
  }
  validateForm() {
    return this.validate(this.dom.form);
  }
  handleInput(field) {
    if (field.type === "file" || !this.started) {
      return;
    }

    if (field.checkValidity()) {
      Feedback.clear(field);
    } else {
      Feedback.show(field, field.validationMessage);
    }
  }

  handleChange(field) {
    if (!this.started) {
      return;
    }
    if (field.type === "file") {
      if (!field.checkValidity()) {
        Feedback.show(field, field.validationMessage);
      } else {
        this.validateFileCount(field);
      }

      return;
    }

    if (field.checkValidity()) {
      Feedback.clear(field);
    } else {
      Feedback.show(field, field.validationMessage);
    }
  }

  validateFileCount(field) {
    if (field.files.length > DynamicConstraints.MAX_IMAGES) {
      Feedback.show(
        field,
        `You may upload a maximum of ${DynamicConstraints.MAX_IMAGES} images.`,
      );

      return false;
    }

    Feedback.clear(field);

    return true;
  }

  markRequiredFields() {
    this.dom.getAll("[required]").forEach((field) => {
      const label = document.querySelector(`label[for="${field.id}"]`);

      label?.classList.add("required");
    });
  }
}
