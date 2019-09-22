class GameEventMessageHandler {
    constructor(game_id, player_id) {
        this.game_id = game_id;
        this.player_id = player_id;
        this.game = null;
    }

    setGame(game) {
        this.game = game;
    }

    handle(e) {
        const message = JSON.parse(e.data);

        if (message.game_id !== this.game_id) {
            return;
        }

        if (message.status !== 'ok') {
            alert(message.status);
        }

        this.state = message.game_state;

        switch (message.source.action.id) {
            case 'deal':
                this.deal();
                break;
            default:
                location.reload();
        }
    }

    deal() {
        const cards = this.getCurrentPlayer().hand.cards;
        this.game.initializeHand(cards);
    }

    getCurrentPlayer() {
        return this.getPlayer(this.player_id);
    }

    getPlayer(id) {
        for (const player of this.state.players) {
            if (player.id === id) {
                return player;
            }
        }
    }
}
