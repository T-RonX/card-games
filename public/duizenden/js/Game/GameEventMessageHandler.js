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
        const cards = this.getLocalPlayer(state).hand.cards;
        this.game.initializeHand(cards);
        this.game.setOpponentCards(state.getPlayersExcept(this.player_id));
        this.game.initializeOpponentHands();

        DiscardedCard.createCard(state.getDiscardedPoolTopCard(), this.canDrawnFromDiscardedPool(state));
        DiscardedCard.resetCard();

        UpdateCurrentPlayer.setActivePlayer(state.getCurrentPlayerId());
        DealButton.hide();
        UndrawnCard.updateColor(state.getUndrawnPoolColor());
    }

    reorderCards(state) {
        if (state.getSourcePlayerId(this.player_id)) {
        }
    }

    drawFromUndrawn(state) {
        if (state.getSourcePlayerId(this.player_id)) {
            const player_cards = this.getLocalPlayer(state).hand.cards;
            const cards = this.game.getHand().getHandContainer().getCards(true);
            const cards_added = DiffCalculator.cardDiff(player_cards, cards);
            this.game.getHand().addCards(cards_added);
            UndrawnCard.resetCard();
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

    getLocalPlayer(state) {
        return state.getPlayer(this.player_id);
    }

    canDrawnFromDiscardedPool(state) {
        if (state.isCurrentPlayer(this.player_id)) {
            const is_draw_allowed = state.isActionAllowed('draw_from_discarded');
            const has_melds = state.hadPlayerMelds(this.player_id);
            const discarded_pool_is_first_card = state.isDiscardedPoolFirstCard();
            const has_minimum_score = state.getPlayerMeldScore(this.player_id) >= 30;

            return is_draw_allowed && ((!(has_melds && discarded_pool_is_first_card)) || has_minimum_score);
        }

        return false;
    }
}
