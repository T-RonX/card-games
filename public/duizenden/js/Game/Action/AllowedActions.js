class AllowedActions {
    static update(actions) {
        const div = $('#allowed-actions');
        let text = '';

        if (actions.length) {
            for (const action of actions) {
                text += `<div class="allowed-action"><span class="allowed-action-bullet">●</span>&nbsp;<span class="allowed-action-text">${action.description}</span></div>`;
            }
        } else {
            text = 'No actions available'
        }

        div.html(text);
    }
}