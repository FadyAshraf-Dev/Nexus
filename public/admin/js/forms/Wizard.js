export default class Wizard {
  constructor(dom) {
    this.dom = dom;
  }

  initialize() {
    this.initializeScrollBehavior();
  }

  next(button) {
    this.show(button.dataset.next);
  }

  previous(button) {
    this.show(button.dataset.prev);
  }

  show(selector) {
    const tab = this.dom.get(selector);

    if (!tab) {
      return;
    }

    bootstrap.Tab.getOrCreateInstance(tab).show();
  }
  initializeScrollBehavior() {
    this.dom
      .getAll('#productWizardTab [data-bs-toggle="tab"]')
      .forEach((tab) => {
        tab.addEventListener("shown.bs.tab", () => {
          tab.closest(".card")?.scrollIntoView({
            behavior: "smooth",

            block: "start",
          });
        });
      });
  }
}
