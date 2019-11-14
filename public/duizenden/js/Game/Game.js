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
        Melds.createMelds(this.player_id, this.z_fighter, this.melds, $('#melds-local'), 113, 179, this.card_separation_meld, this.path_extend_meld, 0);

        this.initializeHand();
        this.initializeOpponentHands();
        this.initializeOpponentMelds();
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
        const max_angle = 20;
        let count = this.opponent_cards.length;
        let angle_per_player = max_angle / (count - 1);
        let start_angle = count === 1 ? 0 : -(max_angle / 2);

        const window_height = window.innerHeight;
        const window_width = window.innerWidth;
        const opponent_pane_width = Math.floor(100 / count);
        const opponent_pane_width_px = Math.floor(window_width / count);
        const opponent_pane_height = Math.floor(window_height * .4);
        const cards_offset_x = -(Math.floor((window_width * (opponent_pane_width / 100)) / 2));
        const cards_offset_y = 0;

        let i = 1;

        const a = Math.floor(window_width / 2);
        const b = Math.floor(window_height / 2);

        const center_point = {x: a, y: b};


        let points_x = [];

        const start = count % 2 === 0 ? Math.floor(opponent_pane_width_px / 2) : 0;

        let new_coords = [];

        for (const i of Array(Math.ceil(count / 2)).keys()) {
            const x = start + (i * opponent_pane_width_px);
            let y = b * Math.sqrt(1 - Math.pow(x / a, 2));
            y = Math.round(-y + cards_offset_y + Math.floor(b));
            const x_left = -x + a;
            const x_right = x + a;

            const point_left = {x: x_left, y: y};
            const angle_left = Math.round(MathHelper.getAngle(point_left, center_point));
            new_coords.splice(0, 0, {coord: point_left, angle: angle_left});

            if (x_right !== x_left) {
                const point_right = {x: x_right, y: y};
                const angle_right = Math.round(MathHelper.getAngle(point_right, center_point));
                new_coords.push({coord: point_right, angle: angle_right});
            }
        }

        const opponent_hands = $('#hands-opponents');
        const opponent_melds = $('#melds-opponents');
        // body.append($(`<div style="position: absolute; top: ${center_point.y}px; left: ${center_point.x}px; width: 20px; height: 20px; background: red; border-radius: 50%"></div>`));
        //
        // for (const data of new_coords) {
        //     body.append($(`<div style="position: absolute; top: ${data.coord.y}px; left: ${data.coord.x}px; width: 2px; height: 2px; background: yellow; border-radius: 50%"></div>`));
        // }
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
            const hand = new OpponentHand(this.z_fighter, cards, hand_container, 76, 120, .2, 0, -Math.round(opponent_pane_width_px / 2), 0);
            hand.initialize();

            opponent_hands.append(hand_pane);
            opponent_melds.append(melds_pane);
        }
    }

    initializeOpponentMelds() {
        for (const [i, opponent] of this.opponent_cards.entries()) {
            let melds_container = $(`.melds-opponent[data-player-id='${opponent.id}']`);

            Melds.createMelds(
                opponent.id,
                this.z_fighter,
                this.opponent_cards[i].melds.reverse(),
                melds_container,
                this.card_width_meld,
                this.card_height_meld,
                this.card_separation_meld,
                this.path_extend_meld,
                0);
        }
    }
}