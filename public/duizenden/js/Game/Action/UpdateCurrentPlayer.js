class UpdateCurrentPlayer {
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