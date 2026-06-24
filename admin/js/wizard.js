            document.addEventListener('DOMContentLoaded', function () {
                // "Next" buttons: each carries data-next="#stepX-tab" pointing
                // at the nav-link <a> id of the tab to activate.
                document.querySelectorAll('.btn-wizard-next').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        var targetTabSelector = btn.getAttribute('data-next');
                        var targetTabEl = document.querySelector(targetTabSelector);
                        if (targetTabEl) {
                            var tab = bootstrap.Tab.getOrCreateInstance(targetTabEl);
                            tab.show();
                        }
                    });
                });

                // "Previous" buttons: each carries data-prev="#stepX-tab".
                document.querySelectorAll('.btn-wizard-prev').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        var targetTabSelector = btn.getAttribute('data-prev');
                        var targetTabEl = document.querySelector(targetTabSelector);
                        if (targetTabEl) {
                            var tab = bootstrap.Tab.getOrCreateInstance(targetTabEl);
                            tab.show();
                        }
                    });
                });

                // Whenever a wizard tab becomes active, scroll the card body
                // back to the top so users on long steps land at the heading.
                document.querySelectorAll('#productWizardTab [data-bs-toggle="tab"]').forEach(function (tabEl) {
                    tabEl.addEventListener('shown.bs.tab', function () {
                        tabEl.closest('.card').scrollIntoView({ behavior: 'smooth', block: 'start' });
                    });
                });

});