const MAX_IMAGES = 5;
let validationState = null;
/* =====================================================
   Container Validation
===================================================== */

function validateContainer(container) {
  const fields = container.querySelectorAll("input, select, textarea");

  let valid = true;
  let firstInvalid = null;

  fields.forEach(function (field) {
    if (field.disabled) {
      return;
    }

    /* ---------- File Inputs ---------- */

    if (field.type === "file") {
      if (!field.checkValidity()) {
        showFeedback(field, field.validationMessage);

        valid = false;

        if (!firstInvalid) {
          firstInvalid = field;
        }
      } else if (!validateFileCount(field)) {
        valid = false;

        if (!firstInvalid) {
          firstInvalid = field;
        }
      }

      return;
    }

    /* ---------- Everything Else ---------- */

    if (!field.checkValidity()) {
      showFeedback(field, field.validationMessage);

      valid = false;

      if (!firstInvalid) {
        firstInvalid = field;
      }
    } else {
      clearFeedback(field);
    }
  });

  if (firstInvalid) {
    firstInvalid.focus();
  }

  return valid;
}

/* =====================================================
   Live Validation
===================================================== */

function initializeFormValidation(app) {
  markRequiredFields();
  const { dom, state } = app;
    validationState = app.state;

  const form = dom.form;
  form.querySelectorAll("input, select, textarea").forEach(function (field) {
    field.addEventListener("input", function () {
      if (field.type === "file" || !state.validationStarted) {
        return;
      }

      if (field.checkValidity()) {
        clearFeedback(field);
      } else {
        showFeedback(field, field.validationMessage);
      }
    });

    field.addEventListener("change", function () {
      if (!state.validationStarted) {
        return;
      }

      /* ---------- File Inputs ---------- */

      if (field.type === "file") {
        if (!field.checkValidity()) {
          showFeedback(field, field.validationMessage);
        } else {
          validateFileCount(field);
        }

        return;
      }

      /* ---------- Everything Else ---------- */

      if (field.checkValidity()) {
        clearFeedback(field);
      } else {
        showFeedback(field, field.validationMessage);
      }
    });
  });
}

/* =====================================================
   Helpers
===================================================== */

function validateFileCount(field) {
  if (field.files.length > MAX_IMAGES) {
    showFeedback(field, `You may upload a maximum of ${MAX_IMAGES} images.`);

    return false;
  }

  clearFeedback(field);

  return true;
}
function markRequiredFields(){
    document.querySelectorAll("[required]").forEach(function (field) {
    const label = document.querySelector(`label[for="${field.id}"]`);

    if (label) {
      label.classList.add("required");
    }
  });

}

