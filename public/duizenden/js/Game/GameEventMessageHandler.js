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
        console.log(message);

        const state = new State(message);

        if (!state.isGameId(this.game_id)) {
            return;
        }

        if (!state.isStatus('ok')) {
            alert(state.getStatus());
        }

        this.writeLogMessage(state.getSourceActionId(), state.getPlayer(state.getSourcePlayerId()));

        switch (state.getSourceActionId()) {
            case 'deal':
                this.deal(state);
                break;
            case 'reorder_cards':
                this.reorderCards(state);
                break;
            case 'draw_from_undrawn':
                this.drawFromUndrawn(state);
                break;
            case 'draw_from_discarded':
                this.drawFromDiscarded(state);
                break;
            case 'draw_from_discarded_and_meld':
                this.drawFromDiscardedAndMeld(state);
                break;
            case 'meld_cards':
                this.meldCards(state);
                break;
            case 'extend_meld':
                this.extendMeld(state);
                break;
            case 'discard_end_turn':
                this.discardEndTurn(state);
                break;
            case 'discard_end_round':
                this.discardEndRound(state);
                break;
            case 'discard_end_game':
                this.discardEndGame(state);
                break;
            case 'undo_last_action':
                location.reload();
                break;
            default:
                alert(`Unknown action '${state.getSourceActionId()}'.`);
        }
    }

    deal(state) {
        this.game.initializeHand(this.getLocalPlayer(state).hand.cards);
        this.game.setOpponentCards(state.getPlayersExcept(this.player_id));
        this.game.initializeOpponentHands();

        DiscardedCard.resetCard(state.getDiscardedPoolTopCard(), state.canDrawnFromDiscardedPool());

        UpdateCurrentPlayer.setActivePlayer(state.getCurrentPlayerId());
        DealButton.hide();
        UndrawnCard.updateColor(state.getUndrawnPoolColor());

        AllowedActions.update(state.getAllowedActions());
    }

    reorderCards(state) {
        if (!state.isSourcePlayerId(this.player_id)) {
            this.game.setOpponentCards(state.getPlayersExcept(this.player_id));
            this.game.initializeOpponentHands();
        }
    }

    drawFromUndrawn(state) {
        if (state.isCurrentPlayer(this.player_id)) {
            const player_cards = this.getLocalPlayer(state).hand.cards;
            const cards = this.game.getHand().getHandContainer().getCards(true);
            const cards_added = DiffCalculator.cardDiff(player_cards, cards);
            this.game.getHand().addCards(cards_added);
            UndrawnCard.resetCard();
        } else {
            this.game.setOpponentCards(state.getPlayersExcept(this.player_id));
            this.game.initializeOpponentHands();
        }

        AllowedActions.update(state.getAllowedActions());
    }

    drawFromDiscarded(state) {
        const card = state.getDiscardedPoolTopCard();

        if (null == card) {
            DiscardedCard.removeCard();
        } else {
            DiscardedCard.resetCard(card, state.canDrawnFromDiscardedPool());
        }

        if (state.isCurrentPlayer(this.player_id)) {
            this.game.initializeHand(this.getLocalPlayer(state).hand.cards);
        } else {
            this.game.setOpponentCards(state.getPlayersExcept(this.player_id));
            this.game.initializeOpponentHands();
        }

        AllowedActions.update(state.getAllowedActions());
    }

    drawFromDiscardedAndMeld() {
    }

    meldCards() {
    }

    extendMeld() {
    }

    discardEndTurn(state) {
        if (!state.isSourcePlayerId(this.player_id)) {
            this.game.setOpponentCards(state.getPlayersExcept(this.player_id));
            this.game.initializeOpponentHands();

            DiscardedCard.resetCard(state.getDiscardedPoolTopCard(), state.canDrawnFromDiscardedPool());
        }

        UpdateCurrentPlayer.setActivePlayer(state.getCurrentPlayerId());
        AllowedActions.update(state.getAllowedActions());
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

    getLocalPlayer(state) {
        return state.getPlayer(this.player_id);
    }
}
