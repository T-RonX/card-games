class UpdateCurrentPlayer {
    static setCurrentPlayer(player_name, is_local_player) {
        const e = $('#actions-current-player-text');
        if (is_local_player) {
            e.text('Your turn');
        } else {
            e.text(`${player_name}'s turn`);
        }
    }

    static setActivePlayer(id) {
        $('span.player_name').each((i, elem) => {
            elem = $(elem);
            if (id === elem.data('player-id')) {
                elem.addClass('active')
            } else {
                elem.removeClass('active')
            }
        })
    }
}