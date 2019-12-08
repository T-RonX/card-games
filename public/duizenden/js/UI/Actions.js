$(document).ready(() => {
    $('#action-buttons .action-button').click((e) => {
        const tab = $(e.currentTarget).data('tab');
        activateButton(tab);
        showTab(tab)
    });

    function showTab(tab) {
        $(`#action-tabs .tab-content:not([data-name='${tab}'])`).addClass('hidden');
        $(`#action-tabs .tab-content[data-name='${tab}']`).removeClass('hidden')
    }

    function activateButton(tab) {
        $(`#action-buttons .action-button[data-tab='${tab}']`).addClass('active');
        $(`#action-buttons .action-button:not([data-tab='${tab}'])`).removeClass('active');
    }
});
