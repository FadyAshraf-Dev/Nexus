export default class Ajax {
  static timeout = 15000;

  static csrfToken;

  static get(url) {
    return this.request("GET", url);
  }

  static post(url, data) {
    return this.request("POST", url, data);
  }

  static put(url, data) {
    return this.request("PUT", url, data);
  }

  static patch(url, data) {
    return this.request("PATCH", url, data);
  }

  static delete(url) {
    return this.request("DELETE", url);
  }
  static async request(method, url, data = null) {
    const controller = new AbortController();

    const timeout = setTimeout(() => controller.abort(), this.timeout);

    const hasBody = data !== null;

    const isFormData = data instanceof FormData;

    const options = {
      method,

      headers: this.buildHeaders(hasBody, isFormData),

      signal: controller.signal,
    };

    if (hasBody) {
      options.body = isFormData ? data : JSON.stringify(data);
    }

    try {
      const response = await fetch(url, options);

      clearTimeout(timeout);

      return await this.handleResponse(response);
    } catch (error) {
      clearTimeout(timeout);

      if (error.name === "AbortError") {
        throw new Error("Request timed out.");
      }

      throw error;
    }
  }
  static buildHeaders(hasBody = false, isFormData = false) {
    const headers = {
      Accept: "application/json",

      "X-Requested-With": "XMLHttpRequest",

      "X-CSRF-TOKEN": this.getCsrfToken(),
    };

    // Content-Type is deliberately omitted for FormData bodies: the
    // browser sets it itself, including the multipart boundary, and
    // setting it manually here would break the upload.
    if (hasBody && !isFormData) {
      headers["Content-Type"] = "application/json";
    }

    return headers;
  }
  static getCsrfToken() {
    if (!this.csrfToken) {
      const meta = document.querySelector('meta[name="csrf-token"]');

      if (!meta) {
        throw new Error("Missing CSRF meta tag.");
      }

      this.csrfToken = meta.content;
    }

    return this.csrfToken;
  }
  static async handleResponse(response) {
    let payload = null;

    const raw = await response.text();

    try {
      payload = JSON.parse(raw);
    } catch {
      console.error(raw);

      throw new Error("Server did not return valid JSON.");
    }
    if (!response.ok) {
      const error = new Error(payload.message ?? "Request failed.");

      // Non-2xx responses can still carry a useful payload beyond
      // .message - e.g. { errors: { field: "..." } } from validation
      // failures. Attach it so callers that need it can read
      // error.payload; callers that only read error.message are
      // unaffected.
      error.payload = payload;

      throw error;
    }

    return payload;
  }
}
