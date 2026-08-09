import Template from "../core/Template.js";

export default class CategoryManagementTemplate extends Template {

  table(categories) {
    if (!categories.length) {
      return this.emptyState();
    }

    return categories.map((category) => this.row(category)).join("");
  }

  row(category) {
    return `
        <tr>
            <td>${this.escape(category.category_name)}</td>

            <td>${this.escape(category.slug)}</td>

            <td class="text-end">${this.actions(category)}</td>
        </tr>
    `;
  }

  actions(category) {
    return `
            <div class="d-inline-flex">

                ${this.button({
                  icon: "trash-2",
                  action: "delete",
                  id: category.id,
                  title: "Delete",
                  className: "btn-datatable btn-icon btn-transparent-dark me-2",
                })}

            </div>
        `;
  }

  emptyState() {
    return `
            <tr>

                <td colspan="3" class="text-center py-5">

                    <i
                        data-feather="folder"
                        style="width:48px;height:48px;"
                        class="mb-3"
                    ></i>

                    <h5>No categories found.</h5>

                </td>

            </tr>
        `;
  }
}