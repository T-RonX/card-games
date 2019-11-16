class Game {
    constructor(player_id, z_fighter, event_handler, hand, melds, opponent_cards, card_width_meld, card_height_meld, card_separation_meld, path_extend_meld) {
        this.event_handler = event_handler;
        this.z_fighter = z_fighter;
        this.hand = hand;
        this.melds = melds;
        this.opponent_cards = opponent_cards;
        this.opponent_hands = [];
        this.card_width_meld = card_width_meld;
        this.card_height_meld = card_height_meld;
        this.card_separation_meld = card_separation_meld;
        this.path_extend_meld = path_extend_meld;
        this.player_id = player_id;
        this.all_melds = [];
    }

    getAllMelds() {
        return this.all_melds;
    }

    setOpponentCards(opponent) {
        this.opponent_cards = opponent;
    }

    static create(
        player_id,
        connection,
        event_handler,
        hand_container_selector,
        hand_cards,
        opponent_cards,
        melds,
        meld_container_card_selector,
        path_draw_from_discarded,
        path_draw_from_undrawn,
        card_width,
        card_height,
        card_width_meld,
        card_height_meld,
        card_separation,
        card_separation_meld,
        path_extend_meld) {

        const z_fighter = new ZFighter(100);
        const hand = new Hand(z_fighter, hand_container_selector, hand_cards, path_draw_from_discarded, path_draw_from_undrawn, card_width, card_height, card_separation);

        let game = new Game(player_id, z_fighter, event_handler, hand, melds, opponent_cards, card_width_meld, card_height_meld, card_separation_meld, path_extend_meld);
        event_handler.setGame(game);

        return game;
    }

    initialize() {
        UndrawnCard.resetCard();
        this.initializeHand();
        this.initializeLocalMelds();
        this.initializeOpponentHands();
        this.initializeOpponentMelds();
    }

    initializeLocalMelds() {
        const container = $('#melds-local');
        let unique = 0;

        for (const meld of this.melds) {
            const m = Melds.createMeld(this.player_id, this.z_fighter, meld.cards.cards, container, 113, 179, this.card_separation_meld, this.path_extend_meld, 0, ++unique);
            this.all_melds.push(m);
        }
    }

    initializeHand(cards = null) {
        if (cards) {
            this.hand.getHandContainer().setCards(cards);
            this.hand.getHandContainer().createCards();
        }

        this.hand.initialize();
    }

    getHand() {
        return this.hand;
    }

    initializeOpponentHands() {
        this.opponent_hands = [];

        for (const [i, opponent] of this.opponent_cards.entries()) {
            let hand_pane = $(`.hand-opponent[data-player-id='${opponent.id}']`);
            let melds_pane = $(`.melds-opponent[data-player-id='${opponent.id}']`);
            let hand_container;

            if (!hand_pane.length) {
                hand_container = $('<div class="hand-opponent-container"></div>');
                hand_pane = $(`<div class="hand-opponent" data-player-id="${opponent.id}"></div>`);
                hand_pane.append(hand_container);
                melds_pane = $(`<div class="melds-opponent" data-player-id="${opponent.id}"></div>`);
            }  else {
                hand_container = $(`.hand-opponent[data-player-id='${opponent.id}'] .hand-opponent-container`)
            }

            const cards = CardHelper.cardIdsHaveValues(opponent.hand.cards) ? opponent.hand.cards : opponent.hand.cards.reverse();
            const hand = new OpponentHand(this.z_fighter, cards, hand_container, 76, 120, .2, 0);
            hand.initialize();
            this.opponent_hands.push(hand);

            $('#hands-opponents').append(hand_pane);
            $('#melds-opponents').append(melds_pane);
        }
    }

    initializeOpponentMelds() {
        for (const [i, opponent] of this.opponent_cards.entries()) {
            let melds_container = $(`.melds-opponent[data-player-id='${opponent.id}']`);

            let u = 0;
            for (const meld of this.opponent_cards[i].melds.reverse()) {
                const m = Melds.createMeld(
                    opponent.id,
                    this.z_fighter,
                    meld.cards.cards,
                    melds_container,
                    this.card_width_meld,
                    this.card_height_meld,
                    this.card_separation_meld,
                    this.path_extend_meld,
                    0,
                    ++u
                );

                this.all_melds.push(m);
            }
        }
    }

    getOpponentHands() {
        return this.opponent_hands;
    }
}