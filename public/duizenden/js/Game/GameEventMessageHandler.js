class GameEventMessageHandler {
    constructor(game_id, player_id, extend_meld_url, card_separation_melds) {
        this.game_id = game_id;
        this.player_id = player_id;
        this.game = null;
        this.extend_meld_url = extend_meld_url;
        this.card_separation_melds = card_separation_melds;
    }

    setGame(game) {
        this.game = game;
    }

    handle(e) {
        const message = JSON.parse(e.data);
        console.log(message);

        const state = new State(message, this.player_id);

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
            case 'invalid_first_meld':
                this.invalidFirstMeld(state);
                break;
            default:
                alert(`Unknown action '${state.getSourceActionId()}'.`);
        }
    }

    deal(state) {
        Melds.removeMelds();
        this.game.initializeHand(this.getLocalPlayer(state).hand.cards);
        this.game.setOpponentCards(state.getPlayersExcept(this.player_id));
        this.game.initializeOpponentHands();

        UpdateCurrentPlayer.setActivePlayer(state.getCurrentPlayerId());
        UndrawnCard.updateColor(state.getUndrawnPoolColor());
        AllowedActions.update(state.getAllowedActions());

        this.manageDealButton(state);
        this.manageMeldButton(state);
        this.manageDraggableUndrawnCard(state);
        this.manageDiscardedCard(state);
    }

    reorderCards(state) {
        if (!state.isSourcePlayerCurrentPlayer()) {
            this.game.setOpponentCards(state.getPlayersExcept(this.player_id));
            this.game.initializeOpponentHands();
        }
    }

    drawFromUndrawn(state) {
        if (state.isCurrentPlayer(this.player_id)) {
            this.addExtraCardToLocalHand(state);
            UndrawnCard.resetCard();
        } else {
            this.game.setOpponentCards(state.getPlayersExcept(this.player_id));
            this.game.initializeOpponentHands();
        }

        AllowedActions.update(state.getAllowedActions());
        this.manageMeldButton(state);
        this.manageDraggableUndrawnCard(state);
        this.manageDraggableDiscardedCard(state);
    }

    drawFromDiscarded(state) {
        if (state.isCurrentPlayer(this.player_id)) {
            this.addExtraCardToLocalHand(state);
        } else {
            this.game.setOpponentCards(state.getPlayersExcept(this.player_id));
            this.game.initializeOpponentHands();
        }

        AllowedActions.update(state.getAllowedActions());
        this.manageMeldButton(state);
        this.manageDraggableUndrawnCard(state);
        this.manageDiscardedCard(state);
    }

    addExtraCardToLocalHand(state) {
        const player_cards = this.getLocalPlayer(state).hand.cards;
        const cards = this.game.getHand().getHandContainer().getCards(true);
        const cards_added = DiffCalculator.cardDiff(player_cards, cards);
        this.game.getHand().addCards(cards_added, state.getExtra('target'));
        const card = this.game.getHand().getCardElementAt(state.getExtra('target') + 1);
        this.outlineAddedCard(card);
    }

    drawFromDiscardedAndMeld(state) {
        this.meldCards(state);
        this.drawFromDiscarded(state);
    }

    meldCards(state) {
        if (state.isSourcePlayerCurrentPlayer()) {
            Melds.createMeld(state.getSourcePlayerId(), new ZFighter(1), state.getExtra('cards_melted'), $('#melds-local'), 113, 179, this.card_separation_melds, this.extend_meld_url, 0, state.getExtra('meld_id') + 1);
            this.game.getHand().removeCards(state.getExtra('cards_melted'));
        } else {
            this.game.setOpponentCards(state.getPlayersExcept(this.player_id));
            this.game.initializeOpponentHands();
            Melds.createMeld(state.getSourcePlayerId(), new ZFighter(1), state.getExtra('cards_melted'), $(`.melds-opponent[data-player-id='${state.getSourcePlayerId()}']`), 113, 179, this.card_separation_melds, null, 0, state.getExtra('meld_id') + 1, true);
        }
    }

    extendMeld(state) {
        const meld = state.getPlayerMeld(state.getSourcePlayerId(), state.getExtra('meld_id'));
        if (state.isSourcePlayerCurrentPlayer()) {
            Melds.extendMeld(state.getSourcePlayerId(), state.getExtra('meld_id') + 1, meld.cards.cards, 113, 179, this.card_separation_melds, this.extend_meld_url, 0);
            this.game.getHand().removeCard(state.getExtra('card_melted'));
        } else {
            Melds.extendMeld(state.getSourcePlayerId(), state.getExtra('meld_id') + 1, meld.cards.cards, 113, 179, this.card_separation_melds, null, 180);
            this.game.setOpponentCards(state.getPlayersExcept(this.player_id));
            this.game.initializeOpponentHands();
        }
    }

    outlineAddedCard(card) {
        card.addClass('add-in add-out');
        setTimeout(() => { card.removeClass('add-in'); }, 1000);
        setTimeout(() => { card.removeClass('add-out'); }, 2500);
    }

    discardEndTurn(state) {
        if (!state.isSourcePlayerCurrentPlayer()) {
            this.game.setOpponentCards(state.getPlayersExcept(this.player_id));
            this.game.initializeOpponentHands();
            this.manageDiscardedCard(state);
        } else {
            this.manageDraggableDiscardedCard(state);
        }

        UpdateCurrentPlayer.setActivePlayer(state.getCurrentPlayerId());
        AllowedActions.update(state.getAllowedActions());

        this.manageMeldButton(state);
        this.manageDraggableUndrawnCard(state);
    }

    discardEndRound(state) {
        this.game.setOpponentCards(state.getPlayersExcept(this.player_id));
        this.game.initializeOpponentHands();

        this.manageDealButton(state);
        this.manageMeldButton(state);
        this.manageDraggableUndrawnCard(state);
        this.manageDiscardedCard(state);
        this.manageScore(state);

        const same_player = state.getCurrentPlayerId() === state.getSourcePlayerId();
        const finisher = state.isSourcePlayerCurrentPlayer() ? 'You' : state.getSourcePlayer().name;
        const next_dealer = same_player ? ` and ${state.isLocalPlayerCurrentPlayer() ? 'are' : 'is'}` : `. ${state.isLocalPlayerCurrentPlayer() ?  'You are' : state.getCurrentPlayer().name} is`;
        const message = `${finisher} finished the round${next_dealer} the next dealer.`;

        this.writeLogMessage(message);
        alert(message);
    }

    discardEndGame(state) {
        this.game.setOpponentCards(state.getPlayersExcept(this.player_id));
        this.game.initializeOpponentHands();

        this.manageDealButton(state);
        this.manageMeldButton(state);
        this.manageRestartButton(state);
        this.manageDraggableUndrawnCard(state);
        this.manageDiscardedCard(state);
        this.manageScore(state);

        const finisher = state.isSourcePlayerCurrentPlayer() ? 'You' : state.getSourcePlayer().name;
        const score = state.getTotalPlayerScore(state.getSourcePlayerId());
        const message = `${finisher} finished the game with ${score} points.`;

        this.writeLogMessage(message);
        alert(message);
    }

    manageScore(state) {
        Score.update(state.getPlayers(), state.getPastRoundsScore());
    }

    manageDealButton(state) {
        if (state.isActionAllowed('deal') && state.isLocalPlayerCurrentPlayer()) {
            DealButton.show();
        } else {
            DealButton.hide();
        }
    }

    manageMeldButton(state) {
        if (state.isActionAllowed('meld_cards') && state.isLocalPlayerCurrentPlayer()) {
            MeldButton.show();
        } else {
            MeldButton.hide();
        }
    }

    manageDraggableUndrawnCard(state) {
        if (state.isActionAllowed('draw_from_undrawn') && state.isLocalPlayerCurrentPlayer()) {
            UndrawnCard.enableDraggable();
        } else {
            UndrawnCard.disableDraggable();
        }
    }
    manageRestartButton(state) {
        if (state.isActionAllowed('restart_game')) {
            RestartButton.show();
        } else {
            RestartButton.hide();
        }
    }

    manageDiscardedCard(state) {
        const card = state.getDiscardedPoolTopCard();

        if (null == card) {
            DiscardedCard.removeCard();
        } else {
            DiscardedCard.resetCard(card, state.canDrawnFromDiscardedPool());
        }

        this.manageDraggableDiscardedCard(state);
    }

    manageDraggableDiscardedCard(state) {
        if (state.isActionAllowed('draw_from_discarded') && state.isLocalPlayerCurrentPlayer()) {
            DiscardedCard.enableDraggable();
        } else {
            DiscardedCard.disableDraggable();
        }
    }

    invalidFirstMeld(state) {
        const cards = Melds.removeMelds(state.getSourcePlayerId());

        if (state.isCurrentPlayer(this.player_id)) {
            this.game.getHand().addCards(cards)
        } else {
            this.game.setOpponentCards(state.getPlayersExcept(this.player_id));
            this.game.initializeOpponentHands();
        }

        this.manageDraggableUndrawnCard(state);
        this.manageDiscardedCard(state);
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
