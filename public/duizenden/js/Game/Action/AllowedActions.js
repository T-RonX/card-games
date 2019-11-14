class AllowedActions {
    static update(actions) {
        const div = $('#allowed-actions');
        let text = '';

        if (actions.length) {
            for (const action of actions) {
                text = text + action.id + '<br/>';
            }
        } else {
            text = 'No actions available<br/>'
        }

        div.html(text);
    }
}