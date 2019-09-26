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
        this.source = message.source;

        this.writeLogMessage(message.source.action.id, this.getPlayer(this.source.player.id));

        switch (message.source.action.id) {
            case 'deal':
                this.deal();
                break;
            case 'reorder_cards':
                this.reorderCards();
                break;
            case 'draw_from_undrawn':
                this.drawFromUndrawn();
                break;
            case 'draw_from_discarded':
                this.drawFromDiscarded();
                break;
            case 'draw_from_discarded_and_meld':
                this.drawFromDiscardedAndMeld();
                break;
            case 'meld_cards':
                this.meldCards();
                break;
            case 'extend_meld':
                this.extendMeld();
                break;
            case 'discard_end_turn':
                this.discardEndTurn();
                break;
            case 'discard_end_round':
                this.discardEndRound();
                break;
            case 'discard_end_game':
                this.discardEndGame();
                break;
            default:
                location.reload();
        }
    }

    deal() {
        const cards = this.getCurrentPlayer().hand.cards;
        this.game.initializeHand(cards);
    }

    reorderCards() {
        if (this.playerIsCausing()) {
        }
    }

    drawFromUndrawn() {
        if (this.playerIsCausing()) {
            const player_cards = this.getCurrentPlayer().hand.cards;
            const cards = this.game.getHand().getHandContainer().getCards(true);
            const cards_added = DiffCalculator.cardDiff(player_cards, cards);
            this.game.getHand().addCards(cards_added);
            ResetUndrawnCard.resetCard();
        }
    }

    drawFromDiscarded() {
    }

    drawFromDiscardedAndMeld() {
    }

    meldCards() {
    }

    extendMeld() {
    }

    discardEndTurn() {
    }

    discardEndRound() {
    }

    discardEndGame() {
    }

    writeLogMessage(message, player = null, add_time = true) {
        const log = $('#log');

        const now = new Date();
        const time = add_time ? `${this.padNumber(now.getHours(),2)}:${this.padNumber(now.getMinutes(),2)}:${this.padNumber(now.getSeconds(),2)} ` : '';
        player = player ? player.name : '';

        message = `${time} ${player}: ${message}<br/>`;
        log.html(log.html() + message);

        log.scrollTop(log.prop("scrollHeight"));
    }

    padNumber(num, size) {
        let s = num + "";
        while (s.length < size) s = "0" + s;
        return s;
    }

    playerIsCausing(player_id = null) {
        if (null === player_id) {
            player_id = this.getCurrentPlayer().id;
        }

        return player_id === this.source.player.id;
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
