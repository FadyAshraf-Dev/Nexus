import Template from "../core/Template.js";

export default class UserManagementTemplate extends Template {

  table(users) {
    if (!users.length) {
      return this.emptyState();
    }

    return users.map((user) => this.row(user)).join("");
  }

  row(user) {
    return `
        <tr>
            <td>${this.escape(user.username)}</td>

            <td>${this.escape(user.email)}</td>

            <td>${this.roleBadge(user.role_permission)}</td>

            <td class="text-end">${this.actions(user)}</td>
        </tr>
    `;
  }

  roleBadge(permission) {
    return this.badge(
      permission,
      "bg-secondary",
    );
  }

  actions(user) {
    return `
            <div class="d-inline-flex">

                ${this.button({
                  icon: "trash-2",
                  action: "delete",
                  id: user.id,
                  title: "Delete",
                  className: "btn-datatable btn-icon btn-transparent-dark me-2",
                })}

            </div>
        `;
  }

  emptyState() {
    return `
            <tr>

                <td colspan="4" class="text-center py-5">

                    <i
                        data-feather="users"
                        style="width:48px;height:48px;"
                        class="mb-3"
                    ></i>

                    <h5>No users found.</h5>

                </td>

            </tr>
        `;
  }
}