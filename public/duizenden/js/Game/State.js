class State {
    constructor(state, player_id) {
        this.data = state;
        this.player_id = player_id;
    }

    getGameId() {
        return this.data.game_id;
    }

    isGameId(id) {
        return id === this.getGameId();
    }

    getStatus() {
        return this.data.status;
    }

    isStatus(status) {
        return status === this.getStatus();
    }

    getSource() {
        return this.data.source;
    }

    getSourcePlayer() {
        return this.getSource().player;
    }

    getSourceAction() {
        return this.getSource().action;
    }

    getSourcePlayerId() {
        return this.getSourcePlayer().id;
    }

    isSourcePlayerId(id) {
        return id === this.getSourcePlayerId();
    }

    getSourceActionId() {
        return this.getSourceAction().id;
    }

    getUndrawnPool() {
        return this.data.game_state.undrawn_pool.cards;
    }

    getUndrawnPoolColor() {
        return this.getUndrawnPool()[this.getUndrawnPool().length - 1];
    }

    getPlayers() {
        return this.data.game_state.players;
    }

    getPlayersExcept(id) {
        let players = [];

        for (const player of this.getPlayers()) {
            if (id !== player.id) {
                players.push(player);
            }
        }

        return players;
    }

    getDiscardedPool() {
        return this.data.game_state.discarded_pool;
    }

    getAllowedActions() {
        return this.data.game_state.allowed_actions;
    }

    getPlayer(id) {
        for (const player of this.getPlayers()) {
            if (player.id === id) {
                return player;
            }
        }
    }

    getCurrentPlayerId() {
        return this.data.game_state.current_player.id;
    }

    getCurrentPlayer() {
        return this.getPlayer(this.getCurrentPlayerId());
    }

    isCurrentPlayer(id) {
        return id === this.getCurrentPlayerId();
    }

    getDiscardedPoolTopCard() {
        return this.getDiscardedPool().top_card;
    }

    isActionAllowed(id) {
        for (const action of this.getAllowedActions()) {
            if (id === action.id) {
                return true;
            }
        }

        return false;
    }

    getPlayerMelds(id) {
        return this.getPlayer(id).melds;
    }

    hadPlayerMelds(id) {
        return this.getPlayerMelds(id).length > 0;
    }

    isDiscardedPoolFirstCard() {
        return this.getDiscardedPool().is_first_card;
    }

    getPlayerScore(id) {
        return this.getPlayer(id).score;
    }

    getPlayerMeldScore(id) {
        return this.getPlayerScore(id).meld
    }

    canDrawnFromDiscardedPool() {
        if (this.isCurrentPlayer(this.player_id)) {
            const is_draw_allowed = this.isActionAllowed('draw_from_discarded');
            const has_melds = this.hadPlayerMelds(this.player_id);
            const discarded_pool_is_first_card = this.isDiscardedPoolFirstCard();
            const has_minimum_score = this.getPlayerMeldScore(this.player_id) >= 30;

            return is_draw_allowed && ((!(has_melds && discarded_pool_is_first_card)) || has_minimum_score);
        }

        return false;
    }
}